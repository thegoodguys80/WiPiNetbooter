import subprocess
from time import sleep

sleep(1)
subprocess.run(['sudo', 'shutdown', '-r', 'now'], check=True)
