#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Static checks for var/www/html/ui_mode.php helpers (no PHP runtime required).
"""
import os
import re
import unittest


class TestUiModeHelpers(unittest.TestCase):
    def setUp(self):
        root = os.path.join(os.path.dirname(__file__), "..")
        self.ui_mode = os.path.join(root, "var", "www", "html", "ui_mode.php")
        self.arcade_icons_css = os.path.join(root, "var", "www", "html", "css", "arcade-icons.css")

    def test_modern_sliding_sidebar_icons_are_whitelisted(self):
        """Icons in modern_sliding_sidebar_nav must appear in arcade_icon $allowed."""
        with open(self.ui_mode, "r", encoding="utf-8", errors="ignore") as f:
            text = f.read()
        allowed_m = re.search(
            r"static \$allowed = \[(.*?)\];",
            text,
            re.DOTALL,
        )
        self.assertIsNotNone(allowed_m, r"Could not parse arcade_icon $allowed list")
        allowed = re.findall(r"'([a-z0-9_-]+)'", allowed_m.group(1))

        nav_icons = re.findall(
            r"\['key'\s*=>\s*'[^']+',\s*'href'\s*=>\s*'[^']+',\s*'icon'\s*=>\s*'([^']+)'",
            text,
        )
        # Fallback: lines like 'icon' => 'dashboard'
        if not nav_icons:
            nav_icons = re.findall(r"'icon'\s*=>\s*'([^']+)'", text)

        self.assertGreater(len(nav_icons), 0, "No sidebar nav icons found")
        for icon in nav_icons:
            self.assertIn(
                icon,
                allowed,
                f"Sidebar uses icon {icon!r} not in arcade_icon allowed list",
            )

    def test_sidebar_nav_icons_have_css_rules(self):
        """Each icon used in modern_sliding_sidebar_nav should have .arcade-icon--* in CSS."""
        with open(self.ui_mode, "r", encoding="utf-8", errors="ignore") as f:
            text = f.read()
        nav_icons = re.findall(r"'icon'\s*=>\s*'([^']+)'", text)
        nav_icons = [i for i in nav_icons if i in (
            "dashboard", "games", "netdimms", "setup", "cabinet", "network", "uimode",
        )]
        self.assertGreaterEqual(len(nav_icons), 7, "Expected modern_sliding_sidebar_nav icons")

        with open(self.arcade_icons_css, "r", encoding="utf-8", errors="ignore") as f:
            css = f.read()
        for icon in set(nav_icons):
            self.assertRegex(
                css,
                rf"\.arcade-icon--{re.escape(icon)}\s*{{",
                f"Missing CSS rule for .arcade-icon--{icon}",
            )


if __name__ == "__main__":
    unittest.main(verbosity=2)
