# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

WiPiNetbooter is a Raspberry Pi-based netbooting solution for Sega arcade systems (Naomi, Naomi2, Chihiro, Triforce, and Atomiswave conversions). It consists of:
- Python-based netboot protocol implementation (triforcetools.py)
- PHP web interface for ROM management and system configuration
- Card reader emulator for arcade games requiring card data
- OpenJVS and OpenFFB integration for controller support
- Network configuration utilities

This is an embedded system image designed to run on Raspberry Pi hardware with a full web-based UI accessed via Chromium in kiosk mode.

## Architecture

### Core Components

**Netboot Engine** (`sbin/piforce/`)
- `triforcetools.py` - Core netboot protocol implementation for communicating with arcade netdimm boards
- `webforce.py` - Main netboot orchestrator called by PHP interface, handles ROM uploading and game launching
- `device.py` - Input device management using evdev
- `configuration.py` - Controller configuration wizard with HTML output

**Card Emulator** (`sbin/piforce/card_emulator/`)
- Emulates serial card readers for arcade games (Initial D, F-Zero AX, Mario Kart GP, Wangan Midnight)
- Game-specific card data modules: `idas_card_data.py`, `id2_card_data.py`, `id3_card_data.py`, `fzero_card_data.py`
- Main emulators: `idcardemu.py`, `fzerocardemu.py`, `mkgpcardemu.py`, `wmmtcardemu.py`
- NFC support: `nfccheck.py`, `nfcread.py`, `nfcwrite.py`, `nfcwipe.py`

**Web Interface** (`var/www/html/`)
- PHP-based UI for ROM selection, netdimm management, and system configuration
- Main pages: `gamelist.php`, `dimms.php`, `setup.php`, `menu.php`
- Card management: `cardemulator.php`, `cardmanagement.php`, `cards.php`
- Network config: `network.php`, `wifi.php`, `bluetooth.php`
- Device config: `devices.php`, `deviceconfig.php`, `openjvs.php`, `openffb.php`
- Uses CSV files (`csv/dimms.csv`, `csv/gamelist.csv`) for data storage

**System Configuration**
- `boot/config.txt` - Raspberry Pi boot configuration with LCD display profiles
- `etc/network/interfaces*` - Network configuration files (home, hotspot, restore modes)
- `etc/xdg/openbox/autostart` - Launches Chromium in kiosk mode on boot
- `root/*.sh` - Update scripts for OpenJVS, OpenFFB, and wifi firmware

### Data Flow

1. User selects game from web interface (PHP)
2. PHP calls `webforce.py` with ROM path and netdimm IP
3. `webforce.py` uses `triforcetools.py` to:
   - Connect to netdimm via socket (port 10703)
   - Set security keycode (zero key to disable decryption)
   - Upload ROM file to DIMM memory
   - Restart host system to boot game
4. For card-required games, `webforce.py` auto-launches appropriate card emulator
5. Card emulators communicate via serial (`/dev/ttyUSB*`) or NFC (`/dev/ttyACM*`)

### Python Dependencies

The codebase uses Python 2/3 hybrid with these key libraries:
- `evdev` - Input device access (controllers, touchscreens)
- `RPi.GPIO` - Raspberry Pi GPIO control (relay mode)
- `psutil` - Process management
- `socket` - Network communication with netdimm
- `nfcpy` - NFC card reader support (card emulator)
- `serial` - Serial port communication (card emulator)

## Docker Development Environment

### Quick Start with Docker

For development without Raspberry Pi hardware, use Docker:

```bash
# Build and start the container
docker-compose up -d

# Access web interface at http://localhost:8080

# View logs
docker-compose logs -f

# Access container shell
docker exec -it wipinetbooter-dev /bin/bash

# Stop container
docker-compose down
```

See [DOCKER.md](DOCKER.md) for complete Docker setup and usage instructions.

### Docker Limitations

⚠️ Docker environment is for **development and testing only**:
- Web interface testing works fully
- Python script syntax checking works
- PHP development works
- Actual netboot operations require physical hardware
- GPIO, USB-serial, and NFC devices require real Raspberry Pi

## Development Commands

### Testing Netboot Protocol

Test netboot connection to a netdimm:
```bash
python3 -c "import sys; sys.path.insert(0, '/sbin/piforce'); import triforcetools as tt; tt.connect('192.168.1.X', 10703); print(tt.NETFIRM_GetInformation()); tt.disconnect()"
```

### Running Card Emulator Standalone

Test Initial D card emulator (requires hardware serial adapter):
```bash
# For Initial D Arcade Stage
sudo python3 /sbin/piforce/card_emulator/idcardemu.py -cp /dev/ttyUSB0 -m idas

# For Initial D 2
sudo python3 /sbin/piforce/card_emulator/idcardemu.py -cp /dev/ttyUSB0 -m id2

# For Initial D 3
sudo python3 /sbin/piforce/card_emulator/idcardemu.py -cp /dev/ttyUSB0 -m id3
```

Test F-Zero AX card emulator:
```bash
sudo python3 /sbin/piforce/card_emulator/fzerocardemu.py -cp /dev/ttyUSB0
```

### Controller Configuration

Run controller mapping wizard (outputs HTML):
```bash
sudo python3 /sbin/piforce/configuration.py /dev/input/eventX
```

### Network Configuration

Switch between network modes using Python utilities:
```bash
# Set DHCP mode
sudo python3 /sbin/piforce/setdhcp.py

# Set static IP
sudo python3 /sbin/piforce/setstatic.py

# Switch to home wifi
sudo python3 /sbin/piforce/homewifi.py

# Switch to hotspot mode
sudo python3 /sbin/piforce/hotspotwifi.py
```

### Manual ROM Upload

Upload a ROM file manually (for testing):
```bash
sudo python3 /sbin/piforce/webforce.py <rom_filename> <netdimm_ip> <relay_mode> <timehack_mode> <openjvs_device> <ffb_device>
# Example:
sudo python3 /sbin/piforce/webforce.py mvsc2.bin 192.168.1.5 relayoff hackoff /dev/ttyUSB0 /dev/input/event0
```

### Updating OpenJVS

Rebuild and install OpenJVS from source:
```bash
sudo /root/update-openjvs.sh
```

### Updating OpenFFB

Rebuild and install OpenFFB from source:
```bash
sudo /root/update-openffb.sh
```

### Web Server

The web interface runs on Apache/PHP. Test PHP syntax:
```bash
php -l /var/www/html/<filename>.php
```

Restart web server (on actual Raspberry Pi):
```bash
sudo systemctl restart apache2
```

### Checking System Processes

Check if OpenJVS is running:
```bash
ps aux | grep openjvs
```

Check if card emulator is running:
```bash
ps aux | grep cardemu
```

Check if webforce is active:
```bash
cat /sbin/piforce/pid.txt
ps aux | grep webforce
```

## Important Implementation Notes

### Security Considerations

**Python 3 Migration**: The codebase is transitioning from Python 2 to Python 3. The current branch `warp-rebuild/security-and-python3` indicates active work on:
- Removing Python 2 deprecated syntax
- Addressing security vulnerabilities in dependencies
- Modernizing string handling (bytes vs str) in socket communication

**Key Security Areas**:
- `triforcetools.py` uses raw socket communication - ensure proper input validation
- PHP files write to CSV files - vulnerable to CSV injection if user input not sanitized
- Card emulators handle binary serial data - validate all packet structures
- System calls via `os.system()` in Python - should use `subprocess.run()` for safety

### File System Paths

All system paths are absolute and assume Raspberry Pi structure:
- ROMs: `/boot/roms/`
- Logs: `/var/www/logs/`, `/var/log/progress.txt`
- Config: `/sbin/piforce/*.txt` (mode files), `/etc/openjvs/`, `/boot/config.txt`
- Device mappings: `/etc/openjvs/devices/`
- Card images: `/var/www/html/cardimages/`

When modifying paths, maintain compatibility with the web interface expectations.

### Netboot Protocol Details

The `triforcetools.py` module implements the Sega NetDIMM protocol:
- All commands are sent as packed binary structs (little-endian)
- DIMM memory is uploaded in 32KB chunks (`DIMM_Upload`)
- CRC32 validation ensures ROM integrity
- Progress tracking writes to `/var/log/progress.txt` for web UI polling
- Security keycode `\x00 * 8` disables encryption (zero security pic required)

### GPIO and Hardware

- Relay mode uses GPIO pin 40 (BOARD numbering) to toggle power/reset
- OpenJVS uses RS-485 adapters on USB serial
- Card readers use either serial (`/dev/ttyUSB*`) or USB NFC (`/dev/ttyACM*`)
- Multiple LCD display profiles in `boot/config.txt.lcd16` and `boot/config.txt.lcd35`

### Web Interface Architecture

PHP interface uses simple patterns:
- Direct file I/O for CSV data (no database)
- System calls via `shell_exec()` to invoke Python scripts
- AJAX-style progress polling by reading `/var/log/progress.txt`
- All game launching goes through `webforce.py` wrapper

When modifying PHP, ensure CSV structure remains compatible with Python readers/writers.

### Card Emulator Protocol

Card emulators implement game-specific serial protocols:
- Initial D: Custom binary protocol on standard serial (38400 baud typically)
- F-Zero AX: Triforce-specific card protocol
- Mario Kart GP: Different packet structure than Initial D
- Each game has unique card data format (see `*_card_data.py` modules)

Card data is stored as Python dictionaries with player profiles, times, car stats, etc.

### Common Patterns

**Unbuffered Output**: Many Python scripts use `Unbuffered` class wrapper to flush output immediately for real-time web display.

**Process Management**: `webforce.py` kills previous instance via PID file (`/sbin/piforce/pid.txt`) to prevent multiple simultaneous ROM uploads.

**Mode Files**: System behavior controlled by single-line text files:
- `/sbin/piforce/openmode.txt` - "openon" or "openoff"
- `/sbin/piforce/ffbmode.txt` - "ffbon" or "ffboff"
- `/sbin/piforce/emumode.txt` - "auto" or "manual"
- `/sbin/piforce/bootfile.txt` - "single" or "menu"

### Testing Strategy

This is embedded system code targeting specific hardware:
- Full testing requires Raspberry Pi 3B+/4B hardware
- Netboot testing requires actual Sega arcade board with NetDIMM
- Card emulator testing requires compatible USB-serial adapter
- Controller testing requires evdev-compatible input devices

For development without hardware:
- Mock socket connections in `triforcetools.py`
- Use CSV file operations for testing data management
- Test PHP logic independently of Python backend
- Validate HTML output from Python scripts

### RULEBOOK.md Context

This repository contains `RULEBOOK.md` which defines a comprehensive development workflow for project rebuilds. **This RULEBOOK is not part of the WiPiNetbooter application** - it's external documentation for a rebuild process using AI assistants. When working on WiPiNetbooter code itself, ignore RULEBOOK.md and focus on the actual application requirements.

The QUICK_START.md similarly relates to that AI-assisted rebuild workflow, not the WiPiNetbooter application.
