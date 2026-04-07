# WiPiNetbooter

![Security Tests](https://github.com/thegoodguys80/WiPiNetbooter/actions/workflows/security-tests.yml/badge.svg?branch=warp-dev)
[![Python 3.6+](https://img.shields.io/badge/python-3.6+-blue.svg)](https://www.python.org/downloads/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Raspberry Pi based Netbooter for Sega Naomi / Naomi 2 / Atomiswave arcade boards with a modern web interface.

---

## What's New in 3.1

### WiFi Page — Full Redesign
- **Tabbed layout** — Home WiFi, Hotspot, and Static IP each in their own tab; no more scrolling through a wall of forms
- **Status cards** — live IP address, SSID, connection type (DHCP/Static) and interface mode shown at a glance
- **Animated mode pill** — pulsing dot shows whether the Pi is in Home or Hotspot mode
- **Show/hide password toggle** — eye icon on password fields
- **SVG icon set** — WiFi, Ethernet, lock, warning, check and restore icons throughout
- **Inline alerts** — success and error messages styled with matching icons
- **Responsive grid** — status cards stack cleanly on small screens

### Game Library
- **Improved image rendering** — game card artwork now uses `object-fit: contain` so images always fit within the marquee box without overflowing
- **Skeleton placeholder** — letter initial shown while artwork loads, removed automatically on load or error
- **Info modal** — marquee art, gameplay screenshot and video preview all in one modal; each section hides itself if no asset is present
- **Launch overlay** — shows game artwork and animated text when sending a game to a NetDIMM

### Dashboard
- **INSERT COIN** tagline added to the header
- **Last Played card** — shows artwork thumbnail, game title, system name and a one-tap LAUNCH button
- **NetDIMM status cards** — animated online/offline dot with IP address per board

---

## What's New in 3.0 — UI/UX Overhaul

### Modern Theme
- **Dark & light mode** — toggle in the sidebar on every page, persisted across sessions
- **Consistent arcade theme** — all pages share the same arcade-retro colour palette; dark and light mode work correctly everywhere
- **CSS variable architecture** — theme switching is instant, no page reload needed
- **48px touch targets** — optimised for arcade cabinet touchscreens
- **Responsive layout** — works on desktop browsers and small touchscreens

### Dashboard (menu.php)
- **Recently Played** — shows the last launched game with a one-tap Launch button
- **Live NetDIMM status** — each configured NetDIMM shows Online/Offline in real time
- Consistent theme with all other pages (naomi2-dashboard special case removed)

### Game Library
- **System filter tabs** — quickly filter by Naomi, Naomi 2, or Atomiswave
- **Search** — filter games by name as you type
- **Launch overlay** — full-screen animation when sending a game to a NetDIMM
- **Skeleton loading** — placeholder tiles while artwork loads
- **Return-to-top button** — appears after scrolling, floats bottom-right
- **Favourites** — star your most-played games for quick access

### Sidebar Navigation
- Available on every page — slides in from the left with a burger button
- **Links:** Dashboard, Games, NetDIMMs, Setup, Options, Network, Card Emulator, Help, Shutdown
- **Live WiFi SSID** — shows the connected network name in the sidebar footer
- **Live NetDIMM count** — shows how many boards are online (e.g. `1/2 NetDIMM`)
- **Theme toggle** — dark/light switch at the bottom of the sidebar

### Card Emulator
- Fully modernised with card-based grid UI
- Breadcrumb navigation (`Dashboard › Card Emulator › Initial D`)
- Back button on sub-pages
- Proper form styling with labelled inputs and primary action button
- Empty state shown when no cards are saved

### Card Management
- Themed data tables — no more hardcoded dark colours; light mode works correctly
- Delete buttons now show a confirmation dialog before deleting card data
- NFC Copy and Delete actions use proper button styling
- Breadcrumb navigation

### All Pages
- Viewport meta tag on every page (fixes mobile scaling)
- Correct `width=device-width, initial-scale=1` format (commas, not semicolons)
- Light mode tested and working across all pages

---

## Requirements

### Hardware
- Raspberry Pi 3B, 3B+ or 4B
- 32GB Class 10 microSD card recommended
- Naomi / Naomi 2 / Atomiswave with NetDIMM (firmware 3.03+)
- Standard network cable and 5V power source for Pi

### Optional Hardware
- Zero security PIC chip (recommended)
- Trendnet TU-S9 USB-Serial adapter (for Card Emulator)
- FTDI RS485 to USB adapter (for OpenJVS)
- OpenJVS Pi HAT
- ACS ACR122U NFC Card Reader

### Software
- Python 3.6+ (Python 2 no longer supported)
- Apache + PHP 8.x
- nmcli (for WiFi scanning and status)

---

## Quick Start

### Download Pre-Built Image
**Full image:** https://drive.google.com/drive/folders/1d2ToNeE02WAdE3Jo_62NHlxzVegzloVy?usp=sharing

**Instruction manual:** https://drive.google.com/file/d/19VvqMnIEYF-vSp-SlMRuhi5AT0qcu-_e/view?usp=drivesdk

### Fresh Raspberry Pi Install

```bash
# Clone the repo onto the Pi
git clone https://github.com/thegoodguys80/WiPiNetbooter.git
cd WiPiNetbooter

# Run the installer
sudo bash install.sh
```

The installer sets up Apache, PHP, Python 3 dependencies, file permissions, and configures the web interface automatically.

### Access the Interface
Open a browser and navigate to your Pi's IP address:
```
http://<pi-ip-address>
```

---

## NetDIMM Online Detection

WiPiNetbooter detects NetDIMM boards using TCP connection to port 10703:

| Result | Meaning |
|---|---|
| Connection succeeds (errno 0) | Board online, ready to receive a game |
| Connection refused (errno 111) | Board online, game currently running |
| Timeout | Board offline or unreachable |

This is more reliable than ICMP ping because NetDIMM boards respond to TCP even when a game is running.

---

## Security

This project has undergone a complete security audit. All critical vulnerabilities have been addressed.

- **Command injection prevention** — all shell commands use `escapeshellarg()`
- **XSS protection** — all user input is escaped with `htmlspecialchars()`
- **Path traversal prevention** — file operations are validated and restricted
- **Input validation** — all user inputs are validated before use

For details see [SECURITY.md](SECURITY.md).

---

## Development

### Docker

```bash
# Start development environment
docker-compose up -d

# Open web interface
open http://localhost:8080

# Run tests
docker exec -it wipinetbooter-dev python3 tests/test_security_fixes.py
```

See [DOCKER.md](DOCKER.md) for full Docker setup.

### Contributing

1. Follow security best practices in [SECURITY.md](SECURITY.md)
2. Use Python 3.6+ for all Python code
3. Use `escapeshellarg()` for all shell command parameters
4. Validate and sanitise all user inputs
5. Run the test suite before submitting a PR

---

## Documentation

- [CHANGELOG.md](CHANGELOG.md) — version history
- [SECURITY.md](SECURITY.md) — security policy
- [DOCKER.md](DOCKER.md) — Docker development environment
- [IMPROVEMENTS.md](IMPROVEMENTS.md) — UI/UX improvement log

---

## Credits

This project is a scratch rewrite of the original solution by **devtty0**, enhanced with a new UI and richer functionality. It supports all netbootable Sega arcade ROMs for Naomi, Naomi 2, and Sammy Atomiswave (Darksoft conversions).

Card reader emulator scripts were originally written by **Winteriscoming** on arcade-projects.com and adapted for the web interface. The netbooting suite including server mode was written by **DragonMinded** and integrated into WiPi.
