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
