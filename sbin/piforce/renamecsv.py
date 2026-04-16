import sys, shutil, os, subprocess

shutil.move(sys.argv[1], sys.argv[2])
os.chmod(sys.argv[2], 0o666)
if sys.argv[3] == 'LCD16':
    subprocess.run(['service', 'lcd-piforce', 'restart'], check=False)
