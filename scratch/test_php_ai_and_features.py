import urllib.request
import json
import sys

sys.stdout.reconfigure(encoding='utf-8')

def test_php_features():
    print("=== TESTING PHP STOREFRONT (PORT 8085) AI & 3D FEATURES ===")
    
    # 1. Test PHP Homepage with AI Chat Widget
    with urllib.request.urlopen("http://localhost:8085/index.php", timeout=5) as res:
        html = res.read().decode('utf-8')
        assert res.getcode() == 200
        assert "ai-chat-widget-root" in html
        print("[PASS] PHP Homepage (http://localhost:8085) renders AI Chatbot Widget.")

    # 2. Test Product Details with 3D Studio & Fiche Technique
    with urllib.request.urlopen("http://localhost:8085/product-details.php?pid=1", timeout=5) as res:
        html = res.read().decode('utf-8')
        assert res.getcode() == 200
        assert "webgl3DCanvas" in html
        assert "fiche-technique" in html
        print("[PASS] PHP Product Details (http://localhost:8085/product-details.php?pid=1) renders 3D Studio & Fiche Technique.")

    # 3. Test PHP AI Chatbot API Endpoint (English Query)
    payload_en = json.dumps({"message": "What is the processor and display on iPhone 15 Pro Max?", "productId": 1}).encode('utf-8')
    req_en = urllib.request.Request("http://localhost:8085/api-chat.php", data=payload_en, headers={"Content-Type": "application/json"})
    with urllib.request.urlopen(req_en, timeout=10) as res:
        data = json.loads(res.read().decode('utf-8'))
        assert res.getcode() == 200
        assert data["success"] is True
        print(f"[PASS] PHP AI Chatbot (/api-chat.php) responded (Engine: {data['engine']}):\n{data['reply'][:120]}...\n")

    # 4. Test PHP AI Chatbot API Endpoint (Moroccan Darija Query)
    payload_darija = json.dumps({"message": "شحال الثمن ديال هاد التلفون واش كاين فالمخزن؟", "productId": 1}).encode('utf-8')
    req_darija = urllib.request.Request("http://localhost:8085/api-chat.php", data=payload_darija, headers={"Content-Type": "application/json"})
    with urllib.request.urlopen(req_darija, timeout=10) as res:
        data = json.loads(res.read().decode('utf-8'))
        assert res.getcode() == 200
        assert data["success"] is True
        assert data["isDarija"] is True
        print(f"[PASS] PHP AI Chatbot (/api-chat.php) Moroccan Darija Response:\n{data['reply'][:120]}...\n")

    print("=== ALL PHP STOREFRONT (PORT 8085) FEATURES VERIFIED SUCCESSFULLY (4/4) ===")

if __name__ == "__main__":
    test_php_features()
