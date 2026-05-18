import os
import zipfile
import json
import glob
import re

cache_files_dir = os.path.expanduser('~/.cache/composer/files')
cache_repo_dir = os.path.expanduser('~/.cache/composer/repo/https---repo.packagist.org')
target_dir = '/home/vladmir/Documents/blog/backend/vendor_archives'

os.makedirs(target_dir, exist_ok=True)
print(f"Creating local artifact repository in {target_dir}...")

def get_strip_prefix(names):
    if not names:
        return ""
    first_parts = set()
    has_root_files = False
    for name in names:
        if not name.strip():
            continue
        parts = name.split('/')
        if len(parts) == 1:
            has_root_files = True
        else:
            first_parts.add(parts[0])
            
    if not has_root_files and len(first_parts) == 1:
        return list(first_parts)[0] + '/'
    return ""

# Step 1: Discover all candidates
candidates = {}

for root, dirs, files in os.walk(cache_files_dir):
    for f in files:
        if f.endswith('.zip'):
            zip_path = os.path.join(root, f)
            
            parts = root.split(os.sep)
            if len(parts) >= 2:
                pkg_name = parts[-2] + '/' + parts[-1]
            else:
                continue
                
            try:
                with zipfile.ZipFile(zip_path, 'r') as z:
                    names = z.namelist()
                    strip_prefix = get_strip_prefix(names)
                    commit_prefix = None
                    if strip_prefix:
                        folder_name = strip_prefix.strip('/')
                        commit_prefix = folder_name.split('-')[-1] if '-' in folder_name else None
                        
                    meta = None
                    for name in names:
                        if name.endswith('composer.json'):
                            try:
                                meta = json.loads(z.read(name).decode('utf-8'))
                                break
                            except:
                                pass
            except:
                continue
                
            if not meta:
                continue
                
            real_pkg_name = meta.get('name', pkg_name)
            
            # Find in provider
            provider_file = os.path.join(cache_repo_dir, f"provider-{real_pkg_name.replace('/', '~')}.json")
            matched_version = None
            
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
                                break
                except:
                    pass
            
            if not matched_version:
                matched_version = meta.get('version')
                if not matched_version:
                    matched_version = '1.0.0'
            
            clean_ver = matched_version.lstrip('v')
            size = os.path.getsize(zip_path)
            
            candidates.setdefault((real_pkg_name, clean_ver), []).append({
                'zip_path': zip_path,
                'strip_prefix': strip_prefix,
                'meta': meta,
                'size': size
            })

# Step 2: Repackage only the best candidate for each unique package version
repackaged_count = 0

for (real_pkg_name, clean_ver), list_candidates in candidates.items():
    # Sort candidates:
    # 1. Prefer non-empty strip_prefix (genuine GitHub zipball with full structure)
    # 2. Prefer larger size (more files/completeness)
    list_candidates.sort(key=lambda c: (1 if c['strip_prefix'] else 0, c['size']), reverse=True)
    best = list_candidates[0]
    
    zip_path = best['zip_path']
    strip_prefix = best['strip_prefix']
    
    archive_name = f"{real_pkg_name.replace('/', '-')}-{clean_ver}.zip"
    new_zip_path = os.path.join(target_dir, archive_name)
    
    print(f"Repackaging {real_pkg_name} ({clean_ver}) [strip prefix: {repr(strip_prefix)}, size: {best['size']}] -> {archive_name}")
    
    try:
        with zipfile.ZipFile(zip_path, 'r') as z_in:
            with zipfile.ZipFile(new_zip_path, 'w', zipfile.ZIP_DEFLATED) as z_out:
                for item in z_in.infolist():
                    if item.is_dir():
                        continue
                    
                    filename = item.filename
                    if strip_prefix and filename.startswith(strip_prefix):
                        new_filename = filename[len(strip_prefix):]
                    else:
                        new_filename = filename
                        
                    if new_filename:
                        if new_filename == 'composer.json':
                            try:
                                composer_json_data = json.loads(z_in.read(filename).decode('utf-8'))
                                composer_json_data['version'] = clean_ver
                                data = json.dumps(composer_json_data, indent=4).encode('utf-8')
                            except Exception as e:
                                data = z_in.read(filename)
                        else:
                            data = z_in.read(filename)
                            
                        z_out.writestr(new_filename, data)
        repackaged_count += 1
    except Exception as e:
        print(f"Error repackaging {real_pkg_name}: {e}")

print(f"Successfully repackaged {repackaged_count} files into the local artifact repository!")
