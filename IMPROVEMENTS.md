# WiPiNetbooter — Improvement Plan & Task Tracker

## Status Key
- [x] Done
- [ ] To Do
- [~] In Progress

---

## Phase 1 — Critical Bug Fixes & Security (COMPLETE)

### Python Backend

- [x] **`card_emulator/*.py` (6 files)** — Replace `os.system()` PID write with direct Python file write (`id2`, `id3`, `idcardemu`, `fzerocardemu`, `mkgpcardemu`, `wmmtcardemu`)
- [x] **`check.py`** — Full Python 2 → 3 migration: `file.readline()` → `f.readline()`, bare `.close` → context managers, `os.system()` → `subprocess.run()`, `sudo python` → `sudo python3`
- [x] **`switchmode.py`** — All bare `.close` → context managers, `os.system()` → `subprocess.run()`, removed unused imports
- [x] **`wificopy.py`** — Bare `.close` → context manager
- [x] **`webforcefw.py`** — Bare `except:` → specific exceptions, `"\x00"` → `b"\x00"` (Python 3 bytes), `exit()` → `sys.exit(0)`
- [x] **`cardlog.py`** — Fixed crash bugs: `file.close` → context manager, `file.readline(lastpidfile)` → `lastpidfile.readline()`, bare `except:` → specific exceptions, added shebang
- [x] **`triforcetools.py`** — Fixed double progress file open, added context manager for ROM file in `DIMM_UploadFile()`
- [x] **`configuration.py`** — Fixed shebang, removed unused `from os import system, name`, leaked file handle on CSV write, removed dead commented code
- [x] **`card_emulator/*.py` (6 files)** — Wrapped `serial.Serial()` in `try/except serial.SerialException`
- [x] **`card_emulator/*.py` (6 files)** — Bare `except:` → `except OSError:` for card file handling
- [x] **`card_emulator/nfcread.py`** — `controlfile.close` → context manager

### Shebang Fixes
- [x] `auditnames.py`, `bluetoothscan.py`, `bluetoothpair.py`, `nfcwrite.py`, `nfcwipe.py`, `nfccheck.py`, `triforcetools.py` — All updated to `#!/usr/bin/env python3`

### PHP Web Interface — Security
- [x] **`cardactions.php`** — Command injection: `escapeshellarg()` on `$copyfile` and `$phpfile` before `popen()`
- [x] **`romaudit.php`** — Command injection: `escapeshellarg()` on `$_GET["path"]` before `popen()`
- [x] **`loadcheck.php`** — XSS: `htmlspecialchars()` + `urlencode()` on user-supplied values in form action and button text
- [x] **`updatedimms.php`** — XSS: `htmlspecialchars()` on `$_GET["name"]`; `intval()` on `$linenum` before array use

---

## Phase 2 — Code Quality (To Do)

### PHP Web Interface
- [ ] **`loadcheck.php`** — `fping` IP address in `pinger()` function not validated before passing to `exec()` — add IP format check
- [ ] **`cardactions.php`** — `$mode` from `$_GET` used in file path construction and HTML output without validation — whitelist check needed
- [ ] **General** — Audit all remaining `shell_exec()` / `popen()` / `exec()` calls across all 67 PHP files for missing `escapeshellarg()`
- [ ] **General** — Audit all `$_GET` / `$_POST` values echoed into HTML for missing `htmlspecialchars()`
- [ ] **`updatedimms.php`** — `$linenum` array index not bounds-checked against actual CSV row count

### Python Backend
- [ ] **`configuration.py`** — Device path `sys.argv[1]` comes from PHP/user input with no validation before `InputDevice()` call
- [ ] **`auditnames.py`** — Review for Python 2 patterns and missing context managers (full read needed)
- [ ] **`bluetoothscan.py` / `bluetoothpair.py`** — Review for `os.system()` usage and missing context managers
- [ ] **`nfcwrite.py` / `nfcwipe.py`** — Review for missing `close()` parentheses and context managers
- [ ] **`card_emulator/nfcread.py`** — File handles opened with bare `open()` at lines 77-88 — convert to context managers
- [ ] **`webforce.py`** — Hardcoded `/dev/ttyUSB0` default for card emulator port — make configurable via config file

### General
- [ ] **Centralise configuration** — Values scattered across `.txt` files in `/sbin/piforce/`. Consider a single JSON/INI config file read by both Python and PHP
- [ ] **Add logging** — Replace bare `print()` statements with Python `logging` module so output level can be controlled
- [ ] **Input validation layer** — Create shared PHP include for validating common `$_GET`/`$_POST` parameters (ROM filenames, IP addresses, mode strings)

---

## Phase 3 — Features & Enhancements (Backlog)

- [ ] **Auto-detect card emulator port** — Replace hardcoded `/dev/ttyUSB0` with dynamic `/dev/serial/by-id/` lookup
- [ ] **Web UI — error feedback** — Show meaningful error messages in UI when netboot fails instead of silent timeout
- [ ] **Health check endpoint** — Add a lightweight PHP endpoint that checks Pi system status (disk space, network, process state) for dashboard display
- [ ] **ROM metadata cache** — Cache auditnames.py results so the web UI doesn't re-scan on every page load
- [ ] **Multi-DIMM simultaneous boot** — Allow loading the same ROM to multiple DIMMs in parallel
- [ ] **Firmware update UI** — Expose `webforcefw.py` functionality through the web interface with progress feedback

---

## Files Changed (Session Log)

| File | Change |
|------|--------|
| `sbin/piforce/card_emulator/id2cardemu.py` | PID write, serial error handling, bare except |
| `sbin/piforce/card_emulator/id3cardemu.py` | PID write, serial error handling, bare except |
| `sbin/piforce/card_emulator/idcardemu.py` | PID write, serial error handling, bare except |
| `sbin/piforce/card_emulator/fzerocardemu.py` | PID write, serial error handling, bare except |
| `sbin/piforce/card_emulator/mkgpcardemu.py` | PID write, serial error handling, bare except |
| `sbin/piforce/card_emulator/wmmtcardemu.py` | PID write, serial error handling, bare except |
| `sbin/piforce/card_emulator/cardlog.py` | Fixed crash bugs, context managers, specific exceptions |
| `sbin/piforce/card_emulator/nfcread.py` | Fixed bare `.close` → context manager |
| `sbin/piforce/check.py` | Full Python 2→3 rewrite |
| `sbin/piforce/switchmode.py` | Context managers, subprocess, removed os.system |
| `sbin/piforce/wificopy.py` | Context manager |
| `sbin/piforce/webforcefw.py` | Specific exceptions, bytes literal, sys.exit |
| `sbin/piforce/triforcetools.py` | Shebang, double file open fix, ROM file context manager |
| `sbin/piforce/configuration.py` | Shebang, unused import, file handle, dead code |
| `sbin/piforce/auditnames.py` | Shebang |
| `sbin/piforce/bluetoothscan.py` | Shebang |
| `sbin/piforce/bluetoothpair.py` | Shebang |
| `sbin/piforce/card_emulator/nfcwrite.py` | Shebang |
| `sbin/piforce/card_emulator/nfcwipe.py` | Shebang |
| `sbin/piforce/card_emulator/nfccheck.py` | Shebang |
| `var/www/html/cardactions.php` | Command injection fix (escapeshellarg) |
| `var/www/html/romaudit.php` | Command injection fix (escapeshellarg) |
| `var/www/html/loadcheck.php` | XSS fix (htmlspecialchars + urlencode) |
| `var/www/html/updatedimms.php` | XSS fix (htmlspecialchars), integer validation (intval) |
