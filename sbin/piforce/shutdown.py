import subprocess, shutil
from time import sleep

sleep(1)
shutil.copy('/var/www/html/csv/romsinfo.csv', '/boot/config/romsinfo.csv')
subprocess.run(['sudo', 'shutdown', 'now'], check=True)
