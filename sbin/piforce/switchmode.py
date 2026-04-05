import sys
import subprocess
from time import sleep

if (sys.argv[1] == 'multi') or (sys.argv[1] == 'single'):
    with open('/sbin/piforce/bootfile.txt', 'w') as f:
        f.write(sys.argv[1])

if (sys.argv[1] == 'auto-off') or (sys.argv[1] == 'always-on'):
    with open('/sbin/piforce/powerfile.txt', 'w') as f:
        f.write(sys.argv[1])

if (sys.argv[1] == 'simple') or (sys.argv[1] == 'advanced'):
    with open('/sbin/piforce/menumode.txt', 'w') as f:
        f.write(sys.argv[1])

if (sys.argv[1] == 'relayon') or (sys.argv[1] == 'relayoff'):
    with open('/sbin/piforce/relaymode.txt', 'w') as f:
        f.write(sys.argv[1])

if (sys.argv[1] == 'hackon') or (sys.argv[1] == 'hackoff'):
    with open('/sbin/piforce/zeromode.txt', 'w') as f:
        f.write(sys.argv[1])

if (sys.argv[1] == 'openon') or (sys.argv[1] == 'openoff'):
    with open('/sbin/piforce/openmode.txt', 'w') as f:
        f.write(sys.argv[1])

if (sys.argv[1] == 'ffbon') or (sys.argv[1] == 'ffboff'):
    with open('/sbin/piforce/ffbmode.txt', 'w') as f:
        f.write(sys.argv[1])

if (sys.argv[1] == 'soundon') or (sys.argv[1] == 'soundoff'):
    with open('/sbin/piforce/soundmode.txt', 'w') as f:
        f.write(sys.argv[1])

if (sys.argv[1] == 'navon') or (sys.argv[1] == 'navoff'):
    with open('/sbin/piforce/navmode.txt', 'w') as f:
        f.write(sys.argv[1])

if (sys.argv[1] == 'manual') or (sys.argv[1] == 'auto'):
    with open('/sbin/piforce/emumode.txt', 'w') as f:
        f.write(sys.argv[1])

if (sys.argv[1] == 'nfcon') or (sys.argv[1] == 'nfcoff'):
    with open('/sbin/piforce/nfcmode.txt', 'w') as f:
        f.write(sys.argv[1])

if sys.argv[1] == 'LCD16':
    with open('/sbin/piforce/lcdmode.txt', 'w') as f:
        f.write('LCD16')
    subprocess.run(['sudo', 'systemctl', 'enable', 'lcd-piforce'])
    subprocess.run(['sudo', 'cp', '/boot/config.txt.lcd16', '/boot/config.txt'])
    sleep(5)
    subprocess.run(['sudo', 'shutdown', 'now'])

if sys.argv[1] == 'LCD35':
    with open('/sbin/piforce/lcdmode.txt', 'w') as f:
        f.write('LCD35')
    subprocess.run(['sudo', 'systemctl', 'disable', 'lcd-piforce'])
    subprocess.run(['sudo', 'cp', '/boot/config.txt.lcd35', '/boot/config.txt'])
    sleep(5)
    subprocess.run(['sudo', 'shutdown', 'now'])
