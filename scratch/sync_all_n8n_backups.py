import shutil

src = "n8n/zeytech_master_ai_commerce_os.json"
destinations = [
    "n8n/loop_master_ai_commerce_os.json",
    "n8n/loop_engineering_ai_workflow.json",
    "n8n/loop_engineering_ollama_workflow.json"
]

for d in destinations:
    shutil.copyfile(src, d)
    print(f"Synchronized {d} with canonical PROD workflow.")
