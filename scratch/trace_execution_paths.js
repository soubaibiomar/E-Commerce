const fs = require('fs');

const workflow = JSON.parse(fs.readFileSync('n8n/zeytech_master_ai_commerce_os.json', 'utf8'));

console.log('=== WORKFLOW MULTI-HOP EXECUTION GRAPH TRACE ===\n');

const entryNodes = [
  '1a. AI Entry Gateway (Web)',
  '1d. Business Events Webhook Ingest',
  '1f. Global Platform Error Trigger',
  '1g. Payment Provider Webhook'
];

entryNodes.forEach((entryName, idx) => {
  console.log(`[Path ${idx + 1}] Tracing Entry Trigger: "${entryName}"`);
  
  const queue = [[entryName]];

  while (queue.length > 0) {
    const currentPath = queue.shift();
    const currentNode = currentPath[currentPath.length - 1];

    const sourceConns = workflow.connections[currentNode];
    if (!sourceConns || !sourceConns.main || sourceConns.main.length === 0) {
      console.log(`  ✓ Terminus: ${currentPath.join(' → ')}`);
      continue;
    }

    let hasOutgoing = false;
    sourceConns.main.forEach((branch, branchIdx) => {
      branch.forEach(target => {
        hasOutgoing = true;
        if (!currentPath.includes(target.node)) {
          queue.push([...currentPath, target.node]);
        }
      });
    });

    if (!hasOutgoing) {
      console.log(`  ✓ Terminus: ${currentPath.join(' → ')}`);
    }
  }
  console.log('');
});
