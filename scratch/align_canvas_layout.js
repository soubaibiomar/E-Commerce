const fs = require('fs');

const FILE_PATH = 'n8n/zeytech_master_ai_commerce_os.json';
const raw = fs.readFileSync(FILE_PATH, 'utf8');
const workflow = JSON.parse(raw);

console.log('Aligning canvas node coordinates for clean, uncluttered visual layout...');

// Define optimal visual positions for every node
const positions = {
  // --- INBOUND TRIGGERS (LANE 1) ---
  '1a. AI Entry Gateway (Web)': [ 100, 160 ],
  '1b. Interactive Chat Trigger': [ 100, 280 ],
  '1c. Telegram / WhatsApp Inbound Webhook': [ 100, 400 ],

  // --- GATEWAY SECURITY & RATE LIMITING & BUDGET GUARD ---
  '2. Unified Inbound Envelope & Guardrails': [ 360, 280 ],
  '2a. Rate Limit Check': [ 600, 280 ],
  '2b. Rate Limit Gate': [ 840, 280 ],
  '2c. Rate Limited Response': [ 1080, 160 ],
  '2d. LLM Budget Guard Check': [ 1080, 280 ],
  '2e. Budget Gate': [ 1320, 280 ],
  '2f. Budget Exceeded — Human Queue': [ 1560, 160 ],

  // --- SUPERVISOR & AI ENGINE ---
  '3. AI SUPERVISOR / ROUTER': [ 1560, 280 ],
  '4. Ollama Local LLM / Cloud Engine': [ 1400, 480 ],
  '5. Short & Long Context Memory': [ 1560, 480 ],

  // --- CONTROLLED TOOLS (Vertical stack under Supervisor) ---
  '6a. Tool: queryCommerceDatabase': [ 1760, 360 ],
  '6b. Tool: manageInventoryReservation': [ 1760, 440 ],
  '6c. Tool: verifyCustomerIdentity': [ 1760, 520 ],
  '6d. Tool: processOrderException': [ 1760, 600 ],

  // --- 14 SPECIALIZED AGENTS (Column 1: Agents 1-7) ---
  'Agent 1: Sales': [ 2000, 60 ],
  'Agent 2: Customer Support': [ 2000, 130 ],
  'Agent 3: Product Expert': [ 2000, 200 ],
  'Agent 4: Recommendations': [ 2000, 270 ],
  'Agent 5: Order Management': [ 2000, 340 ],
  'Agent 6: Inventory': [ 2000, 410 ],
  'Agent 7: Pricing & Promo': [ 2000, 480 ],

  // --- 14 SPECIALIZED AGENTS (Column 2: Agents 8-14) ---
  'Agent 8: Marketing': [ 2240, 60 ],
  'Agent 9: Analytics': [ 2240, 130 ],
  'Agent 10: Forecasting': [ 2240, 200 ],
  'Agent 11: Fraud Detection': [ 2240, 270 ],
  'Agent 12: CRM & Retention': [ 2240, 340 ],
  'Agent 13: Content Generation': [ 2240, 410 ],
  'Agent 14: Admin Copilot': [ 2240, 480 ],

  // --- SUPERVISOR OUTPUT EVALUATOR & DECISION GATES ---
  '7. Supervisor Output Evaluator': [ 2000, 640 ],
  '7a. HITL Support Escalation Queue': [ 2280, 580 ],
  '7b. Manager Approval Gate (> 5000 MAD)': [ 2280, 660 ],
  '7c. Audit Log Writer': [ 2280, 740 ],

  // --- LANE 2: BUSINESS EVENTS & 24H CRON REPORT ---
  '1d. Business Events Webhook Ingest': [ 100, 840 ],
  '1e. 24h Autonomous Daily Report Cron Trigger': [ 100, 960 ],
  '8. Central Event Processor & Deduplication Router': [ 360, 900 ],
  '8a. Idempotency Check': [ 600, 900 ],
  '8b. Idempotency Gate': [ 840, 900 ],
  '8c. Duplicate Event Discarded': [ 1080, 800 ],
  '10. Fetch Live Business Metrics': [ 1080, 920 ],
  'Agent 15: Dedicated Notification Agent': [ 1320, 920 ],
  '9a. Omnichannel Telegram Dispatcher': [ 1560, 860 ],
  '9b. Omnichannel WhatsApp Dispatcher': [ 1560, 980 ],

  // --- LANE 3: GLOBAL PLATFORM ERRORS ---
  '1f. Global Platform Error Trigger': [ 100, 1140 ],
  '11a. Platform Error Extractor': [ 360, 1140 ],
  '11b. Write to Platform Error Logs': [ 600, 1140 ],
  '11c. Dispatch Critical Admin Alert': [ 840, 1140 ],

  // --- LANE 4: PAYMENT PROVIDER WEBHOOK ---
  '1g. Payment Provider Webhook': [ 100, 1300 ],
  '1h. Verify Payment Webhook': [ 360, 1300 ]
};

// Apply positions
workflow.nodes.forEach(node => {
  if (positions[node.name]) {
    node.position = positions[node.name];
  }
});

// Ensure connections are strictly clean and sequential
workflow.connections = {
  // 1a, 1b, 1c -> 2
  "1a. AI Entry Gateway (Web)": {
    "main": [ [ { "node": "2. Unified Inbound Envelope & Guardrails", "type": "main", "index": 0 } ] ]
  },
  "1b. Interactive Chat Trigger": {
    "main": [ [ { "node": "2. Unified Inbound Envelope & Guardrails", "type": "main", "index": 0 } ] ]
  },
  "1c. Telegram / WhatsApp Inbound Webhook": {
    "main": [ [ { "node": "2. Unified Inbound Envelope & Guardrails", "type": "main", "index": 0 } ] ]
  },

  // 2 -> 2a -> 2b (Rate Limit)
  "2. Unified Inbound Envelope & Guardrails": {
    "main": [ [ { "node": "2a. Rate Limit Check", "type": "main", "index": 0 } ] ]
  },
  "2a. Rate Limit Check": {
    "main": [ [ { "node": "2b. Rate Limit Gate", "type": "main", "index": 0 } ] ]
  },
  "2b. Rate Limit Gate": {
    "main": [
      [ { "node": "2d. LLM Budget Guard Check", "type": "main", "index": 0 } ],
      [ { "node": "2c. Rate Limited Response", "type": "main", "index": 0 } ]
    ]
  },

  // 2d -> 2e (Budget Guard)
  "2d. LLM Budget Guard Check": {
    "main": [ [ { "node": "2e. Budget Gate", "type": "main", "index": 0 } ] ]
  },
  "2e. Budget Gate": {
    "main": [
      [ { "node": "3. AI SUPERVISOR / ROUTER", "type": "main", "index": 0 } ],
      [ { "node": "2f. Budget Exceeded — Human Queue", "type": "main", "index": 0 } ]
    ]
  },

  // AI Engine attachments to Supervisor
  "4. Ollama Local LLM / Cloud Engine": {
    "ai_languageModel": [ [ { "node": "3. AI SUPERVISOR / ROUTER", "type": "ai_languageModel", "index": 0 } ] ]
  },
  "5. Short & Long Context Memory": {
    "ai_memory": [ [ { "node": "3. AI SUPERVISOR / ROUTER", "type": "ai_memory", "index": 0 } ] ]
  },
  "6a. Tool: queryCommerceDatabase": {
    "ai_tool": [ [ { "node": "3. AI SUPERVISOR / ROUTER", "type": "ai_tool", "index": 0 } ] ]
  },
  "6b. Tool: manageInventoryReservation": {
    "ai_tool": [ [ { "node": "3. AI SUPERVISOR / ROUTER", "type": "ai_tool", "index": 0 } ] ]
  },
  "6c. Tool: verifyCustomerIdentity": {
    "ai_tool": [ [ { "node": "3. AI SUPERVISOR / ROUTER", "type": "ai_tool", "index": 0 } ] ]
  },
  "6d. Tool: processOrderException": {
    "ai_tool": [ [ { "node": "3. AI SUPERVISOR / ROUTER", "type": "ai_tool", "index": 0 } ] ]
  },

  // Supervisor -> 14 Agents & Evaluator
  "3. AI SUPERVISOR / ROUTER": {
    "main": [
      [
        { "node": "Agent 1: Sales", "type": "main", "index": 0 },
        { "node": "Agent 2: Customer Support", "type": "main", "index": 0 },
        { "node": "Agent 3: Product Expert", "type": "main", "index": 0 },
        { "node": "Agent 4: Recommendations", "type": "main", "index": 0 },
        { "node": "Agent 5: Order Management", "type": "main", "index": 0 },
        { "node": "Agent 6: Inventory", "type": "main", "index": 0 },
        { "node": "Agent 7: Pricing & Promo", "type": "main", "index": 0 },
        { "node": "Agent 8: Marketing", "type": "main", "index": 0 },
        { "node": "Agent 9: Analytics", "type": "main", "index": 0 },
        { "node": "Agent 10: Forecasting", "type": "main", "index": 0 },
        { "node": "Agent 11: Fraud Detection", "type": "main", "index": 0 },
        { "node": "Agent 12: CRM & Retention", "type": "main", "index": 0 },
        { "node": "Agent 13: Content Generation", "type": "main", "index": 0 },
        { "node": "Agent 14: Admin Copilot", "type": "main", "index": 0 },
        { "node": "7. Supervisor Output Evaluator", "type": "main", "index": 0 }
      ]
    ]
  },

  // Output Evaluator -> HITL, Manager Gate, Audit Log Writer
  "7. Supervisor Output Evaluator": {
    "main": [
      [
        { "node": "7a. HITL Support Escalation Queue", "type": "main", "index": 0 },
        { "node": "7b. Manager Approval Gate (> 5000 MAD)", "type": "main", "index": 0 },
        { "node": "7c. Audit Log Writer", "type": "main", "index": 0 }
      ]
    ]
  },

  // Lane 2: Business Events -> Router -> Idempotency -> KPIs -> Notification Agent -> Dispatchers
  "1d. Business Events Webhook Ingest": {
    "main": [ [ { "node": "8. Central Event Processor & Deduplication Router", "type": "main", "index": 0 } ] ]
  },
  "1e. 24h Autonomous Daily Report Cron Trigger": {
    "main": [ [ { "node": "8. Central Event Processor & Deduplication Router", "type": "main", "index": 0 } ] ]
  },
  "8. Central Event Processor & Deduplication Router": {
    "main": [ [ { "node": "8a. Idempotency Check", "type": "main", "index": 0 } ] ]
  },
  "8a. Idempotency Check": {
    "main": [ [ { "node": "8b. Idempotency Gate", "type": "main", "index": 0 } ] ]
  },
  "8b. Idempotency Gate": {
    "main": [
      [ { "node": "10. Fetch Live Business Metrics", "type": "main", "index": 0 } ],
      [ { "node": "8c. Duplicate Event Discarded", "type": "main", "index": 0 } ]
    ]
  },
  "10. Fetch Live Business Metrics": {
    "main": [ [ { "node": "Agent 15: Dedicated Notification Agent", "type": "main", "index": 0 } ] ]
  },
  "Agent 15: Dedicated Notification Agent": {
    "main": [
      [
        { "node": "9a. Omnichannel Telegram Dispatcher", "type": "main", "index": 0 },
        { "node": "9b. Omnichannel WhatsApp Dispatcher", "type": "main", "index": 0 }
      ]
    ]
  },

  // Lane 3: Global Error Trigger -> Extractor -> DB Log & Telegram Alert
  "1f. Global Platform Error Trigger": {
    "main": [ [ { "node": "11a. Platform Error Extractor", "type": "main", "index": 0 } ] ]
  },
  "11a. Platform Error Extractor": {
    "main": [
      [
        { "node": "11b. Write to Platform Error Logs", "type": "main", "index": 0 },
        { "node": "11c. Dispatch Critical Admin Alert", "type": "main", "index": 0 }
      ]
    ]
  },

  // Lane 4: Payment Provider Webhook -> Verification
  "1g. Payment Provider Webhook": {
    "main": [ [ { "node": "1h. Verify Payment Webhook", "type": "main", "index": 0 } ] ]
  }
};

fs.writeFileSync(FILE_PATH, JSON.stringify(workflow, null, 2), 'utf8');
console.log('Saved beautifully aligned canvas workflow to', FILE_PATH);
