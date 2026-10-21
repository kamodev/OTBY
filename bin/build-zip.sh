#!/usr/bin/env bash
# Build dist/overtheball.zip for Appearance → Themes → Add New → Upload Theme.
set -euo pipefail

cd "$(dirname "$0")/.."
mkdir -p dist
rm -f dist/overtheball.zip

python3 - <<'PY'
import os
import zipfile

skip = {'.DS_Store', 'Thumbs.db'}
with zipfile.ZipFile('dist/overtheball.zip', 'w', zipfile.ZIP_DEFLATED) as archive:
    for root, dirs, files in os.walk('overtheball'):
        dirs[:] = sorted(d for d in dirs if not d.startswith('.'))
        for name in sorted(files):
            if name in skip or name.startswith('.'):
                continue
            archive.write(os.path.join(root, name))
PY

echo "Built dist/overtheball.zip ($(du -h dist/overtheball.zip | cut -f1))"
