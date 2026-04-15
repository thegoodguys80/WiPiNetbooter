# Installation

## Hardware Requirements

- Raspberry Pi 3B, 3B+, or 4B
- 32 GB Class 10 microSD card (minimum 16 GB)
- Naomi / Naomi 2 / Atomiswave arcade board with NetDIMM (firmware 3.03+)
- Standard Cat5/Cat6 Ethernet cable
- 5 V / 2.5 A USB-C power supply for the Pi
- Network switch or router to connect Pi and NetDIMM on the same LAN

### Optional Hardware

| Device | Purpose |
|---|---|
| Zero security PIC chip | Required for some game regions |
| Trendnet TU-S9 USB-Serial adapter | Card emulator |
| FTDI RS485 to USB adapter | OpenJVS controller |
| ACS ACR122U NFC card reader | NFC card write/read |

---

## Option A — Pre-Built Image (Recommended)

Download the pre-built SD card image from the Google Drive link in the README.
Flash it to a 32 GB microSD card using [Raspberry Pi Imager](https://www.raspberrypi.com/software/)
or [balenaEtcher](https://etcher.balena.io/).

Boot the Pi, connect it to your network, and navigate to the Pi's IP address in a browser.

---

## Option B — Fresh Install from Source

### 1. Prepare the SD Card

Flash **Raspberry Pi OS Lite (Bullseye or Bookworm, 32-bit)** to the microSD card.
Enable SSH if you want headless setup (create an empty `ssh` file in the `/boot` partition).

### 2. Boot and Connect

Insert the card, connect the Pi to your network via Ethernet, and power it on.
Find its IP address from your router's DHCP table, or use:

```bash
ping raspberrypi.local
```

### 3. Clone and Install

SSH into the Pi, then:

```bash
git clone https://github.com/thegoodguys80/WiPiNetbooter.git
cd WiPiNetbooter
sudo bash install.sh
```

The installer (7 steps, ~5 minutes) sets up Apache, PHP, Python 3 dependencies,
file permissions, state files, and the CSV databases.

### 4. Open the Web Interface

```
http://<pi-ip-address>
```

or

```
http://raspberrypi.local
```

### 5. Add Your ROMs

Copy `.bin` or `.bin.gz` ROM files to `/boot/roms/` on the Pi:

```bash
scp mygame.bin.gz pi@<pi-ip>:/boot/roms/
```

---

## WiFi Modes

| Mode | Description |
|---|---|
| **Home WiFi** | Pi connects to your home router as a client. Use this for normal operation. |
| **Hotspot** | Pi creates its own WiFi network (`192.168.42.x`). Use this for standalone setups. |

Switch modes from the **Network** page in the web UI.
See [configuration.md](configuration.md) for network config details.

---

## Updating

To update to the latest version, pull and re-run the installer:

```bash
cd WiPiNetbooter
git pull
sudo bash install.sh
```

The installer is safe to re-run — it skips creating CSV files if they already exist,
preserving your game list and NetDIMM config.
