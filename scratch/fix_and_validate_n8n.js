const fs = require('fs');

const FILE_PATH = 'n8n/zeytech_master_ai_commerce_os.json';
const raw = fs.readFileSync(FILE_PATH, 'utf8');
const workflow = JSON.parse(raw);

console.log('Fixing workflow issues...');

// 1. Fix JS Syntax in Node 11c
const node11c = workflow.nodes.find(n => n.name === '11c. Dispatch Critical Admin Alert');
if (node11c && node11c.parameters && node11c.parameters.jsCode) {
  node11c.parameters.jsCode = `// Low-Noise Admin Telegram Alert (Gap 1)
const err = $('11a. Platform Error Extractor').first().json;
return [{
  json: {
    alertType: 'CRITICAL_PLATFORM_ERROR',
    text: "🚨 *ZEYTECH CRITICAL ALERT*\\n• Node: *" + err.nodeName + "*\\n• Severity: " + err.severity + "\\n• Error: " + err.errorMessage + "\\n• Trace ID: " + err.traceId,
    status: 'ALERT_DISPATCHED'
  }
}];`;
  console.log('Fixed JS syntax in node 11c.');
}

// 2. Add Node 1h: Verify Payment Webhook if not present
let node1h = workflow.nodes.find(n => n.name === '1h. Verify Payment Webhook');
if (!node1h) {
  node1h = {
    parameters: {
      method: 'POST',
      url: 'http://host.docker.internal:8085/api-payment-verify.php',
      sendBody: true,
      specifyBody: 'json',
      jsonBody: '={{ $json.body || $json }}'
    },
    id: 'verify-payment-webhook-node',
    name: '1h. Verify Payment Webhook',
    type: 'n8n-nodes-base.httpRequest',
    typeVersion: 4.2,
    position: [ 380, 1220 ]
  };
  workflow.nodes.push(node1h);
  console.log('Added node 1h. Verify Payment Webhook.');
}

// 3. Fix Connections for 1g -> 1h
workflow.connections['1g. Payment Provider Webhook'] = {
  main: [
    [
      {
        node: '1h. Verify Payment Webhook',
        type: 'main',
        index: 0
      }
    ]
  ]
};

// 4. Fix Connections for Rate Limit Gate -> Budget Check -> Budget Gate -> Supervisor / Human Queue
workflow.connections['2b. Rate Limit Gate'] = {
  main: [
    [
      {
        node: '2d. LLM Budget Guard Check',
        type: 'main',
        index: 0
      }
    ],
    [
      {
        node: '2c. Rate Limited Response',
        type: 'main',
        index: 0
      }
    ]
  ]
};

workflow.connections['2d. LLM Budget Guard Check'] = {
  main: [
    [
      {
        node: '2e. Budget Gate',
        type: 'main',
        index: 0
      }
    ]
  ]
};

workflow.connections['2e. Budget Gate'] = {
  main: [
    [
      {
        node: '3. AI SUPERVISOR / ROUTER',
        type: 'main',
        index: 0
      }
    ],
    [
      {
        node: '2f. Budget Exceeded — Human Queue',
        type: 'main',
        index: 0
      }
    ]
  ]
};

// Save cleanly formatted JSON
fs.writeFileSync(FILE_PATH, JSON.stringify(workflow, null, 2), 'utf8');
console.log('Saved updated workflow to', FILE_PATH);
