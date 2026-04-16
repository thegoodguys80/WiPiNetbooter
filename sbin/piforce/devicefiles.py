import subprocess, glob

for f in glob.glob('/etc/openjvs/devices/*'):
    subprocess.run(['sudo', 'chmod', '666', f], check=False)
subprocess.run(['sudo', 'chmod', '777', '/etc/openjvs/devices/'], check=False)
