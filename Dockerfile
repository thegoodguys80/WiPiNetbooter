# WiPiNetbooter Docker Development Environment
# This provides a simulated environment for development/testing without physical hardware

FROM debian:bullseye

# Set environment variables
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
    git \
    wget \
    curl \
    fping \
    usbutils \
    # Python libraries that require system packages
    python3-evdev \
    python3-psutil \
    python3-serial \
    # Build tools for compiling dependencies
    build-essential \
    cmake \
    # Network tools
    net-tools \
    iputils-ping \
    iproute2 \
    wireless-tools \
    wpasupplicant \
    hostapd \
    dnsmasq \
    # Bluetooth tools
    bluetooth \
    bluez \
    # NFC tools (for card emulator)
    libnfc-dev \
    libnfc-bin \
    pcscd \
    pcsc-tools \
    libpcsclite-dev \
    # GPIO simulation (mock for non-Pi environment)
    python3-rpi.gpio \
    # Clean up
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Python packages
RUN pip3 install --no-cache-dir \
    nfcpy \
    pyserial \
    evdev \
    psutil

# Create directory structure
RUN mkdir -p /boot/roms \
    /boot/config \
    /boot/overlays \
    /var/www/html \
    /var/www/logs \
    /var/log \
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

# Create mode files with default values
RUN echo "openoff" > /sbin/piforce/openmode.txt && \
    echo "ffboff" > /sbin/piforce/ffbmode.txt && \
    echo "auto" > /sbin/piforce/emumode.txt && \
    echo "menu" > /sbin/piforce/bootfile.txt && \
    echo "0" > /sbin/piforce/pid.txt

# Create CSV files if they don't exist
RUN mkdir -p /var/www/html/csv && \
    touch /var/www/html/csv/dimms.csv && \
    touch /var/www/html/csv/gamelist.csv && \
    echo "name,ipaddress,type" > /var/www/html/csv/dimms.csv && \
    echo "filename,name,system,category,favourite" > /var/www/html/csv/gamelist.csv

# Create logs directory and progress file
RUN mkdir -p /var/www/logs && \
    touch /var/log/progress.txt && \
    chmod 666 /var/log/progress.txt

# Configure Apache
RUN a2enmod php8.2 || a2enmod php7.4 || a2enmod php && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Make Python scripts executable
RUN chmod +x /sbin/piforce/*.py && \
    chmod +x /sbin/piforce/card_emulator/*.py && \
    chmod +x /root/*.sh

# Configure Apache to use /var/www/html
RUN sed -i 's|/var/www/html|/var/www/html|g' /etc/apache2/sites-available/000-default.conf && \
    echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose ports
# 80 - Web interface
# 10703 - Netdimm protocol (if simulating)
EXPOSE 80 10703

# Create startup script
RUN echo '#!/bin/bash\n\
echo "Starting WiPiNetbooter Development Environment..."\n\
echo ""\n\
echo "⚠️  HARDWARE SIMULATION MODE"\n\
echo "This container simulates the Raspberry Pi environment for development."\n\
echo "Actual netboot operations require physical hardware:"\n\
echo "  - Raspberry Pi 3B+/4B"\n\
echo "  - Sega arcade board with NetDIMM"\n\
echo "  - USB-serial adapters for card emulation"\n\
echo ""\n\
echo "Web interface will be available at: http://localhost"\n\
echo ""\n\
\n\
# Start Apache in foreground\n\
echo "Starting Apache web server..."\n\
apachectl -D FOREGROUND\n\
' > /start.sh && chmod +x /start.sh

# Set working directory
WORKDIR /var/www/html

# Start Apache
CMD ["/start.sh"]
