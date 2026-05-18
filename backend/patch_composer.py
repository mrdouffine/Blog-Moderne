import os
import json
import re
import glob

cache_files_dir = os.path.expanduser('~/.cache/composer/files')
cache_repo_dir = os.path.expanduser('~/.cache/composer/repo/https---repo.packagist.org')
lock_file_path = '/home/vladmir/Documents/blog/backend/composer.lock'

# 1. Scan for all cached zip files and collect their SHAs
print("Scanning cached zip files...")
cached_shas = {}
for root, dirs, files in os.walk(cache_files_dir):
    for f in files:
        if f.endswith('.zip'):
            sha = f[:-4]
            # Find the package name from the path
            parts = root.split(os.sep)
            if len(parts) >= 2:
                package_name = parts[-2] + '/' + parts[-1]
                cached_shas.setdefault(package_name, []).append(sha)

print(f"Found {sum(len(v) for v in cached_shas.values())} cached zips across {len(cached_shas)} packages.")

# 2. Parse provider JSONs to map SHAs to versions
sha_to_version = {}
print("Parsing provider files to map SHAs to versions...")
for provider_file in glob.glob(os.path.join(cache_repo_dir, 'provider-*.json')):
    try:
        with open(provider_file, 'r') as f:
            data = json.load(f)
            
        packages_dict = data.get('packages', {})
        for pkg_name, pkg_data in packages_dict.items():
            if isinstance(pkg_data, dict):
                for ver_name, ver_data in pkg_data.items():
                    dist = ver_data.get('dist', {})
                    ref = dist.get('reference')
                    if ref:
                        sha_to_version[(pkg_name, ref)] = ver_name
            elif isinstance(pkg_data, list):
                for ver_data in pkg_data:
                    ver_name = ver_data.get('version')
                    dist = ver_data.get('dist', {})
                    ref = dist.get('reference')
                    if ref and ver_name:
                        sha_to_version[(pkg_name, ref)] = ver_name
    except Exception as e:
        continue

print(f"Mapped {len(sha_to_version)} (package, SHA) pairs to versions.")

# 3. For each cached package, find which SHA we can map to a version
pkg_cached_resolved = {}
for pkg_name, shas in cached_shas.items():
    resolved = None
    for sha in shas:
        if (pkg_name, sha) in sha_to_version:
            ver = sha_to_version[(pkg_name, sha)]
            if not resolved or ver_compare(ver, resolved[1]) > 0:
                resolved = (sha, ver)
    if resolved:
        pkg_cached_resolved[pkg_name] = resolved

def ver_compare(v1, v2):
    # Simple version comparison
    def parse_ver(v):
        return [int(x) for x in re.findall(r'\d+', v)]
    try:
        p1 = parse_ver(v1)
        p2 = parse_ver(v2)
        return (p1 > p2) - (p1 < p2)
    except:
        return 0

print(f"Resolved {len(pkg_cached_resolved)} packages from cache.")
if len(pkg_cached_resolved) == 0:
    print("Debug: Sample cached_shas keys:", list(cached_shas.keys())[:10])
    print("Debug: Sample sha_to_version keys:", list(sha_to_version.keys())[:10])
    # Let's check a specific one
    test_pkg = 'symfony/http-foundation'
    if test_pkg in cached_shas:
        print(f"Debug: {test_pkg} shas in cache:", cached_shas[test_pkg])
        for sha in cached_shas[test_pkg]:
            print(f"Debug: checking {test_pkg} with sha {sha} in sha_to_version:", (test_pkg, sha) in sha_to_version)
            # Find if there are close matches
            for k in sha_to_version.keys():
                if k[0] == test_pkg:
                    print(f"Debug: close match in sha_to_version: {k}")
                    break

# 4. Load composer.lock and patch it
if not os.path.exists(lock_file_path):
    print("composer.lock not found!")
    exit(1)

with open(lock_file_path, 'r') as f:
    lock_data = json.load(f)

patched_count = 0
for section in ['packages', 'packages-dev']:
    for pkg in lock_data.get(section, []):
        name = pkg.get('name')
        if name in pkg_cached_resolved:
            sha, ver = pkg_cached_resolved[name]
            old_ver = pkg.get('version')
            old_sha = pkg.get('source', {}).get('reference')
            
            if old_sha != sha or old_ver != ver:
                pkg['version'] = ver
                if 'source' in pkg:
                    pkg['source']['reference'] = sha
                    if 'url' in pkg['source']:
                        pkg['source']['url'] = re.sub(r'/[^/]+\.git$', f'/{name.split("/")[-1]}.git', pkg['source']['url'])
                if 'dist' in pkg:
                    pkg['dist']['reference'] = sha
                    pkg['dist']['url'] = f"https://api.github.com/repos/{name}/zipball/{sha}"
                
                print(f"Patched {name}: {old_ver} ({old_sha[:8]}...) -> {ver} ({sha[:8]}...)")
                patched_count += 1

# Save the patched lock file
with open(lock_file_path, 'w') as f:
    json.dump(lock_data, f, indent=4)

print(f"Successfully patched {patched_count} packages in composer.lock!")
