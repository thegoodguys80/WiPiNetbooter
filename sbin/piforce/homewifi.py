import subprocess
import sys, shutil
from time import sleep

shutil.copy('/etc/wpa_supplicant/wpa_supplicant.conf.new', '/etc/wpa_supplicant/wpa_supplicant.conf')

subprocess.run(['sudo', 'systemctl', 'disable', 'hostapd.service'], check=True)
subprocess.run(['systemctl', 'disable', 'isc-dhcp-server.service'], check=True)

shutil.copy('/etc/network/interfaces', '/etc/network/interfaces.hotspot')
shutil.copy('/etc/network/interfaces.home', '/etc/network/interfaces')

ssid = sys.argv[1]
passkey = sys.argv[2]

p1 = subprocess.Popen(
    ["wpa_passphrase", ssid, passkey],
    stdout=subprocess.PIPE
)

p2 = subprocess.Popen(
    ["sudo", "tee", "-a", "/etc/wpa_supplicant/wpa_supplicant.conf"],
    stdin=p1.stdout,
    stdout=subprocess.PIPE
)

p1.stdout.close()
p2.communicate()

with open('/sbin/piforce/wifimode.txt', 'w') as f:
    f.write('home')

with open('/boot/wifi.txt', 'w') as f:
    f.write('')

sleep(3)
subprocess.run(['sudo', 'reboot'], check=True)
