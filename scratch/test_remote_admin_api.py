import urllib.request
import json
import sys

sys.stdout.reconfigure(encoding='utf-8')

def test_remote_admin_endpoints():
    print("=== TESTING REMOTE ADMIN & REAL-TIME SALE NOTIFICATION ENGINE ===")

    # Test 1: Remote Admin Telegram /sales Command via PHP /api-chat.php
    url = "http://localhost:8085/api-chat.php"
    data = json.dumps({"message": "/sales today"}).encode("utf-8")
    req = urllib.request.Request(url, data=data, headers={"Content-Type": "application/json"})
    with urllib.request.urlopen(req, timeout=5) as res:
        resp = json.loads(res.read().decode("utf-8"))
        print("[PASS] Remote Admin /sales Command response:")
        print(f"       {resp.get('reply')[:120]}...\n")

    # Test 2: Remote Admin Stock Inspection
    data = json.dumps({"message": "check stock for product 1"}).encode("utf-8")
    req = urllib.request.Request(url, data=data, headers={"Content-Type": "application/json"})
    with urllib.request.urlopen(req, timeout=5) as res:
        resp = json.loads(res.read().decode("utf-8"))
        print("[PASS] Remote Admin /stock Command response:")
        print(f"       {resp.get('reply')[:120]}...\n")

    # Test 3: Remote Admin Order Lookup
    data = json.dumps({"message": "where is order ORD-2026-755040"}).encode("utf-8")
    req = urllib.request.Request(url, data=data, headers={"Content-Type": "application/json"})
    with urllib.request.urlopen(req, timeout=5) as res:
        resp = json.loads(res.read().decode("utf-8"))
        print("[PASS] Remote Admin /order Command response:")
        print(f"       {resp.get('reply')[:120]}...\n")

    # Test 4: Verify Master n8n Workflow with Remote Admin & Sale Alert Nodes
    with open("n8n/loop_master_ai_commerce_os.json", "r", encoding="utf-8") as f:
        wf = json.load(f)
    nodes = [n["name"] for n in wf.get("nodes", [])]
    print(f"Verified n8n Workflow Nodes ({len(nodes)} total):")
    for n in nodes:
        print(f"  - {n}")

    assert any("Telegram" in n for n in nodes), "Missing Telegram Admin node"
    assert any("SALE COMPLETED" in n for n in nodes), "Missing Sale Completed Event node"
    print("\n[PASS] Remote Admin & Sale Alert n8n Nodes validated successfully!")

if __name__ == "__main__":
    test_remote_admin_endpoints()
