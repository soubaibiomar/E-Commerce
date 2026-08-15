import json
from check_n8n_connections import check_n8n_connections

files = [
    "n8n/zeytech_master_ai_commerce_os.json",
    "n8n/loop_master_ai_commerce_os.json",
    "n8n/loop_engineering_ai_workflow.json",
    "n8n/loop_engineering_ollama_workflow.json"
]

for f in files:
    print(f"\n==========================================")
    print(f"AUDITING FILE: {f}")
    print(f"==========================================")
    check_n8n_connections(f)
