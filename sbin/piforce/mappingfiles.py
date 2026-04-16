import subprocess, glob

for f in glob.glob('/etc/openjvs/games/*'):
    subprocess.run(['sudo', 'chmod', '666', f], check=False)
subprocess.run(['sudo', 'chmod', '777', '/etc/openjvs/games/'], check=False)

for f in glob.glob('/etc/openffb/games/*'):
    subprocess.run(['sudo', 'chmod', '666', f], check=False)
subprocess.run(['sudo', 'chmod', '777', '/etc/openffb/games/'], check=False)
