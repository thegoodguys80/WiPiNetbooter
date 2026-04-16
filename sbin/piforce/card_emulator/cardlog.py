#!/usr/bin/env python3
import os
import signal
import sys

with open('/sbin/piforce/card_emulator/currentcard.txt', 'w') as cardfile:
    cardfile.write(sys.argv[1])

with open('/sbin/piforce/card_emulator/pid.txt', 'r') as lastpidfile:
    lastpid = lastpidfile.readline().strip()

try:
    os.kill(int(lastpid), signal.SIGKILL)
except (ValueError, ProcessLookupError, PermissionError):
    pass
