# Docker Setup for WiPiNetbooter

This Docker setup provides two main use cases:
1. **Development environment** - Test web interface and scripts without hardware
2. **Network netboot server** - Serve ROMs to arcade machines over the network (e.g., on Unraid)

## Use Cases

### 1. Network Netboot Server (Unraid, NAS, Server)

✅ **What Works:**
- NetDIMM communication over TCP/IP (port 10703)
- ROM uploading to arcade boards over network
- NetDIMM scanning on local network
- Web interface for game selection and management
- Game launching to remote arcade machines
- Centralized ROM storage and management

❌ **What Doesn't Work:**
- OpenJVS (USB-serial controller I/O)
- OpenFFB (USB force feedback wheels)
- Card reader emulation (USB-serial/NFC devices)
- GPIO relay control
- Local input device detection

**Perfect for**: Running WiPiNetbooter on a server (Unraid, NAS, Docker host) that communicates with arcade machines on the same network.

### 2. Development/Testing Environment

✅ **What Works:**
- Web interface testing and development
- UI testing on different screen sizes
- Python script syntax checking
- PHP development
- CSV/configuration management

❌ **What Doesn't Work:**
- Actual netboot operations (no arcade hardware)
- USB device access
- GPIO operations

## Deployment as Network Netboot Server (Unraid/NAS)

### Prerequisites
- Unraid server or Docker host on same network as arcade machines
- Network access to arcade NetDIMM boards (port 10703)
- ROM files stored on the server

### Option 1: Host Networking (Recommended)

Use host networking for direct access to arcade machines:

```yaml
# docker-compose.yml for Unraid
version: '3.8'
services:
  wipinetbooter:
    build: .
    container_name: wipinetbooter-netboot
    network_mode: host
    volumes:
      - /mnt/user/roms:/boot/roms          # Your ROM storage
      - ./var/www/html:/var/www/html       # Web interface
      - ./sbin/piforce:/sbin/piforce       # Python scripts
    restart: unless-stopped
    environment:
      - TZ=America/New_York                 # Set your timezone
```

### Option 2: Bridge Networking

Use bridge mode with explicit port mapping:

```yaml
version: '3.8'
services:
  wipinetbooter:
    build: .
    container_name: wipinetbooter-netboot
    ports:
      - "80:80"           # Web interface
      - "10703:10703"     # NetDIMM communication
    volumes:
      - /mnt/user/roms:/boot/roms
      - ./var/www/html:/var/www/html
    restart: unless-stopped
```

### Unraid Installation Steps

```bash
# SSH into Unraid
ssh root@<unraid-ip>

# Navigate to appdata directory
cd /mnt/user/appdata

# Create directory and clone repository
mkdir -p wipinetbooter
cd wipinetbooter
git clone https://github.com/thegoodguys80/WiPiNetbooter.git .
git checkout warp-dev

# Create docker-compose.yml (use one of the configs above)
nano docker-compose.yml

# Build and start
docker-compose up -d --build

# Check logs
docker-compose logs -f

# Access web interface
# http://<unraid-ip>:80/gamelist.php (host mode)
# or http://<unraid-ip>/gamelist.php (bridge mode)
```

### ROM Management on Unraid

```bash
# ROMs should be in: /mnt/user/roms/
# The container mounts this to: /boot/roms/

# Example structure:
# /mnt/user/roms/
#   ├── mvsc2.bin.gz
#   ├── ikaruga.bin.gz
#   └── 18wheeler.bin.gz

# Set permissions
chmod -R 755 /mnt/user/roms
```

### Network Configuration

Ensure arcade machines can reach the Docker host:

1. **Find Docker host IP**: `ip addr show`
2. **Configure NetDIMM** on arcade board to point to Docker host IP
3. **Test connectivity**: From arcade machine, ping Docker host
4. **Check port**: `netstat -tulpn | grep 10703`

## Quick Start (Development)

### 1. Build and Run

```bash
# Build and start the container
docker-compose up -d

# View logs
docker-compose logs -f

# Stop the container
docker-compose down
```

### 2. Access the Web Interface

Open your browser to:
```
http://localhost:8080
```

### 3. Development Workflow

The following directories are mounted as volumes for live code editing:
- `sbin/piforce/` - Python netboot scripts
- `var/www/html/` - PHP web interface
- `boot/roms/` - ROM files (if testing)
- `logs/` - Application logs

Any changes to these files will be immediately reflected in the container.

## Docker Commands

### Build the Image

```bash
# Build from Dockerfile
docker build -t wipinetbooter:dev .

# Or use docker-compose
docker-compose build
```

### Run the Container

```bash
# Using docker-compose (recommended)
docker-compose up -d

# Or using docker directly
docker run -d \
  -p 8080:80 \
  -p 10703:10703 \
  -v $(pwd)/sbin/piforce:/sbin/piforce \
  -v $(pwd)/var/www/html:/var/www/html \
  --name wipinetbooter-dev \
  wipinetbooter:dev
```

### Container Management

```bash
# View running containers
docker ps

# View container logs
docker logs wipinetbooter-dev
docker-compose logs -f

# Access container shell
docker exec -it wipinetbooter-dev /bin/bash

# Restart container
docker-compose restart

# Stop and remove container
docker-compose down

# Stop and remove with volumes
docker-compose down -v
```

### Testing Python Scripts

```bash
# Access the container
docker exec -it wipinetbooter-dev /bin/bash

# Test triforcetools (without hardware, will fail to connect)
python3 -c "import sys; sys.path.insert(0, '/sbin/piforce'); import triforcetools"

# Test PHP syntax
php -l /var/www/html/dimms.php

# Check Python script syntax
python3 -m py_compile /sbin/piforce/webforce.py

# Run card emulator in test mode (will fail without hardware)
python3 /sbin/piforce/card_emulator/idcardemu.py --help
```

## Development Setup

### Prerequisites

- Docker Desktop (Mac/Windows) or Docker Engine (Linux)
- Docker Compose v3.8 or higher
- At least 2GB free disk space

### Directory Structure in Container

```
/
├── boot/
│   ├── roms/              # ROM files
│   └── config.txt         # Pi boot config
├── sbin/piforce/          # Python netboot engine
│   ├── triforcetools.py
│   ├── webforce.py
│   └── card_emulator/
├── var/www/html/          # PHP web interface
│   ├── gamelist.php
│   ├── dimms.php
│   └── csv/               # Data storage
├── etc/
│   ├── network/           # Network configs
│   └── openjvs/           # Controller configs
└── var/log/               # Log files
```

## Testing Without Hardware

### Web Interface Testing

1. **Add Test Netdimms**: Navigate to Setup > Manage Netdimms
   - Add dummy IP addresses (won't actually connect)
   - Test form validation and CSV storage

2. **Game List Management**: Navigate to Setup > Edit Game List
   - Add/edit/delete games
   - Test CSV operations
   - Check favorites functionality

3. **Network Configuration**: Navigate to Setup > Network Configuration
   - Test UI for network settings
   - Note: Actual network changes won't apply in Docker

### Python Script Testing

```bash
# Test imports
docker exec -it wipinetbooter-dev python3 -c "
import sys
sys.path.insert(0, '/sbin/piforce')
import triforcetools
import device
print('All imports successful')
"

# Check script syntax
docker exec -it wipinetbooter-dev bash -c "
for file in /sbin/piforce/*.py; do
  python3 -m py_compile \$file && echo \"\$file: OK\"
done
"
```

### PHP Testing

```bash
# Test PHP syntax for all files
docker exec -it wipinetbooter-dev bash -c "
for file in /var/www/html/*.php; do
  php -l \$file
done
"
```

## Connecting Real Hardware (Advanced)

If you have USB-serial adapters connected to your host:

### On Linux

```bash
# Find device
ls -la /dev/ttyUSB*

# Add device to docker-compose.yml
devices:
  - /dev/ttyUSB0:/dev/ttyUSB0
  - /dev/ttyACM0:/dev/ttyACM0

# Run with privileged mode
privileged: true
```

### On Mac/Windows

USB device passthrough is limited. Consider:
1. Running Docker in a Linux VM with USB passthrough
2. Using VirtualBox/VMware for full hardware access
3. Deploying directly to Raspberry Pi for hardware testing

## Troubleshooting

### Port Already in Use

```bash
# Find process using port 8080
lsof -i :8080

# Use different port in docker-compose.yml
ports:
  - "8081:80"
```

### Permission Errors

```bash
# Fix permissions on host
chmod -R 755 sbin/piforce
chmod -R 755 var/www/html

# Inside container
docker exec -it wipinetbooter-dev bash
chown -R www-data:www-data /var/www/html
```

### PHP Not Loading

```bash
# Check Apache status
docker exec -it wipinetbooter-dev service apache2 status

# Check Apache error logs
docker exec -it wipinetbooter-dev cat /var/log/apache2/error.log

# Restart Apache
docker exec -it wipinetbooter-dev service apache2 restart
```

### Python Import Errors

```bash
# Check installed packages
docker exec -it wipinetbooter-dev pip3 list

# Reinstall dependencies
docker exec -it wipinetbooter-dev pip3 install evdev psutil pyserial nfcpy
```

## Updating the Container

After modifying Dockerfile or system dependencies:

```bash
# Rebuild and restart
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## Production Deployment

**Note**: This Docker setup is for development only. For production:

1. Use the official Raspberry Pi image
2. Follow the installation manual
3. Flash to SD card and boot on actual hardware
4. Configure hardware-specific settings

## Additional Resources

- [WiPiNetbooter README](README.md)
- [WARP.md](WARP.md) - Development guidance
- [Docker Documentation](https://docs.docker.com/)
- [Docker Compose Reference](https://docs.docker.com/compose/)
