import json
import os

lock_path = 'composer.lock'

if not os.path.exists(lock_path):
    print("composer.lock not found!")
    exit(1)

with open(lock_path, 'r') as f:
    data = json.load(f)

count = 0
for section in ['packages', 'packages-dev']:
    for pkg in data.get(section, []):
        if 'dist' in pkg and 'shasum' in pkg['dist']:
            pkg['dist']['shasum'] = ""
            count += 1

with open(lock_path, 'w') as f:
    json.dump(data, f, indent=4)

print(f"Cleared shasum for {count} packages in composer.lock!")
