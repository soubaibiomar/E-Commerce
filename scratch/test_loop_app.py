import urllib.request
import json
import sys

sys.stdout.reconfigure(encoding='utf-8')

def test_nextjs_endpoints():
    print("=== EXECUTING LOOP-APP NEXT.JS, OLLAMA & N8N AI VERIFICATION SUITE ===")

    # 1. Homepage
    url_home = "http://localhost:3000/"
    with urllib.request.urlopen(url_home, timeout=10) as res:
        code = res.getcode()
        body = res.read().decode('utf-8')
        assert code == 200
        assert "Loop" in body
        print(f"[PASS] Next.js 14 Landing Page (http://localhost:3000) returned HTTP {code}.")

    # 2. Product Details with 3D Studio & Fiche Technique
    url_prod = "http://localhost:3000/products/1"
    with urllib.request.urlopen(url_prod, timeout=10) as res:
        code = res.getcode()
        body = res.read().decode('utf-8')
        assert code == 200
        assert "Apple iPhone 15 Pro Max" in body
        assert "Fiche Technique Complète" in body
        assert "Interactive 360° Studio" in body
        print(f"[PASS] Next.js Product Details (http://localhost:3000/products/1) rendered 3D Studio & Fiche Technique.")

    # 3. Direct Ollama AI Chat Route (/api/chat)
    url_chat = "http://localhost:3000/api/chat"
    payload = json.dumps({
        "message": "What is the battery and camera on the iPhone 15 Pro Max?",
        "productId": 1
    }).encode('utf-8')
    req = urllib.request.Request(url_chat, data=payload, headers={"Content-Type": "application/json"})
    with urllib.request.urlopen(req, timeout=10) as res:
        code = res.getcode()
        data = json.loads(res.read().decode('utf-8'))
        assert code == 200
        assert "reply" in data
        print(f"[PASS] Ollama AI Chat API (/api/chat) returned response (source: '{data.get('source')}'): {data['reply'][:90]}...")

    # 4. n8n AI Chat Proxy API Endpoint (/api/n8n/chat)
    url_ai_chat = "http://localhost:3000/api/n8n/chat"
    payload_n8n = json.dumps({
        "message": "What is the processor and display on this model?",
        "productId": 1
    }).encode('utf-8')
    req_n8n = urllib.request.Request(url_ai_chat, data=payload_n8n, headers={"Content-Type": "application/json"})
    with urllib.request.urlopen(req_n8n, timeout=10) as res:
        code = res.getcode()
        data = json.loads(res.read().decode('utf-8'))
        assert code == 200
        assert "reply" in data
        print(f"[PASS] n8n AI Chat API (/api/n8n/chat) returned response (source: '{data.get('source')}'): {data['reply'][:90]}...")

    # 5. n8n AI Spec Generator Proxy API Endpoint
    url_ai_spec = "http://localhost:3000/api/n8n/generate-specs"
    spec_payload = json.dumps({
        "productName": "Sony WH-1000XM6",
        "brand": "Sony",
        "category": "Audio"
    }).encode('utf-8')
    req_spec = urllib.request.Request(url_ai_spec, data=spec_payload, headers={"Content-Type": "application/json"})
    with urllib.request.urlopen(req_spec, timeout=10) as res:
        code = res.getcode()
        data = json.loads(res.read().decode('utf-8'))
        assert code == 200
        assert "specifications" in data
        assert data["specifications"]["Brand"] == "Sony"
        print(f"[PASS] n8n AI Fiche Generator (/api/n8n/generate-specs) produced structured JSON specs.")

    # 6. Admin AI Generator Suite
    url_admin_ai = "http://localhost:3000/admin/ai-generator"
    with urllib.request.urlopen(url_admin_ai, timeout=10) as res:
        code = res.getcode()
        body = res.read().decode('utf-8')
        assert code == 200
        assert "Admin AI Suite" in body
        print(f"[PASS] Admin AI Generator Portal (http://localhost:3000/admin/ai-generator) returned HTTP {code}.")

    # 7. Cart Page
    url_cart = "http://localhost:3000/cart"
    with urllib.request.urlopen(url_cart, timeout=10) as res:
        code = res.getcode()
        body = res.read().decode('utf-8')
        assert code == 200
        assert "Shopping Cart" in body
        print(f"[PASS] Shopping Cart (http://localhost:3000/cart) returned HTTP {code}.")

    print("\n=== ALL NEXT.JS, OLLAMA & N8N AI VERIFICATION TESTS PASSED (7/7) ===")

if __name__ == "__main__":
    test_nextjs_endpoints()
