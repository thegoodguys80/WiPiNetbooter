# Security Policy

## Overview

WiPiNetbooter has undergone a comprehensive security audit and refactoring to eliminate critical vulnerabilities. This document outlines the security measures implemented and best practices for contributors.

## Security Improvements (January 2026)

### ✅ Fixed Vulnerabilities

#### 1. Command Injection (CRITICAL)
**Status:** ✅ FIXED in all 29 affected files

**Problem:** The codebase used `escapeshellcmd()` which is vulnerable to command injection attacks. Attackers could inject malicious commands through user input.

**Solution:** 
- Replaced all `escapeshellcmd()` calls with `escapeshellarg()`
- Added comprehensive input validation
- Implemented whitelisting for parameters

**Files Fixed:**
- PHP: load.php, wifi.php, wired.php, devices.php, devicescan.php, bluetooth.php, launchcard.php, launchopenjvs.php, updatewifi.php, updatehotspot.php, scanning.php, saveaudit.php, cardmanagement.php, mapping.php, ffbmapping.php, shutdown.php, reboot.php, switchmode.php, importcsv.php, updatecsvenable.php, updatecsvfave.php, updatecsvmapping.php, updatecsvffbmapping.php, fwupdatesend.php, loadnochrome.php, dimmscanner.php, gamelist.php
- Python: webforce.py (migrated to subprocess module)

#### 2. Cross-Site Scripting (XSS)
**Status:** ✅ FIXED

**Solution:**
- Added `htmlspecialchars()` with `ENT_QUOTES` and UTF-8 encoding to all user-controlled output
- URL encoding for parameters in links
- Proper escaping of all dynamic content

**Protected Pages:** wifi.php, devicescan.php, bluetooth.php, mapping.php, ffbmapping.php, and more

#### 3. Path Traversal
**Status:** ✅ FIXED

**Solution:**
- Use `basename()` to prevent directory traversal
- Validate file paths against expected directories
- Use `realpath()` to resolve and validate paths
- Whitelist allowed directories

**Example:**
```php
// Before (vulnerable)
$file = $_GET['file'];
shell_exec("sudo rm $file");

// After (secure)
$file = $_GET['file'] ?? '';
$realpath = realpath($file);
if ($realpath === false || strpos($realpath, '/expected/path/') !== 0) {
    die('Error: Invalid file path');
}
$file = basename($file);
shell_exec('sudo rm ' . escapeshellarg($file));
```

#### 4. Input Validation
**Status:** ✅ IMPLEMENTED

**Solution:**
- IP address validation using `filter_var()` with `FILTER_VALIDATE_IP`
- MAC address validation with regex patterns
- Filename validation with alphanumeric patterns
- SSID/PSK length and character validation
- Device path validation

## Security Features

### 1. Secure Shell Command Execution

All shell commands now use proper parameter escaping:

```php
// Correct usage
$command = 'sudo python /sbin/piforce/script.py ' . escapeshellarg($userInput);
shell_exec($command);
```

### 2. Input Validation Functions

**IP Addresses:**
```php
if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    die('Invalid IP address');
}
```

**Filenames:**
```php
$filename = basename($filename);
if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
    die('Invalid filename');
}
```

**MAC Addresses:**
```php
if (!preg_match('/^([0-9A-Fa-f]{2}:){5}[0-9A-Fa-f]{2}$/', $mac)) {
    die('Invalid MAC address');
}
```

### 3. XSS Protection

All user-controlled output is escaped:

```php
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
echo '<a href="page.php?param=' . urlencode($userInput) . '">Link</a>';
```

### 4. Python 3 Security

- Migrated from `os.system()` to `subprocess` module
- Proper argument passing prevents shell injection
- Context managers for file handling

## Testing

A comprehensive test suite validates security measures:

```bash
python3 tests/test_security_fixes.py
```

**Test Coverage:**
- ✅ No `escapeshellcmd()` in codebase
- ✅ `escapeshellarg()` usage verified
- ✅ Input validation present
- ✅ XSS protection verified
- ✅ Python 3 compatibility
- ✅ No SQL injection patterns
- ✅ Subprocess usage (not os.system)

## Reporting Security Issues

If you discover a security vulnerability, please:

1. **DO NOT** open a public issue
2. Contact the maintainers privately
3. Provide detailed information about the vulnerability
4. Allow time for a fix before public disclosure

## Security Best Practices for Contributors

### For PHP Development

1. **Always validate user input:**
   ```php
   $input = $_GET['param'] ?? '';
   if (!preg_match('/^[expected_pattern]$/', $input)) {
       die('Invalid input');
   }
   ```

2. **Use `escapeshellarg()` for shell commands:**
   ```php
   $cmd = 'command ' . escapeshellarg($param);
   ```

3. **Escape output:**
   ```php
   echo htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
   ```

4. **Validate file paths:**
   ```php
   $file = basename($file);
   $realpath = realpath($file);
   ```

### For Python Development

1. **Use subprocess, not os.system:**
   ```python
   subprocess.run(['sudo', 'command', param], check=True)
   ```

2. **Validate inputs:**
   ```python
   if not re.match(r'^[a-zA-Z0-9_-]+$', filename):
       raise ValueError("Invalid filename")
   ```

3. **Use context managers:**
   ```python
   with open(filepath, 'r') as f:
       data = f.read()
   ```

## Security Audit History

| Date | Version | Changes |
|------|---------|---------|
| January 2026 | 2.0 | Complete security refactor - 29 files secured |
| | | Command injection eliminated |
| | | XSS protection added |
| | | Path traversal prevention |
| | | Python 3 migration |
| | | Comprehensive test suite added |

## Dependencies

Security depends on:
- Python 3.6+ (required)
- PHP 7.4+ (recommended)
- Properly configured sudo permissions
- Firewall protection for web interface

## Additional Resources

- [OWASP PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)
- [Python Security Best Practices](https://python.readthedocs.io/en/stable/library/security_warnings.html)
- [Input Validation Guide](https://cheatsheetseries.owasp.org/cheatsheets/Input_Validation_Cheat_Sheet.html)

---

**Last Updated:** January 11, 2026  
**Security Audit By:** Warp AI Assistant  
**Status:** Production Ready ✅
