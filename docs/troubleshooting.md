# Troubleshooting

## Pi Not Found on Network

**Symptom:** Can't ping the Pi or reach the web interface.

1. Check the Pi is powered on — the green activity LED should be blinking.
2. Check the Ethernet cable is connected at both ends.
3. Check your router's DHCP table for the Pi's IP address.
4. Try `ping raspberrypi.local` — works if mDNS is available on your OS.
5. If you set a static IP, verify it is in the right subnet for your network.

---

## Game Won't Load / NetDIMM Shows Offline

**Symptom:** NetDIMM card shows "Offline" on the Dashboard, or launching a game times out.

1. Confirm the NetDIMM is powered on and connected to the same switch as the Pi.
2. From the Pi, test connectivity:
   ```bash
   ping <netdimm-ip>
   ```
3. Check the NetDIMM firmware version is 3.03+.
4. Check the IP address configured in **NetDIMMs** matches the actual board IP.
5. If the board shows "Online" but launch times out, check that `/boot/roms/<romfile>` exists on the Pi.

---

## WiFi Mode Switching Not Working

**Symptom:** Switching between Home and Hotspot mode in the Network page has no effect.

1. Check `/sbin/piforce/wifimode.txt` contains the expected value (`home` or `hotspot`).
2. Check Apache can run Python scripts as root:
   ```bash
   sudo -u www-data sudo python3 /sbin/piforce/switchmode.py
   ```
   If this fails with a sudoers error, re-run `sudo bash install.sh` to restore the sudoers entry.
3. Check `/etc/network/interfaces.home` and `/etc/network/interfaces.hotspot` exist.

---

## Card Emulator Not Detected

**Symptom:** Card emulator page shows no serial port or errors on launch.

1. Plug in the USB-Serial adapter and check it appears:
   ```bash
   ls /dev/ttyUSB*
   ```
2. Check the `www-data` user has permission to access the port:
   ```bash
   sudo usermod -a -G dialout www-data
   sudo systemctl restart apache2
   ```
3. Check the card emulator script for your game is configured for the correct port.

---

## Web Interface Shows Blank Page or PHP Errors

1. Check Apache is running:
   ```bash
   sudo systemctl status apache2
   ```
2. Check PHP error log:
   ```bash
   sudo tail -50 /var/log/apache2/error.log
   ```
3. Check file permissions:
   ```bash
   sudo chown -R www-data:www-data /var/www/html
   sudo chmod -R 755 /var/www/html
   ```

---

<!-- TODO: Add known error messages from log.txt and their fixes -->
