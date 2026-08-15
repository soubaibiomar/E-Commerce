import os

base = r"d:\Online Shopping\shopping\admin\productimages"
print("=== SCANNING PRODUCT IMAGES ===")
for root, dirs, files in os.walk(base):
    rel = os.path.relpath(root, base)
    if rel != ".":
        print(f"Directory {rel}: {files}")
