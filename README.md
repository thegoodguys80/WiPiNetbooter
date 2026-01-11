# WiPiNetbooter

![Security Tests](https://github.com/thegoodguys80/WiPiNetbooter/actions/workflows/security-tests.yml/badge.svg?branch=warp-dev)
[![Python 3.6+](https://img.shields.io/badge/python-3.6+-blue.svg)](https://www.python.org/downloads/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Raspberry Pi based Netbooter for Sega Naomi/Chihiro/Triforce arcade boards

## 🎉 Version 2.0 - Security & Python 3 Release

**⚠️ IMPORTANT:** This release includes **critical security fixes**. All users should update immediately.

### ✅ What's New in 2.0
- **🛡️ Security Hardened** - 29 files secured, 50+ vulnerabilities fixed
- **🐍 Python 3 Compatible** - Modern Python support (Python 2 no longer supported)
- **✨ Code Quality** - Clean, documented, maintainable code
- **🧪 Tested** - Comprehensive test suite included

**See [CHANGELOG.md](CHANGELOG.md) for full details** | **Security Info: [SECURITY.md](SECURITY.md)**

---

## Requirements

### **NEW:** Python 3.6+ Required
- Python 2 is no longer supported
- Update existing installations: `sudo apt-get install python3 python3-pip`

### Hardware
- Raspberry Pi 3B, 3B+ or 4B
- 32GB Class 10 microSD card recommended
- Naomi/Naomi2/Triforce/Chihiro with NetDIMM (firmware 3.03+)
- Standard network cable
- 5V power source for Pi

### Optional Hardware
- Zero security PIC chip (recommended)
- Trendnet TU-S9 USB-Serial adapter (for Card Emulator)
- FTDI RS485 to USB adapter (for OpenJVS)
- OpenJVS Pi HAT
- ACS ACR122U NFC Card Reader

---

## Quick Start

### Download Pre-Built Image
<b>Full image download link:</b> https://drive.google.com/drive/folders/1d2ToNeE02WAdE3Jo_62NHlxzVegzloVy?usp=sharing<br><br>
<b>Instruction manual:</b> https://drive.google.com/file/d/19VvqMnIEYF-vSp-SlMRuhi5AT0qcu-_e/view?usp=drivesdk<br>
<p>This version of the Pi Netbooter code is a scratch rewrite of the original solution written by devtty0 and has been enhanced with a new user interface and richer functionality. It has full support for all netbootable Sega arcade ROMs for the Naomi, Naomi2, Triforce, Chihiro and the Atomiswave conversions made possible by Darksoft. This version also includes the card reader emulator code for games that support it, the original python scripts were written by Winteriscoming on the arcade-projects.com forums and have been adapted for use in a web interface. The entire netbooting suite of scripts including the on screen menu and server mode was written by DragonMinded and integrated into WiPi.</p>
<p>You will need:</p>
<p>A Raspberry Pi v3B, 3B+ or 4B and microSD Card - 32GB Class 10 card recommended</p>
<p>A Naomi, Naomi2, Triforce or Chihiro with a netdimm running firmware 3.03 or greater</p>
<p>A standard network cable and 5v power source for the Pi &ndash; you can make a custom cable to draw power directly from the system</p>
<p>A Web Browser :)</p>
<p>Optional but recommended: a zero security pic chip</p>
<p>Optional: a Trendnet TU-S9 USB-Serial adaptor and custom serial cable for the Card Emulator</p>
<p>Optional: an FTDI based RS485 to USB adaptor for OpenJVS (see <a href="https://github.com/OpenJVS/OpenJVS">https://github.com/OpenJVS/OpenJVS</a> for more information)</p>
<p>Optional: OpenJVS Pi HAT (see <a href="https://github.com/OpenJVS/OpenJVS">https://github.com/OpenJVS/OpenJVS</a> for more information)</p>
<p>Optional: ACS ACR122U NFC Card Reader</p>

## Testing

Version 2.0 includes a comprehensive test suite to validate security fixes:

```bash
# Run security validation tests
python3 tests/test_security_fixes.py
```

**Test Coverage:**
- ✅ Command injection prevention
- ✅ XSS protection verification
- ✅ Input validation checks
- ✅ Python 3 compatibility
- ✅ Secure subprocess usage

## Security

This project has undergone a complete security audit. All critical vulnerabilities have been addressed.

**Key Security Features:**
- **Command Injection Prevention** - All shell commands use proper parameter escaping
- **XSS Protection** - All user input is properly escaped in output
- **Path Traversal Prevention** - File operations are validated and restricted
- **Input Validation** - Comprehensive validation of all user inputs

For detailed security information, see [SECURITY.md](SECURITY.md).

**Security Reporting:** If you discover a vulnerability, please see SECURITY.md for responsible disclosure.

## Development

### Docker Development Environment

For development without Raspberry Pi hardware:

```bash
# Start Docker environment
docker-compose up -d

# Access web interface
open http://localhost:8080

# Run tests
docker exec -it wipinetbooter-dev python3 tests/test_security_fixes.py
```

See [DOCKER.md](DOCKER.md) for complete Docker setup instructions.

### Contributing

Contributions are welcome! When contributing:

#### Setup Pre-Commit Hooks

Install pre-commit hooks to catch issues before committing:

```bash
./install-hooks.sh
```

This will automatically:
- Check Python and PHP syntax
- Validate security patterns
- Run security tests
- Check for secrets

#### Guidelines

1. Follow the security best practices in [SECURITY.md](SECURITY.md)
2. Run the test suite before submitting
3. Use Python 3.6+ for all Python code
4. Use `escapeshellarg()` for all shell command parameters
5. Validate and sanitize all user inputs
6. Pre-commit hooks must pass before submitting PRs

## Documentation

- **[CHANGELOG.md](CHANGELOG.md)** - Version history and changes
- **[SECURITY.md](SECURITY.md)** - Security policy and best practices
- **[DOCKER.md](DOCKER.md)** - Docker development environment
- **[WARP.md](WARP.md)** - Development guidance for AI assistants

