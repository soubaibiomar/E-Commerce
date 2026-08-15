import urllib.request
import sys

sys.stdout.reconfigure(encoding='utf-8')

def test_3d_assets_and_page():
    # 1. Test 3D JS script
    url_js = "http://localhost:8085/assets/js/product-3d-model.js"
    req_js = urllib.request.Request(url_js, headers={"User-Agent": "Mozilla/5.0"})
    with urllib.request.urlopen(req_js, timeout=10) as res:
        code = res.getcode()
        body = res.read().decode("utf-8", errors="ignore")
        assert code == 200, f"HTTP {code} on {url_js}"
        assert "Product3DViewer" in body, "Product3DViewer class missing in JS file"
        print("[PASS] product-3d-model.js loaded successfully with WebGL Three.js engine.")

    # 2. Test 3D Model Studio on Smartphone (iPhone 15 Pro Max)
    url_p1 = "http://localhost:8085/product-details.php?pid=1"
    req_p1 = urllib.request.Request(url_p1, headers={"User-Agent": "Mozilla/5.0"})
    with urllib.request.urlopen(req_p1, timeout=10) as res:
        code = res.getcode()
        body = res.read().decode("utf-8", errors="ignore")
        assert code == 200
        assert "webgl3DCanvas" in body, "webgl3DCanvas container missing"
        assert "3D Model Studio" in body, "3D Model Studio button missing"
        assert "three.min.js" in body, "Three.js CDN script missing"
        assert "OrbitControls.js" in body, "OrbitControls CDN script missing"
        assert "switchMediaView" in body, "switchMediaView function missing"
        print("[PASS] Product Details (iPhone 15 Pro) integrates 3D WebGL Studio & Controls.")

    # 3. Test 3D Model Studio on Laptop (MacBook Pro)
    url_p3 = "http://localhost:8085/product-details.php?pid=3"
    req_p3 = urllib.request.Request(url_p3, headers={"User-Agent": "Mozilla/5.0"})
    with urllib.request.urlopen(req_p3, timeout=10) as res:
        code = res.getcode()
        body = res.read().decode("utf-8", errors="ignore")
        assert code == 200
        assert "type: 'laptop'" in body, "Model type laptop not detected"
        print("[PASS] Product Details (MacBook Pro) correctly configures 3D Laptop model.")

    # 4. Test 3D Model Studio on Headphones (Sony WH-1000XM5)
    url_p5 = "http://localhost:8085/product-details.php?pid=5"
    req_p5 = urllib.request.Request(url_p5, headers={"User-Agent": "Mozilla/5.0"})
    with urllib.request.urlopen(req_p5, timeout=10) as res:
        code = res.getcode()
        body = res.read().decode("utf-8", errors="ignore")
        assert code == 200
        assert "type: 'headphone'" in body, "Model type headphone not detected"
        print("[PASS] Product Details (Sony WH-1000XM5) correctly configures 3D Headphone model.")

if __name__ == "__main__":
    print("=== EXECUTING 3D MODEL STUDIO VERIFICATION SUITE ===")
    test_3d_assets_and_page()
    print("\n=== ALL 3D MODEL STUDIO TESTS PASSED (4/4) ===")
