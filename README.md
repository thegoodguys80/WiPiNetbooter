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
