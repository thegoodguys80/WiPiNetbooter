import os, collections, signal, sys, subprocess, socket
from time import sleep

with open('/sbin/piforce/bootfile.txt') as f:
    bootmode = f.readline().strip()
with open('/var/www/logs/log.txt') as f:
    bootrom = f.readline().strip()
with open('/sbin/piforce/powerfile.txt') as f:
    powermode = f.readline().strip()
with open('/sbin/piforce/nfcmode.txt') as f:
    nfcmode = f.readline().strip()

if nfcmode == 'nfcon':
    cp = subprocess.Popen(['python3', '/sbin/piforce/card_emulator/nfcread.py'])

if os.path.exists('/boot/wifi.txt'):
    with open('/boot/wifi.txt') as f:
        wifi = f.readline().strip()
    if wifi:
        subprocess.run(['sudo', 'python3', '/sbin/piforce/homewifi.py', wifi])

if os.path.exists('/boot/reset.txt'):
    os.remove('/boot/reset.txt')
    subprocess.run(['sudo', 'python3', '/sbin/piforce/hotspotrestore.py'])
    subprocess.run(['sudo', 'reboot'])

with open('/sbin/piforce/relaymode.txt') as f:
    relaymode = f.readline().strip()
with open('/sbin/piforce/zeromode.txt') as f:
    zeromode = f.readline().strip()

if bootmode == 'single':
    cmd = ['sudo', 'python3', '/sbin/piforce/webforce.py', bootrom, relaymode, zeromode]
    with open('/var/www/logs/scriptlog.txt', 'w') as scriptfile:
        scriptfile.write('Last command run - ' + ' '.join(cmd))
    subprocess.run(cmd)

if powermode == 'auto-off':
    subprocess.run(['sudo', 'shutdown', '-h', '-t', '600'])
