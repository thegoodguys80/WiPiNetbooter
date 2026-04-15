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
