# Project Cleanup Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make WiPiNetbooter neat and elegant for contributors and end users — clean `.gitignore`, remove temp files, add LICENSE, rewrite README, and add structured `docs/`.

**Architecture:** Seven independent tasks executed in sequence. No code changes — only file additions, deletions, and edits. Each task ends with a commit so the git history stays clean and logical.

**Tech Stack:** Bash (git), Markdown

---

## Task 1: Update .gitignore

**Files:**
- Modify: `.gitignore`

- [ ] **Step 1: Replace `.gitignore` with the expanded version**

The current `.gitignore` only covers Python artifacts and Warp AI files. Replace the entire file content with:

```
__pycache__/
*.py[cod]
*.class
*.so
.Python

# Warp AI Assistant files (keep local only)
WARP.md
RULEBOOK.md
QUICK_START.md
SESSION_SUMMARY.md
README.md copy

# ROM files (user-provided, not redistributable)
boot/roms/

# Node dependencies
node_modules/

# Runtime logs
logs/

# Machine-specific network config
etc/dhcpcd.conf

# Claude / AI session artifacts
.claude/
CLAUDE_ANDROID_MIGRATION.md
IMPROVEMENTS.md

# AI migration working notes inside docs/
docs/ANDROID_MIGRATION_PLAN.md
docs/MIGRATION_SUMMARY.md
docs/PROJECT_DOCUMENTATION.md

# Internal planning files (brainstorming specs)
docs/superpowers/

# Design system working files
design-system/

# Visual brainstorming session files
.superpowers/
```

- [ ] **Step 2: Verify the right files are now ignored**

Run:
```bash
git status --short
```

Expected: `boot/roms/`, `node_modules/`, `logs/`, `.claude/`, `IMPROVEMENTS.md`, `CLAUDE_ANDROID_MIGRATION.md`, `design-system/`, `docs/ANDROID_MIGRATION_PLAN.md`, `docs/MIGRATION_SUMMARY.md`, `docs/PROJECT_DOCUMENTATION.md`, `docs/superpowers/`, `.superpowers/`, `etc/dhcpcd.conf` should all disappear from the untracked list.

- [ ] **Step 3: Commit**

```bash
git add .gitignore
git commit -m "chore: expand .gitignore for ROMs, logs, AI artifacts, node_modules"
```

---

## Task 2: Delete Temp/Dev Files

**Files:**
- Delete: `var/www/html/gamelist.php.backup`
- Delete: `var/www/html/testwifi.php`
- Delete: `sbin/piforce/testwifi.py`

- [ ] **Step 1: Delete the three temp files**

```bash
rm var/www/html/gamelist.php.backup
rm var/www/html/testwifi.php
rm sbin/piforce/testwifi.py
```

- [ ] **Step 2: Verify they are gone**

```bash
git status --short
```

Expected: all three files appear as deleted (prefixed with `D`).

- [ ] **Step 3: Commit**

```bash
git add -u var/www/html/gamelist.php.backup var/www/html/testwifi.php sbin/piforce/testwifi.py
git commit -m "chore: remove backup and dev test files"
```

---

## Task 3: Add LICENSE

**Files:**
- Create: `LICENSE`

- [ ] **Step 1: Create the MIT license file at the repo root**

Create `LICENSE` with this exact content:

```
MIT License

Copyright (c) 2026 thegoodguys80

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```

- [ ] **Step 2: Verify it exists**

```bash
git status --short
```

Expected: `?? LICENSE`

- [ ] **Step 3: Commit**

```bash
git add LICENSE
git commit -m "chore: add MIT license"
```

---

## Task 4: Add CONTRIBUTING.md

**Files:**
- Create: `CONTRIBUTING.md`

- [ ] **Step 1: Create CONTRIBUTING.md at the repo root**

Content:

```markdown
# Contributing to WiPiNetbooter

Thank you for your interest in contributing. Here is everything you need to get started.

---

## Running Locally

The easiest way to test changes is on a Raspberry Pi running the installed version.
For web interface changes you can also use Docker:

    docker-compose up -d
    open http://localhost:8080

See [DOCKER.md](DOCKER.md) for full Docker setup instructions.

---

## Coding Conventions

- **PHP** — follow the style of existing pages. Use `htmlspecialchars()` for all output,
  `escapeshellarg()` for all shell parameters.
- **Python** — Python 3.6+ only. Use context managers (`with open(...) as f`),
  `subprocess.run()` for subprocesses, specific exception types instead of bare `except:`.
- **No new dependencies** without discussing in an issue first.
- **No PHP frameworks** — the project is intentionally plain PHP for Pi compatibility.

---

## Submitting a Pull Request

1. Fork the repo and create a branch from `master`.
2. Make your changes and test them on a Pi or via Docker.
3. Run the security test suite:
   ```bash
   python3 tests/test_security_fixes.py
   ```
4. Open a PR against `master` with a clear description of what changed and why.

---

## ROM Files

ROM files are **not included** in this repository and must not be added.
Users supply their own `.bin` or `.bin.gz` ROM files and copy them to `/boot/roms/` on the Pi.
`boot/roms/` is gitignored.

---

## Security

Before submitting, review [SECURITY.md](SECURITY.md). Any PR that introduces shell commands,
file operations, or user input handling will be checked against the security policy.
```

- [ ] **Step 2: Verify it exists**

```bash
git status --short
```

Expected: `?? CONTRIBUTING.md`

- [ ] **Step 3: Commit**

```bash
git add CONTRIBUTING.md
git commit -m "docs: add CONTRIBUTING.md"
```

---

## Task 5: Add docs/ — Four Documentation Files

**Files:**
- Create: `docs/installation.md`
- Create: `docs/hardware.md`
- Create: `docs/configuration.md`
- Create: `docs/troubleshooting.md`

> Note: `docs/ANDROID_MIGRATION_PLAN.md`, `docs/MIGRATION_SUMMARY.md`, and `docs/PROJECT_DOCUMENTATION.md` already exist locally but are gitignored — they will not appear in the commit.

- [ ] **Step 1: Create docs/installation.md**

```markdown
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
```

- [ ] **Step 2: Create docs/hardware.md**

```markdown
# Hardware Reference

## Supported Arcade Boards

| Board | System | Notes |
|---|---|---|
| Sega Naomi | GD-ROM / NetDIMM | Most common |
| Sega Naomi 2 | GD-ROM / NetDIMM | Higher-spec Naomi |
| Sammy Atomiswave | Cartridge / NetDIMM | Darksoft conversion required |

---

## NetDIMM Compatibility

WiPiNetbooter communicates with the NetDIMM over a standard Ethernet connection on **port 10703**.

| NetDIMM Firmware | Status |
|---|---|
| 3.03+ | Supported |
| Below 3.03 | Not supported — update firmware first |

The NetDIMM must be on the same LAN as the Pi. WiPiNetbooter detects board status via TCP:

| TCP result | Meaning |
|---|---|
| Connection succeeds (errno 0) | Board online, ready to receive a game |
| Connection refused (errno 111) | Board online, game currently running |
| Timeout | Board offline or unreachable |

---

## Raspberry Pi

| Model | Status |
|---|---|
| Pi 3B | Supported |
| Pi 3B+ | Recommended |
| Pi 4B | Supported |
| Pi Zero / Pi 2 | Not supported |

The Pi must be connected to the same network switch or router as your NetDIMM board(s).

---

## Optional Peripherals

### Card Emulator

Requires a **Trendnet TU-S9 USB-Serial adapter** (or compatible RS-232 USB adapter).
Connect the adapter to the Pi USB port and to the Naomi card reader port.
The card emulator scripts live in `/sbin/piforce/card_emulator/`.

### OpenJVS Controller

Requires an **FTDI RS485 to USB adapter** or an **OpenJVS Pi HAT**.
Used to emulate JVS I/O boards (controls, coin inputs) on Naomi hardware.

### NFC Card Reader

Requires an **ACS ACR122U** USB NFC reader.
Used to read and write physical Naomi card data.

---

## Touchscreen Setup

WiPiNetbooter's web interface is optimised for touchscreen kiosk use (48 px touch targets,
full-screen layout). Any touchscreen with a browser in kiosk mode will work.
The interface has been tested with 7-inch HDMI displays connected directly to the Pi.
```

- [ ] **Step 3: Create docs/configuration.md**

```markdown
# Configuration

## Network Configuration

WiPiNetbooter manages network config via three files installed to `/etc/network/`:

| File | Purpose |
|---|---|
| `interfaces` | Active config — loaded by the networking service |
| `interfaces.home` | Home WiFi template — applied when switching to Home mode |
| `interfaces.hotspot` | Hotspot template — applied when switching to Hotspot mode |

> **Warning:** These files use a fixed-line format. The comment `#DO NOT MOVE OR REMOVE LINES ABOVE`
> marks the boundary that the Python scripts depend on. Do not reorder lines above that comment.

### Static IP

To set a static IP for the Ethernet interface, use the **Network** page in the web UI
(Network → Static IP tab). The UI writes to `/etc/network/interfaces` and restarts networking.

To set manually, edit `/etc/network/interfaces`:

```
auto eth0
iface eth0 inet static
address 192.168.1.102
netmask 255.255.255.0
gateway 192.168.1.1
```

---

## Adding NetDIMM Boards

1. Open the web interface and go to **NetDIMMs**.
2. Click **Add NetDIMM**.
3. Enter a name and the board's IP address.
4. Save — the board will appear on the Dashboard with a live online/offline indicator.

NetDIMM config is stored in `/var/www/html/csv/dimms.csv`:

```
name,ipaddress,type
Main Cabinet,192.168.1.40,naomi
```

---

## ROM Directory Layout

Copy ROM files to `/boot/roms/` on the Pi. Both compressed and uncompressed formats are supported:

```
/boot/roms/
  mygame.bin
  anothergame.bin.gz
```

The game list is managed via the web UI (**Games → Edit Game List**) and stored in
`/var/www/html/csv/gamelist.csv`.

---

## Game List CSV Format

`/var/www/html/csv/gamelist.csv` columns:

```
system,filename,name,manufacturer,year,category,players,genre,favourite
naomi,mygame.bin,My Game,Sega,2001,action,2,fighting,0
```

| Column | Values |
|---|---|
| system | `naomi`, `naomi2`, `atomiswave` |
| filename | ROM filename in `/boot/roms/` |
| favourite | `0` or `1` |

Use **Games → Import CSV** to bulk-import a game list.

---

## State Files

WiPiNetbooter uses plain text files in `/sbin/piforce/` to persist mode settings:

| File | Values | Purpose |
|---|---|---|
| `wifimode.txt` | `wifioff`, `home`, `hotspot` | Active WiFi mode |
| `menumode.txt` | `simple`, `modern` | UI mode |
| `bootfile.txt` | `menu`, ROM filename | What to boot on startup |
| `emumode.txt` | `auto`, `manual` | Card emulator mode |
| `openmode.txt` | `openoff`, `openon` | OpenJVS on/off |
| `nfcmode.txt` | `nfcoff`, `nfcon` | NFC reader on/off |

These are written by the PHP web interface and read by the Python backend scripts.

---

<!-- TODO: Add card emulator hardware wiring details and serial port configuration -->
```

- [ ] **Step 4: Create docs/troubleshooting.md**

```markdown
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
```

- [ ] **Step 5: Verify all four files exist**

```bash
ls docs/
```

Expected output includes: `installation.md  hardware.md  configuration.md  troubleshooting.md`

- [ ] **Step 6: Commit**

```bash
git add docs/installation.md docs/hardware.md docs/configuration.md docs/troubleshooting.md
git commit -m "docs: add installation, hardware, configuration, and troubleshooting guides"
```

---

## Task 6: Rewrite README.md

**Files:**
- Modify: `README.md`

- [ ] **Step 1: Replace README.md with the new project-first structure**

Replace the entire content of `README.md` with:

```markdown
# WiPiNetbooter

![Security Tests](https://github.com/thegoodguys80/WiPiNetbooter/actions/workflows/security-tests.yml/badge.svg?branch=warp-dev)
[![Python 3.6+](https://img.shields.io/badge/python-3.6+-blue.svg)](https://www.python.org/downloads/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A Raspberry Pi web interface for netbooting Sega Naomi, Naomi 2, and Atomiswave arcade boards.
Browse your game library, send ROMs to a NetDIMM over the network, manage WiFi, and emulate
card readers — all from a touchscreen-optimised browser UI.

---

<!-- Add a screenshot or GIF here: ![WiPiNetbooter screenshot](docs/screenshot.png) -->

---

## Features

- **Game library** — browse by system, search by name, mark favourites, launch with one tap
- **Game artwork** — marquee art, gameplay screenshots, and video previews per game
- **NetDIMM management** — add multiple boards, live online/offline status, one-tap launch
- **WiFi management** — switch between Home WiFi and Hotspot mode from the browser
- **Dark and light theme** — arcade-retro colour scheme, persisted across sessions
- **Card emulator** — emulate Naomi and Atomiswave save cards over USB-Serial
- **OpenJVS support** — emulate JVS I/O boards for controls and coin inputs
- **NFC reader** — read and write physical Naomi card data
- **Touchscreen optimised** — 48 px touch targets, designed for arcade cabinet kiosk use

---

## Requirements

**Hardware**
- Raspberry Pi 3B+ or 4B
- 32 GB microSD card
- Naomi / Naomi 2 / Atomiswave board with NetDIMM (firmware 3.03+)
- Ethernet cable and network switch

**Software** (installed automatically by `install.sh`)
- Raspberry Pi OS Bullseye or Bookworm (32-bit)
- Apache + PHP 8.x
- Python 3.6+

See [docs/hardware.md](docs/hardware.md) for optional peripherals (card emulator, OpenJVS, NFC).

---

## Quick Start

### Option A — Pre-Built Image

Download the pre-built SD card image and flash it to a 32 GB microSD card:
[Google Drive — WiPiNetbooter images](https://drive.google.com/drive/folders/1d2ToNeE02WAdE3Jo_62NHlxzVegzloVy?usp=sharing)

### Option B — Install from Source

```bash
git clone https://github.com/thegoodguys80/WiPiNetbooter.git
cd WiPiNetbooter
sudo bash install.sh
```

Then open `http://<pi-ip-address>` in a browser.

For full step-by-step instructions including network setup, see [docs/installation.md](docs/installation.md).

---

## Documentation

| Guide | Contents |
|---|---|
| [Installation](docs/installation.md) | Hardware, SD card setup, install.sh walkthrough, WiFi modes |
| [Hardware](docs/hardware.md) | Supported boards, NetDIMM firmware, optional peripherals |
| [Configuration](docs/configuration.md) | Network config, adding NetDIMMs, ROM layout, game list CSV |
| [Troubleshooting](docs/troubleshooting.md) | Common issues and fixes |
| [Changelog](CHANGELOG.md) | Version history |
| [Security](SECURITY.md) | Security policy and audit summary |
| [Docker](DOCKER.md) | Docker development environment |

---

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for how to set up a dev environment, coding conventions,
and the PR process.

---

## Credits

Originally based on the netbooting solution by **devtty0**, extended with a full web UI and
richer functionality.
Card emulator scripts originally written by **Winteriscoming** (arcade-projects.com), adapted
for the web interface.
Netbooting suite including server mode written by **DragonMinded** and integrated into WiPi.

---

## License

MIT — see [LICENSE](LICENSE).
```

- [ ] **Step 2: Verify the file looks correct**

```bash
head -5 README.md
```

Expected: starts with `# WiPiNetbooter`

- [ ] **Step 3: Commit**

```bash
git add README.md
git commit -m "docs: rewrite README with project-first structure"
```

---

## Task 7: Commit Pending CHANGELOG.md Changes

**Files:**
- Modify: `CHANGELOG.md` (already modified — commit the pending changes)

- [ ] **Step 1: Check what changed in CHANGELOG.md**

```bash
git diff CHANGELOG.md
```

Review the diff to confirm the changes are intentional (new version entries, formatting fixes).

- [ ] **Step 2: Commit**

```bash
git add CHANGELOG.md
git commit -m "docs: update CHANGELOG.md"
```

---

## Final Verification

- [ ] Run `git status` — should show a clean working tree.
- [ ] Run `git log --oneline -8` — should show 7 clean commits in logical order.
- [ ] Verify `docs/` contains exactly 4 files: `installation.md`, `hardware.md`, `configuration.md`, `troubleshooting.md`.
- [ ] Verify `LICENSE`, `CONTRIBUTING.md` exist at repo root.
- [ ] Verify `var/www/html/gamelist.php.backup`, `var/www/html/testwifi.php`, `sbin/piforce/testwifi.py` are gone.
