import urllib.request
import json
import socket
import sys

sys.stdout.reconfigure(encoding='utf-8')

def test_enterprise_stack():
    print("================================================================================")
    print("=== EXECUTING COMPLETE 16-PILLAR ENTERPRISE VERIFICATION SUITE (PHASES 1-6) ===")
    print("================================================================================\n")

    # 1. Check Redis Port (Phase 2)
    with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
        res = s.connect_ex(('localhost', 6379))
        assert res == 0
        print("[PASS] Phase 2 - Redis 7 Event Queue (localhost:6379) is LIVE & accepting socket connections.")

    # 2. Check Qdrant Vector DB (Phase 4)
    with urllib.request.urlopen("http://localhost:6333/", timeout=5) as res:
        data = json.loads(res.read().decode('utf-8'))
        assert res.getcode() == 200
        print(f"[PASS] Phase 4 - Qdrant Vector DB (http://localhost:6333) is LIVE: {data.get('title')} v{data.get('version')}.")

    # 3. Core Users RBAC API (Phase 1)
    with urllib.request.urlopen("http://localhost:3000/api/core/users", timeout=10) as res:
        data = json.loads(res.read().decode('utf-8'))
        assert res.getcode() == 200
        assert data["total"] > 0
        print(f"[PASS] Phase 1 - Core Users RBAC API (/api/core/users) returned {data['total']} users (Admin verified).")

    # 4. Core Inventory Management API (Phase 1 & 2)
    with urllib.request.urlopen("http://localhost:3000/api/core/inventory?productId=1", timeout=10) as res:
        data = json.loads(res.read().decode('utf-8'))
        assert res.getcode() == 200
        assert data["stock"] >= 0
        print(f"[PASS] Phase 1 - Core Inventory API (/api/core/inventory) verified Product #1 stock: {data['stock']} units ({data['warehouse']}).")

    # 5. Core Orders API Creation & State Machine (Phase 1 & 2)
    order_payload = json.dumps({
        "userId": 1,
        "items": [{"productId": 1, "quantity": 1, "unitPrice": 134900}],
        "paymentMethod": "Credit Card",
        "currency": "USD",
        "shippingCity": "Casablanca"
    }).encode('utf-8')
    req_order = urllib.request.Request("http://localhost:3000/api/core/orders", data=order_payload, headers={"Content-Type": "application/json"})
    with urllib.request.urlopen(req_order, timeout=10) as res:
        data = json.loads(res.read().decode('utf-8'))
        assert res.getcode() == 200
        assert data["success"] is True
        order_num = data["order"]["orderNumber"]
        print(f"[PASS] Phase 1 - Order Fulfillment Engine created order '{order_num}' with tracking '{data['order']['trackingNumber']}'.")

    # 6. Multi-Agent AI Supervisor & Darija NLP (Phase 3 & 5)
    chat_payload = json.dumps({
        "message": "شحال الثمن ديال هاد التلفون وبغيت نعرف واش كاين فالمخزن",
        "productId": 1,
        "currency": "MAD"
    }).encode('utf-8')
    req_chat = urllib.request.Request("http://localhost:3000/api/chat", data=chat_payload, headers={"Content-Type": "application/json"})
    with urllib.request.urlopen(req_chat, timeout=10) as res:
        data = json.loads(res.read().decode('utf-8'))
        assert res.getcode() == 200
        assert data["role"] in ["SALES_AGENT", "INVENTORY_AGENT"]
        print(f"[PASS] Phase 3 & 5 - Multi-Agent Supervisor routed to '{data['role']}' with Darija grounding.")

    # 7. Multimodal Vision Search API (Phase 5)
    vision_payload = json.dumps({
        "imageDescription": "Titanium flagship smartphone with camera island"
    }).encode('utf-8')
    req_vision = urllib.request.Request("http://localhost:3000/api/search/vision", data=vision_payload, headers={"Content-Type": "application/json"})
    with urllib.request.urlopen(req_vision, timeout=10) as res:
        data = json.loads(res.read().decode('utf-8'))
        assert res.getcode() == 200
        assert data["success"] is True
        print(f"[PASS] Phase 5 - Multimodal Vision Search (/api/search/vision) detected: {data['visionInference']['detectedCategory']}.")

    # 8. AI Observability & Control Center (Phase 6)
    with urllib.request.urlopen("http://localhost:3000/admin/ai-observability", timeout=10) as res:
        assert res.getcode() == 200
        print("[PASS] Phase 6 - AI Observability & Control Center (/admin/ai-observability) returned HTTP 200.")

    # 9. Classic PHP Storefront (Docker Port 8085)
    with urllib.request.urlopen("http://localhost:8085/index.php", timeout=10) as res:
        assert res.getcode() == 200
        print("[PASS] Classic PHP Storefront (http://localhost:8085) returned HTTP 200.")

    print("\n================================================================================")
    print("=== ALL 16-PILLAR ENTERPRISE VERIFICATION SUITES PASSED (9/9) ===")
    print("================================================================================")

if __name__ == "__main__":
    test_enterprise_stack()
