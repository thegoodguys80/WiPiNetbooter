#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Security validation tests for WiPiNetbooter
Tests verify that security fixes are properly implemented
"""

import unittest
import sys
import os
import subprocess
import re

# Add parent directory to path for imports
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'sbin', 'piforce'))


class TestSecurityFixes(unittest.TestCase):
    """Test suite for security vulnerability fixes"""
    
    def setUp(self):
        """Set up test fixtures"""
        self.php_dir = os.path.join(os.path.dirname(__file__), '..', 'var', 'www', 'html')
        self.python_dir = os.path.join(os.path.dirname(__file__), '..', 'sbin', 'piforce')
    
    def test_no_escapeshellcmd_in_php(self):
        """Verify escapeshellcmd() has been replaced with escapeshellarg()"""
        vulnerable_files = []
        
        for filename in os.listdir(self.php_dir):
            if filename.endswith('.php'):
                filepath = os.path.join(self.php_dir, filename)
                with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                    # Check for vulnerable escapeshellcmd pattern
                    if re.search(r'escapeshellcmd\s*\(', content):
                        vulnerable_files.append(filename)
        
        self.assertEqual(len(vulnerable_files), 0, 
                        f"Found escapeshellcmd in: {vulnerable_files}")
    
    def test_escapeshellarg_present(self):
        """Verify escapeshellarg() is used for command parameters"""
        secure_files = []
        
        for filename in ['load.php', 'wifi.php', 'devicescan.php', 'bluetooth.php']:
            filepath = os.path.join(self.php_dir, filename)
            if os.path.exists(filepath):
                with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                    if re.search(r'escapeshellarg\s*\(', content):
                        secure_files.append(filename)
        
        self.assertGreater(len(secure_files), 0, 
                          "No escapeshellarg usage found in secured files")
    
    def test_input_validation_present(self):
        """Verify input validation functions are used"""
        validation_patterns = [
            r'filter_var\s*\(',
            r'preg_match\s*\(',
            r'basename\s*\(',
        ]
        
        files_with_validation = []
        
        for filename in ['wifi.php', 'load.php', 'fwupdatesend.php']:
            filepath = os.path.join(self.php_dir, filename)
            if os.path.exists(filepath):
                with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                    for pattern in validation_patterns:
                        if re.search(pattern, content):
                            files_with_validation.append(filename)
                            break
        
        self.assertGreater(len(files_with_validation), 0,
                          "No input validation found in secured files")
    
    def test_xss_protection(self):
        """Verify XSS protection with htmlspecialchars"""
        files_with_xss_protection = []
        
        for filename in ['wifi.php', 'devicescan.php', 'bluetooth.php']:
            filepath = os.path.join(self.php_dir, filename)
            if os.path.exists(filepath):
                with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                    if re.search(r'htmlspecialchars\s*\(', content):
                        files_with_xss_protection.append(filename)
        
        self.assertGreater(len(files_with_xss_protection), 0,
                          "No XSS protection found in secured files")
    
    def test_php_syntax_valid(self):
        """Verify all PHP files have valid syntax"""
        # This requires php to be installed
        php_available = subprocess.run(['which', 'php'], 
                                      capture_output=True).returncode == 0
        
        if not php_available:
            self.skipTest("PHP not available for syntax checking")
        
        invalid_files = []
        
        for filename in os.listdir(self.php_dir):
            if filename.endswith('.php'):
                filepath = os.path.join(self.php_dir, filename)
                result = subprocess.run(['php', '-l', filepath],
                                      capture_output=True, text=True)
                if result.returncode != 0:
                    invalid_files.append(filename)
        
        self.assertEqual(len(invalid_files), 0,
                        f"PHP syntax errors in: {invalid_files}")
    
    def test_python3_compatibility(self):
        """Verify Python files are Python 3 compatible"""
        import py_compile
        
        incompatible_files = []
        
        for filename in ['webforce.py', 'triforcetools.py']:
            filepath = os.path.join(self.python_dir, filename)
            if os.path.exists(filepath):
                try:
                    py_compile.compile(filepath, doraise=True)
                except py_compile.PyCompileError:
                    incompatible_files.append(filename)
        
        self.assertEqual(len(incompatible_files), 0,
                        f"Python 3 syntax errors in: {incompatible_files}")
    
    def test_no_sql_injection_patterns(self):
        """Check for potential SQL injection patterns"""
        # WiPiNetbooter uses CSV files, not SQL, but check for any SQL patterns
        sql_patterns = [
            r'mysql_query\s*\(',
            r'mysqli_query\s*\(',
            r'\$_(GET|POST)\s*\[.*\]\s*["\'].*SELECT',
        ]
        
        files_with_sql = []
        
        for filename in os.listdir(self.php_dir):
            if filename.endswith('.php'):
                filepath = os.path.join(self.php_dir, filename)
                with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                    content = f.read()
                    for pattern in sql_patterns:
                        if re.search(pattern, content, re.IGNORECASE):
                            files_with_sql.append(filename)
                            break
        
        # Should be 0 since we use CSV files
        self.assertEqual(len(files_with_sql), 0,
                        f"Potential SQL patterns found in: {files_with_sql}")


class TestPython3Migration(unittest.TestCase):
    """Test suite for Python 3 migration"""
    
    def setUp(self):
        """Set up test fixtures"""
        self.python_dir = os.path.join(os.path.dirname(__file__), '..', 'sbin', 'piforce')
    
    def test_no_xrange_usage(self):
        """Verify xrange() has been replaced with range()"""
        files_with_xrange = []
        
        for filename in ['triforcetools.py', 'webforce.py']:
            filepath = os.path.join(self.python_dir, filename)
            if os.path.exists(filepath):
                with open(filepath, 'r', encoding='utf-8') as f:
                    for line in f:
                        # Skip comments (lines starting with # after whitespace)
                        stripped = line.strip()
                        if stripped.startswith('#'):
                            continue
                        # Check for xrange usage in actual code
                        if re.search(r'\bxrange\s*\(', line):
                            files_with_xrange.append(filename)
                            break
        
        self.assertEqual(len(files_with_xrange), 0,
                        f"xrange() found in code (not comments): {files_with_xrange}")
    
    def test_python3_shebang(self):
        """Verify Python files use python3 shebang"""
        for filename in ['triforcetools.py', 'webforce.py']:
            filepath = os.path.join(self.python_dir, filename)
            if os.path.exists(filepath):
                with open(filepath, 'r', encoding='utf-8') as f:
                    first_line = f.readline()
                    self.assertIn('python3', first_line.lower(),
                                f"{filename} should use python3 shebang")
    
    def test_subprocess_usage(self):
        """Verify subprocess module is used instead of os.system"""
        filepath = os.path.join(self.python_dir, 'webforce.py')
        if os.path.exists(filepath):
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
                # Should have subprocess imports
                self.assertIn('subprocess', content,
                            "webforce.py should use subprocess module")
                # Should not use os.system
                self.assertNotIn('os.system(', content,
                               "webforce.py should not use os.system()")


def run_tests():
    """Run the test suite"""
    loader = unittest.TestLoader()
    suite = unittest.TestSuite()
    
    suite.addTests(loader.loadTestsFromTestCase(TestSecurityFixes))
    suite.addTests(loader.loadTestsFromTestCase(TestPython3Migration))
    
    runner = unittest.TextTestRunner(verbosity=2)
    result = runner.run(suite)
    
    return result.wasSuccessful()


if __name__ == '__main__':
    success = run_tests()
    sys.exit(0 if success else 1)
