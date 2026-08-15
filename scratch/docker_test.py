import urllib.request
import urllib.parse
import sys

sys.stdout.reconfigure(encoding='utf-8')

def test_endpoint(path, name, expected_text=None):
    url = f"http://localhost:8085/{path}" if not path.startswith("http") else path
    try:
        req = urllib.request.Request(url, headers={"User-Agent": "Mozilla/5.0"})
        with urllib.request.urlopen(req, timeout=10) as response:
            code = response.getcode()
            body = response.read().decode("utf-8", errors="ignore")
            success = (code == 200)
            if expected_text and expected_text.lower() not in body.lower():
                print(f"[FAIL] {name} ({url}) missing expected text: '{expected_text}'")
                return False
            print(f"[PASS] {name} ({url}) returned HTTP {code} ({len(body)} bytes).")
            return True
    except Exception as e:
        print(f"[FAIL] {name} ({url}) failed with error: {e}")
        return False

if __name__ == "__main__":
    print("=== EXECUTING DOCKER INTEGRATION TEST SUITE ===")
    tests = [
        ("index.php", "Homepage", "assets/images/logo.jpg"),
        ("category.php?cid=1", "Category Page (cid=1)", "Category"),
        ("product-details.php?pid=1", "Product Details (pid=1)", "Product"),
        ("sub-category.php?scid=1", "Subcategory Page (scid=1)", "Subcategory"),
        ("search-result.php?product=micromax", "Product Search", "Search"),
        ("login.php", "User Login Page", "Sign in"),
        ("signup.php", "User Signup Page", "Sign Up"),
        ("forgot-password.php", "Password Recovery Page", "Password"),
        ("track-orders.php", "Order Tracking Page", "Track"),
        ("admin/index.php", "Admin Portal Login", "Admin"),
        ("http://localhost:8086/", "phpMyAdmin Database Manager", "phpMyAdmin")
    ]
    
    results = [test_endpoint(path, name, exp) for path, name, exp in tests]
    passed = sum(1 for r in results if r)
    total = len(results)
    print(f"\n=== DOCKER TEST SUITE SUMMARY: {passed}/{total} PASSED ===")
    assert passed == total, "Not all tests passed!"
