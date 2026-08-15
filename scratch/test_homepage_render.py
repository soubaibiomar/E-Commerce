import urllib.request
import re
import sys

sys.stdout.reconfigure(encoding='utf-8')

def test_images_on_homepage():
    print("=== TESTING ALL IMAGE ASSETS ON HOMEPAGE (http://localhost:8085/) ===")
    
    with urllib.request.urlopen("http://localhost:8085/index.php", timeout=5) as res:
        html = res.read().decode('utf-8')

    # Find all <img src="...">
    img_srcs = re.findall(r'<img[^>]+src=["\']([^"\']+)["\']', html)
    print(f"Found {len(img_srcs)} images on the homepage.")

    broken = []
    checked = set()
    for src in img_srcs:
        if src in checked:
            continue
        checked.add(src)

        url = src if src.startswith("http") else f"http://localhost:8085/{src.lstrip('/')}"
        try:
            req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
            with urllib.request.urlopen(req, timeout=5) as img_res:
                if img_res.getcode() != 200:
                    broken.append((src, img_res.getcode()))
                else:
                    print(f"  [OK 200] {src}")
        except Exception as e:
            broken.append((src, str(e)))

    if broken:
        print(f"\n[FAIL] Found {len(broken)} broken images:")
        for b in broken:
            print(f"  - {b[0]} -> {b[1]}")
        sys.exit(1)
    else:
        print(f"\n[PASS] 100% of images ({len(checked)} unique images) loaded successfully with HTTP 200!")

if __name__ == "__main__":
    test_images_on_homepage()
