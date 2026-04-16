#!/usr/bin/env python3
"""Scan for WiFi networks using nmcli and write results to wifilist.php."""
import subprocess
import re

def scan_networks():
    try:
        # Ensure WiFi radio is on
        subprocess.run(['nmcli', 'radio', 'wifi', 'on'], capture_output=True)
        # Trigger a fresh scan
        subprocess.run(['nmcli', 'dev', 'wifi', 'rescan'], capture_output=True)
        # Get results: output fields SSID only, skip empty/hidden ones
        result = subprocess.run(
            ['nmcli', '-t', '-f', 'SSID', 'dev', 'wifi', 'list'],
            capture_output=True, text=True
        )
        ssids = []
        seen = set()
        for line in result.stdout.splitlines():
            ssid = line.strip()
            # Skip empty/hidden networks
            if ssid and ssid not in seen:
                seen.add(ssid)
                ssids.append(ssid)
        return ssids
    except Exception as e:
        return []

ssids = scan_networks()

with open('/var/www/html/wifilist.php', 'w') as f:
    f.write('<?php\n')
    for i, name in enumerate(ssids, start=1):
        # Escape single quotes for PHP string safety
        safe_name = name.replace("'", "\\'")
        f.write(f"$name{i} = '{safe_name}';\n")
    f.write(f'$ssids = {len(ssids)};\n')
    f.write('?>')
