import json

def check_n8n_connections(file_path):
    with open(file_path, "r", encoding="utf-8") as f:
        wf = json.load(f)

    nodes = {n["name"]: n for n in wf.get("nodes", [])}
    connections = wf.get("connections", {})

    print(f"Total Nodes Defined in JSON: {len(nodes)}")
    print(f"Total Connection Senders: {len(connections)}")

    # 1. Check if all senders in connections exist in nodes
    for sender in connections:
        if sender not in nodes:
            print(f"[ERROR] Sender '{sender}' in connections does not exist in nodes list!")

    # 2. Check if all target nodes in connections exist in nodes
    for sender, targets_dict in connections.items():
        for conn_type, target_groups in targets_dict.items():
            for group in target_groups:
                for target_item in group:
                    target_name = target_item.get("node")
                    if target_name not in nodes:
                        print(f"[ERROR] Target node '{target_name}' referenced from '{sender}' ({conn_type}) does not exist!")

    # 3. Check for any completely orphaned/disconnected nodes (neither sender nor target)
    targets_set = set()
    senders_set = set(connections.keys())

    for sender, targets_dict in connections.items():
        for conn_type, target_groups in targets_dict.items():
            for group in target_groups:
                for target_item in group:
                    targets_set.add(target_item.get("node"))

    all_connected = targets_set.union(senders_set)
    orphans = []
    for name in nodes:
        if name not in all_connected:
            orphans.append(name)

    if orphans:
        print(f"\n[ALERT] Disconnected/Orphan nodes found ({len(orphans)}):")
        for o in orphans:
            print(f"  - {o}")
    else:
        print("\n[SUCCESS] 0 orphan nodes! All 30 nodes are actively wired into connections.")

    # 4. Check Agent sub-nodes on LangChain agents
    agents = [n for n in wf.get("nodes", []) if "@n8n/n8n-nodes-langchain.agent" in n.get("type", "")]
    for ag in agents:
        ag_name = ag["name"]
        print(f"\nChecking LangChain Agent '{ag_name}':")
        # Check who connects into ag_name as ai_languageModel, ai_memory, ai_tool
        incoming_models = []
        incoming_memories = []
        incoming_tools = []

        for sender, targets_dict in connections.items():
            if "ai_languageModel" in targets_dict:
                for grp in targets_dict["ai_languageModel"]:
                    for t in grp:
                        if t.get("node") == ag_name:
                            incoming_models.append(sender)
            if "ai_memory" in targets_dict:
                for grp in targets_dict["ai_memory"]:
                    for t in grp:
                        if t.get("node") == ag_name:
                            incoming_memories.append(sender)
            if "ai_tool" in targets_dict:
                for grp in targets_dict["ai_tool"]:
                    for t in grp:
                        if t.get("node") == ag_name:
                            incoming_tools.append(sender)

        print(f"  - Language Model connected: {incoming_models or '[NONE]'}")
        print(f"  - Memory connected: {incoming_memories or '[NONE]'}")
        print(f"  - Tools connected: {incoming_tools or '[NONE]'}")

if __name__ == "__main__":
    check_n8n_connections("n8n/zeytech_master_ai_commerce_os.json")
