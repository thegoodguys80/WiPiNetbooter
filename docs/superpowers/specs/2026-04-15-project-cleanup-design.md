# WiPiNetbooter — Project Cleanup Design

**Date:** 2026-04-15  
**Branch:** warp-dev  
**Goal:** Make the project neat and elegant for both contributors (GitHub) and end users (install experience).

---

## Scope

Three parallel concerns:
1. **Repository hygiene** — `.gitignore`, remove temp/dev files, clean git state
2. **Front door** — README rewrite, LICENSE, CONTRIBUTING.md
3. **Documentation** — `docs/` folder with 4 structured files

---

## 1. .gitignore Additions

Add to `.gitignore`:

```
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

---

## 2. Files to Delete

These are temporary dev/test files with no value in the repo:

- `var/www/html/gamelist.php.backup`
- `var/www/html/testwifi.php`
- `sbin/piforce/testwifi.py`

---

## 3. Front Door Files

### LICENSE
Standard MIT license file at repo root. Use GitHub username `thegoodguys80` (from CI badge URL in README). Year: 2026.

### CONTRIBUTING.md
Short file covering:
- How to clone and run locally (Pi or test environment)
- Coding conventions: follow existing PHP/Python style, no new dependencies without discussion
- How to submit a pull request (branch from `master`, open PR against `master`)
- Note: ROM files are not included in the repo — users provide their own

### README.md (rewrite)
Structure (Option A — project-first):

1. **Header** — project name + badges (CI, Python, License)
2. **One-paragraph description** — what it is, what hardware it supports, why you'd want it
3. **Screenshot/demo placeholder** — note to add screenshot
4. **Features** — short bullet list (game library, dark/light theme, WiFi management, card emulator, NetDIMM status, touchscreen optimised)
5. **Requirements** — Raspberry Pi 3B+, NetDIMM board(s), SD card
6. **Quick Start** — `install.sh` as primary path + note that manual network steps are in `docs/installation.md`
7. **Documentation** — links to all 4 docs files
8. **Contributing** — link to CONTRIBUTING.md
9. **License** — MIT

---

## 4. docs/ Structure

Four new files, all committed to `docs/`:

### docs/installation.md
- Hardware requirements (Pi 3B+, SD card size, network switch)
- Flash SD card with Raspberry Pi OS
- Run `install.sh`
- Open browser to Pi's IP
- Initial setup (first login, adding NetDIMM)
- WiFi mode options: Home WiFi vs Hotspot
- **Gaps to fill:** exact SD card OS version, first-login credentials

### docs/configuration.md
- Network config files: `etc/network/interfaces`, `interfaces.home`, `interfaces.hotspot`
- Static IP setup via web UI
- Adding / editing NetDIMM boards
- ROM directory layout (`boot/roms/`)
- Card emulator setup
- **Gaps to fill:** exact ROM naming convention, card emulator hardware requirements

### docs/hardware.md
- Supported arcade boards: Naomi, Naomi 2, Atomiswave
- NetDIMM versions (what's compatible)
- Pi 3B+ wiring to NetDIMM
- Touchscreen setup (if applicable)
- **Gaps to fill:** NetDIMM version compatibility table, wiring diagram

### docs/troubleshooting.md
- Pi not found on network
- Game won't load / NetDIMM shows offline
- WiFi mode switching not working
- Card emulator not detected
- **Gaps to fill:** known error messages and their fixes

---

## 5. Commit Sequence

Logical order for clean git history:

1. `.gitignore` additions
2. Delete temp/dev files (`gamelist.php.backup`, `testwifi.php`, `testwifi.py`)
3. Add `LICENSE`
4. Add `CONTRIBUTING.md`
5. Add `docs/` (4 files with gaps flagged as `<!-- TODO: ... -->`)
6. Rewrite `README.md`
7. Commit pending `CHANGELOG.md` changes

---

## Out of Scope

- Refactoring PHP or Python code
- UI/UX changes
- Moving or reorganising `var/www/html/` page structure
- Automated tests or CI changes
