#!/bin/bash
# WiPiNetbooter — Raspberry Pi Install Script
# Run on a fresh Raspberry Pi OS (Bullseye or Bookworm, 32/64-bit)
# Usage: sudo bash install.sh

set -e

# ── Must run as root ────────────────────────────────────────────────────────
if [ "$(id -u)" -ne 0 ]; then
    echo "ERROR: Run this script with sudo: sudo bash install.sh"
    exit 1
fi

REPO_DIR="$(cd "$(dirname "$0")" && pwd)"
echo ""
echo "================================================="
echo "  WiPiNetbooter — Raspberry Pi Installer"
echo "================================================="
echo "  Repo: $REPO_DIR"
echo "  OS:   $(grep PRETTY_NAME /etc/os-release 2>/dev/null | cut -d= -f2 | tr -d '\"')"
echo "================================================="
echo ""

# ── 1. System packages ───────────────────────────────────────────────────────
echo "[1/7] Installing system packages…"
apt-get update -qq
apt-get install -y \
    apache2 \
    php \
    libapache2-mod-php \
    php-cli \
    php-mbstring \
    python3 \
    python3-pip \
    python3-dev \
    python3-evdev \
    python3-psutil \
    python3-serial \
    git \
    wget \
    curl \
    sudo \
    nmap \
    fping \
    net-tools \
    iputils-ping \
    iproute2 \
    usbutils \
    wireless-tools \
    wpasupplicant \
    hostapd \
    dnsmasq \
    bluetooth \
    bluez \
    libnfc-dev \
    libnfc-bin \
    pcscd \
    pcsc-tools \
    libpcsclite-dev \
    build-essential \
    cmake
echo "  ✓ System packages installed"

# ── 2. Python packages ───────────────────────────────────────────────────────
echo "[2/7] Installing Python packages…"
pip3 install --no-cache-dir nfcpy pyserial evdev psutil shortuuid 2>/dev/null || \
pip3 install --no-cache-dir --break-system-packages nfcpy pyserial evdev psutil shortuuid
# RPi.GPIO is hardware-specific; install only if on real Pi hardware
python3 -c "import RPi.GPIO" 2>/dev/null || \
    pip3 install --no-cache-dir RPi.GPIO 2>/dev/null || \
    pip3 install --no-cache-dir --break-system-packages RPi.GPIO 2>/dev/null || \
    echo "  (RPi.GPIO not installed — app handles this gracefully)"
echo "  ✓ Python packages installed"

# ── 3. Directory structure ───────────────────────────────────────────────────
echo "[3/7] Creating directories…"
mkdir -p \
    /boot/roms \
    /boot/config \
    /boot/overlays \
    /var/www/html \
    /var/www/logs \
    /var/log/activecard \
    /var/log/printdata \
    /var/log/cardcheck \
    /sbin/piforce \
    /sbin/piforce/card_emulator \
    /etc/openjvs \
    /etc/openjvs/devices \
    /etc/xdg/openbox \
    /root/OpenJVS-Hat
echo "  ✓ Directories created"

# ── 4. Copy application files ────────────────────────────────────────────────
echo "[4/7] Copying application files…"
cp -r "$REPO_DIR/sbin/piforce/."     /sbin/piforce/
cp -r "$REPO_DIR/var/www/html/."     /var/www/html/
cp -r "$REPO_DIR/boot/."             /boot/ 2>/dev/null || true
cp -r "$REPO_DIR/etc/."              /etc/  2>/dev/null || true
cp -r "$REPO_DIR/root/."             /root/ 2>/dev/null || true
echo "  ✓ Files copied"

# ── 5. Initialise state files ────────────────────────────────────────────────
echo "[5/7] Initialising state files…"
for f in openmode ffbmode emumode bootfile powerfile relaymode zeromode nfcmode menumode soundmode navmode lcdmode wifimode; do
    [ -f /sbin/piforce/${f}.txt ] || echo "default" > /sbin/piforce/${f}.txt
done
[ -f /sbin/piforce/openmode.txt ]  || echo "openoff"   > /sbin/piforce/openmode.txt
[ -f /sbin/piforce/ffbmode.txt ]   || echo "ffboff"    > /sbin/piforce/ffbmode.txt
[ -f /sbin/piforce/emumode.txt ]   || echo "auto"      > /sbin/piforce/emumode.txt
[ -f /sbin/piforce/bootfile.txt ]  || echo "menu"      > /sbin/piforce/bootfile.txt
[ -f /sbin/piforce/powerfile.txt ] || echo "always-on" > /sbin/piforce/powerfile.txt
[ -f /sbin/piforce/relaymode.txt ] || echo "relayoff"  > /sbin/piforce/relaymode.txt
[ -f /sbin/piforce/zeromode.txt ]  || echo "hackoff"   > /sbin/piforce/zeromode.txt
[ -f /sbin/piforce/nfcmode.txt ]   || echo "nfcoff"    > /sbin/piforce/nfcmode.txt
[ -f /sbin/piforce/menumode.txt ]  || echo "simple"    > /sbin/piforce/menumode.txt
[ -f /sbin/piforce/soundmode.txt ] || echo "soundoff"  > /sbin/piforce/soundmode.txt
[ -f /sbin/piforce/navmode.txt ]   || echo "navoff"    > /sbin/piforce/navmode.txt
[ -f /sbin/piforce/lcdmode.txt ]   || echo "LCD35"     > /sbin/piforce/lcdmode.txt
[ -f /sbin/piforce/wifimode.txt ]  || echo "wifioff"   > /sbin/piforce/wifimode.txt
echo "modern" > /sbin/piforce/ui_mode.txt
echo "0"      > /sbin/piforce/pid.txt
echo "0"      > /sbin/piforce/card_emulator/pid.txt
touch /var/www/logs/log.txt
touch /var/www/logs/scriptlog.txt
touch /var/log/progress.txt

# CSV files (only create if missing — preserve existing data)
mkdir -p /var/www/html/csv
[ -f /var/www/html/csv/dimms.csv ] || \
    echo "name,ipaddress,type" > /var/www/html/csv/dimms.csv
[ -f /var/www/html/csv/gamelist.csv ] || \
    printf 'system,filename,name,manufacturer,year,category,players,genre,favourite\n' \
      > /var/www/html/csv/gamelist.csv
echo "  ✓ State files initialised"

# ── 6. Permissions ───────────────────────────────────────────────────────────
echo "[6/7] Setting permissions…"
chown -R www-data:www-data /var/www/html /var/www/logs
chmod -R 755 /var/www/html
chmod -R 755 /var/www/html/csv
chmod -R 755 /sbin/piforce
chmod 644 /var/log/progress.txt
chmod -R 755 /var/log/activecard /var/log/printdata /var/log/cardcheck
chmod +x /sbin/piforce/*.py 2>/dev/null || true
chmod +x /sbin/piforce/card_emulator/*.py 2>/dev/null || true
chmod +x /root/*.sh 2>/dev/null || true

# Allow www-data to run only the piforce scripts as root without password
grep -q 'www-data ALL=(ALL) NOPASSWD' /etc/sudoers || \
    echo 'www-data ALL=(ALL) NOPASSWD: /usr/bin/python3 /sbin/piforce/*, /usr/bin/python3 /sbin/piforce/card_emulator/*, /usr/bin/tail -n 1 /var/log/progress.txt' >> /etc/sudoers
echo "  ✓ Permissions set"

# ── 7. Apache ────────────────────────────────────────────────────────────────
echo "[7/7] Configuring Apache…"
# Enable the right PHP module (version varies by OS release)
a2enmod php8.2 2>/dev/null || a2enmod php8.1 2>/dev/null || \
    a2enmod php7.4 2>/dev/null || a2enmod php 2>/dev/null

grep -q 'ServerName localhost' /etc/apache2/apache2.conf || \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf
grep -q 'php_flag display_errors' /etc/apache2/apache2.conf || \
    echo "php_flag display_errors On" >> /etc/apache2/apache2.conf

systemctl enable apache2
systemctl restart apache2
echo "  ✓ Apache configured and started"

# ── Done ─────────────────────────────────────────────────────────────────────
PI_IP=$(hostname -I | awk '{print $1}')
echo ""
echo "================================================="
echo "  Installation complete!"
echo "================================================="
echo ""
echo "  Web UI:  http://${PI_IP}"
echo "  Also:    http://$(hostname).local"
echo ""
echo "  ROMs:    copy .bin / .bin.gz files to /boot/roms/"
echo "  Logs:    /var/www/logs/log.txt"
echo ""
echo "  To update later, re-run: sudo bash install.sh"
echo "================================================="
echo ""
