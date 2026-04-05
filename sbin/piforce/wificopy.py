import sys

wifi = '"' + sys.argv[1] + '" "' + sys.argv[2] + '"'
clean = wifi.replace("\\", "")
with open('/boot/wifi.txt', 'w') as wififile:
    wififile.write(clean)
