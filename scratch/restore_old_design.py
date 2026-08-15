import glob
import os

php_files = glob.glob('shopping/**/*.php', recursive=True)
count = 0

for file_path in php_files:
    try:
        with open(file_path, 'r', encoding='utf-8') as f:
            content = f.read()

        new_content = content.replace(
            '<link rel="stylesheet" href="assets/css/modern.css">',
            '<link rel="stylesheet" href="assets/css/blue.css">'
        )

        if new_content != content:
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(new_content)
            count += 1
            print(f"[RESTORED] {file_path} -> blue.css")
    except Exception as e:
        print(f"[ERROR] {file_path}: {e}")

print(f"\nSuccessfully restored classic theme on {count} PHP template files.")
