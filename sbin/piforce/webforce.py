#!/usr/bin/env python3
"""Webforce - Main netboot orchestrator for WiPiNetbooter.

Handles ROM uploading and game launching via netdimm protocol.
Called by PHP web interface with validated parameters.
"""

import os
import signal
import sys
import subprocess
import socket
import glob
import shlex
from time import sleep

import triforcetools
import psutil

try:
    import RPi.GPIO as GPIO
    GPIO_AVAILABLE = True
except (ImportError, RuntimeError):
    # GPIO not available in Docker/non-Pi environment
    GPIO_AVAILABLE = False

def checkprocess(process):
    for proc in psutil.process_iter():
        try:
            if process.lower() in proc.name().lower():
                return True
        except (psutil.NoSuchProcess, psutil.AccessDenied, psutil.ZombieProcess):
            pass
    return False

def exists(path):
    """Check if a path exists safely."""
    try:
        os.stat(path)
    except OSError:
        return False
    return True

def validate_device_path(path):
    """Validate device path to prevent command injection."""
    if not path.startswith('/dev/'):
        raise ValueError(f"Invalid device path: {path}")
    if '..' in path or path.count('/') > 3:
        raise ValueError(f"Suspicious device path: {path}")
    return path

# Read configuration files using context managers (Python 3)
with open('/sbin/piforce/pid.txt', 'r') as f:
    lastpid = f.readline().strip()

with open('/sbin/piforce/openmode.txt', 'r') as f:
    openjvs = f.readline().strip()

with open('/sbin/piforce/bootfile.txt', 'r') as f:
    singlemode = f.readline().strip()

with open('/sbin/piforce/ffbmode.txt', 'r') as f:
    ffbmode = f.readline().strip()

with open('/sbin/piforce/emumode.txt', 'r') as f:
    emumode = f.readline().strip()

if (singlemode == 'single'):
    sleep(5)

activedimm = sys.argv[2]

# Kill previous instance if running
try:
    pid = int(lastpid)
    if pid > 0:
        os.kill(pid, signal.SIGKILL)
except (ValueError, ProcessLookupError, PermissionError):
    pass  # Process not running or invalid PID

currentpid = os.getpid()

# Write current PID to file
with open('/sbin/piforce/pid.txt', 'w') as f:
    f.write(str(currentpid))

# Start OpenJVS if enabled (SECURE: using subprocess with list arguments)
if openjvs == 'openon':
    try:
        # Kill any existing openjvs processes
        subprocess.run(['sudo', 'killall', '-9', 'openjvs'], 
                      stderr=subprocess.DEVNULL, check=False)
        
        # Validate and start new openjvs process
        device = validate_device_path(sys.argv[5])
        subprocess.Popen(['sudo', 'openjvs', device],
                        stdout=subprocess.DEVNULL,
                        stderr=subprocess.DEVNULL)
    except (IndexError, ValueError) as e:
        print(f"Error starting OpenJVS: {e}", file=sys.stderr)

# Start OpenFFB if enabled (SECURE: using subprocess with list arguments)
if ffbmode == 'ffbon':
    try:
        # Kill any existing openffb processes
        subprocess.run(['sudo', 'killall', '-9', 'openffb'],
                      stderr=subprocess.DEVNULL, check=False)
        
        # Validate and start new openffb process
        device = validate_device_path(sys.argv[6])
        subprocess.Popen(['sudo', 'openffb', '-h=0', f'-gp={device}'],
                        stdout=subprocess.DEVNULL,
                        stderr=subprocess.DEVNULL)
    except (IndexError, ValueError) as e:
        print(f"Error starting OpenFFB: {e}", file=sys.stderr)

# Log ROM and DIMM info
try:
    log_entry = f"{sys.argv[1]} {sys.argv[2]}"
    with open('/var/www/logs/log.txt', 'w') as f:
        f.write(log_entry)
except (IndexError, IOError) as e:
    print(f"Error writing log: {e}", file=sys.stderr)

print("Sending Game...")

rom_dir = '/boot/roms/'
romfile = rom_dir+sys.argv[1]

# Start cycraft for specific games (SECURE: no user input here)
if 'monster_ride' in romfile or 'cycraft' in romfile:
    try:
        subprocess.Popen(['sudo', '/usr/lib/cycraft'],
                        stdout=subprocess.DEVNULL,
                        stderr=subprocess.DEVNULL)
    except FileNotFoundError:
        print("Warning: cycraft binary not found", file=sys.stderr)

while True:
                # Relay mode - toggle GPIO for arcade board power/reset
                if sys.argv[3] == 'relayon' and GPIO_AVAILABLE:
                    try:
                        GPIO.setmode(GPIO.BOARD)
                        GPIO.setup(40, GPIO.OUT)
                        GPIO.output(40, 1)
                        sleep(0.4)
                        GPIO.output(40, 0)
                        sleep(2.0)
                    except Exception as e:
                        print(f"GPIO error: {e}", file=sys.stderr)
                
                # Connect to netdimm
                try:
                    triforcetools.connect(activedimm, 10703)
                except (socket.error, ConnectionRefusedError, OSError) as e:
                    print(f"Connection error: {e}", file=sys.stderr)
                    sleep(1)
                    continue
                # Upload ROM to netdimm
                triforcetools.HOST_SetMode(0, 1)
                triforcetools.SECURITY_SetKeycode(b"\x00" * 8)
                triforcetools.DIMM_CheckOff()
                triforcetools.DIMM_UploadFile(romfile)
                triforcetools.HOST_Restart()
                triforcetools.TIME_SetLimit(10*60*1000)

                # Auto-start card emulator for Initial D games (SECURE: validated inputs)
                if emumode == 'auto' and 'initial_d' in romfile and not checkprocess('cardemu'):
                    # Determine card emulator port
                    emuport = '/dev/ttyUSB0'  # Default
                    if exists('/dev/COM1'):
                        try:
                            compath = os.readlink('/dev/COM1')
                            devices = glob.glob('/dev/ttyUSB*')
                            for device in devices:
                                if device != compath:
                                    emuport = device
                                    break
                        except OSError:
                            pass
                    
                    # Determine Initial D version
                    if 'initial_d_3' in romfile:
                        IDMode = 'id3'
                    elif 'initial_d_2' in romfile:
                        IDMode = 'id2'
                    else:
                        IDMode = 'idas'
                    
                    # Start card emulator (SECURE: using subprocess with validated args)
                    try:
                        emuport = validate_device_path(emuport)
                        subprocess.Popen([
                            'sudo', 'python3',
                            '/sbin/piforce/card_emulator/idcardemu.py',
                            '-cp', emuport,
                            '-m', IDMode
                        ], stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
                    except (ValueError, FileNotFoundError) as e:
                        print(f"Error starting card emulator: {e}", file=sys.stderr)

                # Time hack mode - keep resetting timer
                if sys.argv[4] == 'hackon':
                    while True:
                        triforcetools.TIME_SetLimit(10*60*1000)
                        sleep(5)
                
                sleep(5)
                triforcetools.disconnect()
                
                # Cleanup GPIO if used
                if GPIO_AVAILABLE:
                    try:
                        GPIO.cleanup()
                    except Exception:
                        pass
                
                sys.exit(0)
