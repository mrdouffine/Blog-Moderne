import os
import zipfile
import json
import glob

cache_files_dir = os.path.expanduser('~/.cache/composer/files')
cache_repo_dir = os.path.expanduser('~/.cache/composer/repo/https---repo.packagist.org')

print("Scanning cached zip files and matching with provider JSONs...")
mappings = []

for root, dirs, files in os.walk(cache_files_dir):
    for f in files:
        if f.endswith('.zip'):
            zip_path = os.path.join(root, f)
            cache_key = f[:-4]
            
            # Find the package name from the path
            parts = root.split(os.sep)
            if len(parts) >= 2:
                pkg_name = parts[-2] + '/' + parts[-1]
            else:
                continue
                
            try:
                with zipfile.ZipFile(zip_path, 'r') as z:
                    names = z.namelist()
                    # Find root folder in zip
                    root_folders = [n for n in names if n.endswith('/') and n.count('/') == 1]
                    if not root_folders:
                        # Try to find any folder prefix
                        root_folders = list(set([n.split('/')[0] + '/' for n in names if '/' in n]))
                    
                    if root_folders:
                        root_folder = root_folders[0]
                        # Folder name is usually like 'symfony-http-foundation-02656f7/'
                        folder_name = root_folder.strip('/')
                        commit_prefix = folder_name.split('-')[-1] if '-' in folder_name else None
                    else:
                        commit_prefix = None
                        
                    # Let's read composer.json from zip to verify package name
                    meta = None
                    for name in names:
                        if name.endswith('composer.json'):
                            try:
                                meta = json.loads(z.read(name).decode('utf-8'))
                                break
                            except:
                                pass
            except Exception as e:
                print(f"Error reading zip {zip_path}: {e}")
                continue
                
            if not meta:
                continue
                
            real_pkg_name = meta.get('name', pkg_name)
            
            # Now let's find the matching version in provider JSON
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
                        
                    # 1. Match by commit prefix if available
                    if commit_prefix and len(commit_prefix) >= 5:
                        for ver_data in pkg_versions:
                            ref = ver_data.get('dist', {}).get('reference') or ver_data.get('source', {}).get('reference')
                            if ref and ref.startswith(commit_prefix):
                                matched_version = ver_data.get('version')
                                matched_sha = ref
                                break
                                
                    # 2. Match by any reference if we only have one zip and one version or similar
                    if not matched_version:
                        # Let's see if we can find any version matching the dist url or similar
                        for ver_data in pkg_versions:
                            ref = ver_data.get('dist', {}).get('reference')
                            # Sometimes composer cache keys match a specific URL
                            # Let's print out and we can see
                            pass
                except Exception as e:
                    print(f"Error reading provider for {real_pkg_name}: {e}")
            
            mappings.append({
                'pkg_name': real_pkg_name,
                'cache_key': cache_key,
                'commit_prefix': commit_prefix,
                'matched_version': matched_version,
                'matched_sha': matched_sha,
                'zip_path': zip_path
            })

# Print top 30 mappings
for m in mappings[:30]:
    print(f"Package: {m['pkg_name']}")
    print(f"  Cache Key: {m['cache_key']}")
    print(f"  Commit Prefix: {m['commit_prefix']}")
    print(f"  Matched Version: {m['matched_version']}")
    print(f"  Matched SHA: {m['matched_sha']}")
    print("-" * 40)
