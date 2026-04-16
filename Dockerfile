# WiPiNetbooter Docker Development Environment
# Simulates the Raspberry Pi environment for dev/testing without physical hardware

FROM debian:bullseye

ENV DEBIAN_FRONTEND=noninteractive
ENV PYTHONUNBUFFERED=1

# Install system dependencies
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    python3-dev \
    apache2 \
    php \
    libapache2-mod-php \
    php-cli \
    php-mbstring \
    git \
    wget \
    curl \
    sudo \
    fping \
    usbutils \
    python3-evdev \
    python3-psutil \
    python3-serial \
    build-essential \
    cmake \
    net-tools \
    nmap \
    iputils-ping \
    iproute2 \
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
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Python packages (RPi.GPIO excluded — no hardware; app handles ImportError gracefully)
RUN pip3 install --no-cache-dir \
    nfcpy \
    pyserial \
    evdev \
    psutil \
    shortuuid

# Create directory structure
RUN mkdir -p \
    /boot/roms \
    /boot/config \
    /boot/overlays \
    /var/www/html \
    /var/www/logs \
    /var/log \
    /var/log/activecard \
    /var/log/printdata \
    /var/log/cardcheck \
    /sbin/piforce \
    /sbin/piforce/card_emulator \
    /etc/openjvs \
    /etc/openjvs/devices \
    /etc/network \
    /etc/udev/rules \
    /etc/xdg/openbox \
    /root/OpenJVS-Hat

# Copy application files
COPY sbin/piforce/ /sbin/piforce/
COPY var/www/html/ /var/www/html/
COPY boot/ /boot/
COPY etc/ /etc/
COPY root/ /root/

# Create all mode/state files with safe defaults
RUN echo "openoff"   > /sbin/piforce/openmode.txt   && \
    echo "ffboff"    > /sbin/piforce/ffbmode.txt     && \
    echo "auto"      > /sbin/piforce/emumode.txt     && \
    echo "menu"      > /sbin/piforce/bootfile.txt    && \
    echo "0"         > /sbin/piforce/pid.txt         && \
    echo "0"         > /sbin/piforce/card_emulator/pid.txt && \
    echo "always-on" > /sbin/piforce/powerfile.txt   && \
    echo "relayoff"  > /sbin/piforce/relaymode.txt   && \
    echo "hackoff"   > /sbin/piforce/zeromode.txt    && \
    echo "nfcoff"    > /sbin/piforce/nfcmode.txt     && \
    echo "simple"    > /sbin/piforce/menumode.txt    && \
    echo "soundoff"  > /sbin/piforce/soundmode.txt   && \
    echo "navoff"    > /sbin/piforce/navmode.txt     && \
    echo "LCD35"     > /sbin/piforce/lcdmode.txt     && \
    echo "wifioff"   > /sbin/piforce/wifimode.txt    && \
    echo "modern"    > /sbin/piforce/ui_mode.txt     && \
    echo ""          > /var/www/logs/log.txt          && \
    echo ""          > /var/www/logs/scriptlog.txt    && \
    echo ""          > /var/log/progress.txt

# Create CSV files with headers (only if not already present via volume mount)
RUN mkdir -p /var/www/html/csv && \
    echo "name,ipaddress,type" > /var/www/html/csv/dimms.csv && \
    printf 'system,filename,name,manufacturer,year,category,players,genre,favourite\n' \
      > /var/www/html/csv/gamelist.csv && \
    printf '# ROM filenames hidden from the main game list (one per line)\n' \
      > /var/www/html/csv/gamelist_hidden.txt

# Set permissions
RUN echo 'www-data ALL=(ALL) NOPASSWD: ALL' >> /etc/sudoers

RUN chown -R www-data:www-data /var/www/html /var/www/logs && \
    chmod -R 755 /var/www/html && \
    chmod -R 777 /var/www/html/csv && \
    chmod -R 777 /sbin/piforce && \
    chmod 666 /var/log/progress.txt && \
    chmod -R 777 /var/log/activecard /var/log/printdata /var/log/cardcheck

# Configure Apache
RUN a2enmod php8.2 2>/dev/null || a2enmod php7.4 2>/dev/null || a2enmod php && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    echo "php_flag display_errors On" >> /etc/apache2/apache2.conf

# Make Python scripts executable
RUN chmod +x /sbin/piforce/*.py && \
    chmod +x /sbin/piforce/card_emulator/*.py && \
    chmod +x /root/*.sh 2>/dev/null || true

# Expose ports
EXPOSE 80 10703

# Startup script
RUN printf '#!/bin/bash\n\
echo ""\n\
echo "WiPiNetbooter — Development Environment"\n\
echo "========================================="\n\
echo "Web UI: http://localhost:8081"\n\
echo "Hardware mode: SIMULATED (no Pi/NetDIMM needed)"\n\
echo ""\n\
# Ensure writable runtime files survive volume mounts\n\
for f in openmode ffbmode emumode bootfile powerfile relaymode zeromode nfcmode menumode soundmode navmode lcdmode wifimode ui_mode; do\n\
  [ -f /sbin/piforce/${f}.txt ] || echo "default" > /sbin/piforce/${f}.txt\n\
done\n\
[ -f /sbin/piforce/pid.txt ] || echo "0" > /sbin/piforce/pid.txt\n\
[ -f /var/www/logs/log.txt ]  || touch /var/www/logs/log.txt\n\
[ -f /var/log/progress.txt ]  || touch /var/log/progress.txt\n\
apachectl -D FOREGROUND\n' > /start.sh && chmod +x /start.sh

WORKDIR /var/www/html
CMD ["/start.sh"]
