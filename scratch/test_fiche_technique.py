import urllib.request
import sys

sys.stdout.reconfigure(encoding='utf-8')

def test_product_fiche_technique(pid, name, expected_specs):
    url = f"http://localhost:8085/product-details.php?pid={pid}"
    req = urllib.request.Request(url, headers={"User-Agent": "Mozilla/5.0"})
    with urllib.request.urlopen(req, timeout=10) as res:
        code = res.getcode()
        body = res.read().decode("utf-8", errors="ignore")
        
        assert code == 200, f"HTTP {code} on {url}"
        assert "Fiche Technique" in body, f"Fiche Technique tab missing on {name}"
        
        for spec in expected_specs:
            assert spec.lower() in body.lower(), f"Expected spec '{spec}' missing in {name}"
        
        print(f"[PASS] {name} (pid={pid}): Fiche Technique verified with {len(expected_specs)} technical specifications.")

if __name__ == "__main__":
    print("=== TESTING FICHE TECHNIQUE & REAL PRODUCTS ===")
    
    # 1. iPhone 15 Pro Max
    test_product_fiche_technique(1, "Apple iPhone 15 Pro Max", [
        "A17 Pro", "Super Retina XDR", "Titanium", "48MP", "ProRes", "IP68"
    ])
    
    # 2. Samsung Galaxy S24 Ultra
    test_product_fiche_technique(2, "Samsung Galaxy S24 Ultra", [
        "Snapdragon 8 Gen 3", "Dynamic AMOLED 2X", "200MP", "S-Pen", "5,000 mAh"
    ])
    
    # 3. MacBook Pro 16
    test_product_fiche_technique(3, "Apple MacBook Pro 16", [
        "M3 Max", "Liquid Retina XDR", "36GB unified memory", "140W USB-C"
    ])
    
    # 4. Sony WH-1000XM5
    test_product_fiche_technique(5, "Sony WH-1000XM5", [
        "Noise-Cancelling", "Processor V1", "LDAC", "30 hours"
    ])
    
    # 5. Sony PS5 Slim
    test_product_fiche_technique(10, "Sony PlayStation 5 Slim", [
        "AMD Ryzen Zen 2", "1TB Custom NVMe", "DualSense", "Tempest 3D"
    ])
    
    # 6. Atomic Habits
    test_product_fiche_technique(19, "Atomic Habits", [
        "James Clear", "Hardcover", "Penguin Random House", "978-0735211292"
    ])
    
    print("\n=== ALL FICHE TECHNIQUE & PRODUCT TESTS PASSED SUCCESSFULLY! ===")
