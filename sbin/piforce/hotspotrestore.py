import subprocess
import shutil

shutil.copy('/etc/wpa_supplicant/wpa_supplicant.conf.new', '/etc/wpa_supplicant/wpa_supplicant.conf')

subprocess.run(['sudo', 'systemctl', 'enable', 'hostapd.service'], check=True)
subprocess.run(['systemctl', 'enable', 'isc-dhcp-server.service'], check=True)

shutil.copy('/etc/network/interfaces.restore', '/etc/network/interfaces')

with open('/sbin/piforce/wifimode.txt', 'w') as f:
    f.write('hotspot')

with open('/boot/wifi.txt', 'w') as f:
    f.write('')
