import urllib.request
import json
import sys

sys.stdout.reconfigure(encoding='utf-8')

def test_php_multi_agent_endpoint():
    print("=== 1. TESTING PHP STOREFRONT 14-AGENT AI ENDPOINT (PORT 8085) ===")
    
    test_cases = [
        {
            "name": "Moroccan Darija Smartphone Query",
            "payload": {"message": "bghit téléphone mzyan max 4000 dh", "currency": "MAD"},
            "expected_agent": "SALES_AGENT",
            "check_text": "درهم"
        },
        {
            "name": "Live Order Tracking Query",
            "payload": {"message": "where is my order please?", "orderNumber": "ORD-2026-755040"},
            "expected_agent": "ORDER_MANAGEMENT_AGENT",
            "check_text": "Status"
        },
        {
            "name": "Warehouse Inventory Stock Query",
            "payload": {"message": "how many units in warehouse stock?", "productId": 1},
            "expected_agent": "INVENTORY_AGENT",
            "check_text": "Warehouse"
        },
        {
            "name": "Fiche Technique & 3D Model Query",
            "payload": {"message": "give me the full specs and 3d model details", "productId": 1},
            "expected_agent": "PRODUCT_EXPERT_AGENT",
            "check_text": "Price"
        },
        {
            "name": "Business Analytics & KPIs",
            "payload": {"message": "what is our revenue and conversion rate?"},
            "expected_agent": "ANALYTICS_AGENT",
            "check_text": "Conversion"
        }
    ]

    passed = 0
    for tc in test_cases:
        url = "http://localhost:8085/api-chat.php"
        data = json.dumps(tc["payload"]).encode("utf-8")
        req = urllib.request.Request(url, data=data, headers={"Content-Type": "application/json"})
        
        try:
            with urllib.request.urlopen(req, timeout=5) as res:
                res_data = json.loads(res.read().decode("utf-8"))
                reply = res_data.get("reply", "")
                agent = res_data.get("agentRole", "")
                
                assert res_data.get("success") == True, f"Failed: {res_data}"
                assert tc["expected_agent"] == agent or tc["check_text"].lower() in reply.lower(), f"Unexpected agent {agent}"
                print(f"[PASS] {tc['name']} -> Routed to: {agent}")
                print(f"       Snippet: {reply[:80]}...\n")
                passed += 1
        except Exception as e:
            print(f"[FAIL] {tc['name']} -> Error: {e}\n")

    print(f"PHP AI Engine: {passed}/{len(test_cases)} Passed.")
    return passed == len(test_cases)

def test_n8n_workflow_file():
    print("\n=== 2. VERIFYING MASTER n8n AI WORKFLOW FILE ===")
    with open("n8n/loop_master_ai_commerce_os.json", "r", encoding="utf-8") as f:
        wf = json.load(f)
    
    nodes = [n["name"] for n in wf.get("nodes", [])]
    print(f"Total Workflow Nodes: {len(nodes)}")
    for n in nodes:
        print(f"  - {n}")
    
    assert any("SUPERVISOR" in n for n in nodes), "Missing AI Supervisor"
    assert any("Tool Router" in n for n in nodes), "Missing Controlled Tool Router"
    assert any("Daily AI Business Report" in n for n in nodes), "Missing Autonomous Trigger"
    print("[PASS] Master n8n AI Commerce OS Workflow validated.")
    return True

if __name__ == "__main__":
    p1 = test_php_multi_agent_endpoint()
    p2 = test_n8n_workflow_file()
    
    if p1 and p2:
        print("\n=== ALL LOOP AI COMMERCE OPERATING SYSTEM TESTS PASSED SUCCESSFULLY! ===")
    else:
        sys.exit(1)
