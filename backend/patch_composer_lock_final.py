import os
import zipfile
import json
import re
import glob

cache_files_dir = os.path.expanduser('~/.cache/composer/files')
cache_repo_dir = os.path.expanduser('~/.cache/composer/repo/https---repo.packagist.org')
lock_file_path = '/home/vladmir/Documents/blog/backend/composer.lock'

# 1. Scan and resolve all cached packages
print("Scanning and mapping all cached packages...")
cached_packages = {}

for root, dirs, files in os.walk(cache_files_dir):
    for f in files:
        if f.endswith('.zip'):
            zip_path = os.path.join(root, f)
            cache_key = f[:-4]
            
            parts = root.split(os.sep)
            if len(parts) >= 2:
                pkg_name = parts[-2] + '/' + parts[-1]
            else:
                continue
                
            try:
                with zipfile.ZipFile(zip_path, 'r') as z:
                    names = z.namelist()
                    root_folders = [n for n in names if n.endswith('/') and n.count('/') == 1]
                    if not root_folders:
                        root_folders = list(set([n.split('/')[0] + '/' for n in names if '/' in n]))
                    
                    if root_folders:
                        folder_name = root_folders[0].strip('/')
                        commit_prefix = folder_name.split('-')[-1] if '-' in folder_name else None
                    else:
                        commit_prefix = None
                        
                    meta = None
                    for name in names:
                        if name.endswith('composer.json'):
                            try:
                                meta = json.loads(z.read(name).decode('utf-8'))
                                break
                            except:
                                pass
            except Exception as e:
                continue
                
            if not meta:
                continue
                
            real_pkg_name = meta.get('name', pkg_name)
            
            # Find in provider
            provider_file = os.path.join(cache_repo_dir, f"provider-{real_pkg_name.replace('/', '~')}.json")
            matched_version = None
            matched_sha = None
            
            if os.path.exists(provider_file):
                try:
                    with open(provider_file, 'r') as pf:
                        pdata = json.load(pf)
                    packages_dict = pdata.get('packages', {})
                    pkg_versions = packages_dict.get(real_pkg_name, [])
                    
                    if isinstance(pkg_versions, dict):
                        pkg_versions_list = []
                        for v_name, v_data in pkg_versions.items():
                            v_data['version'] = v_name
                            pkg_versions_list.append(v_data)
                        pkg_versions = pkg_versions_list
                        
                    if commit_prefix and len(commit_prefix) >= 5:
                        for ver_data in pkg_versions:
                            ref = ver_data.get('dist', {}).get('reference') or ver_data.get('source', {}).get('reference')
                            if ref and ref.startswith(commit_prefix):
                                matched_version = ver_data.get('version')
                                matched_sha = ref
                                break
                except Exception as e:
                    pass
            
            if matched_version and matched_sha:
                cached_packages.setdefault(real_pkg_name, []).append({
                    'version': matched_version,
                    'sha': matched_sha,
                    'cache_key': cache_key
                })

print(f"Mapped {len(cached_packages)} packages from cache.")

# Helper to compare versions
def ver_compare(v1, v2):
    def parse_ver(v):
        return [int(x) for x in re.findall(r'\d+', v)]
    try:
        p1 = parse_ver(v1)
        p2 = parse_ver(v2)
        return (p1 > p2) - (p1 < p2)
    except:
        return 0

# 2. Patch composer.lock
if not os.path.exists(lock_file_path):
    print("composer.lock not found!")
    exit(1)

with open(lock_file_path, 'r') as f:
    lock_data = json.load(f)

patched_count = 0
for section in ['packages', 'packages-dev']:
    for pkg in lock_data.get(section, []):
        name = pkg.get('name')
        if name in cached_packages:
            candidates = cached_packages[name]
            
            # Find the best candidate:
            # Prefer version that matches major version if possible, or just the highest version
            locked_ver = pkg.get('version')
            locked_major = locked_ver.split('.')[0].lstrip('v') if locked_ver else ''
            
            best = None
            for c in candidates:
                c_major = c['version'].split('.')[0].lstrip('v')
                if not best:
                    best = c
                elif c_major == locked_major and best['version'].split('.')[0].lstrip('v') != locked_major:
                    best = c
                elif c_major == locked_major and ver_compare(c['version'], best['version']) > 0:
                    best = c
                elif ver_compare(c['version'], best['version']) > 0:
                    best = c
            
            if best:
                old_ver = pkg.get('version')
                old_sha = pkg.get('source', {}).get('reference')
                
                pkg['version'] = best['version']
                if 'source' in pkg:
                    pkg['source']['reference'] = best['sha']
                    pkg['source']['url'] = f"https://github.com/{name}.git"
                if 'dist' in pkg:
                    pkg['dist']['reference'] = best['sha']
                    pkg['dist']['url'] = f"https://api.github.com/repos/{name}/zipball/{best['sha']}"
                
                if old_ver != best['version'] or old_sha != best['sha']:
                    print(f"Patched {name}: {old_ver} ({old_sha[:8] if old_sha else ''}) -> {best['version']} ({best['sha'][:8]})")
                    patched_count += 1

# Save patched composer.lock
with open(lock_file_path, 'w') as f:
    json.dump(lock_data, f, indent=4)

print(f"Successfully patched {patched_count} packages in composer.lock!")
