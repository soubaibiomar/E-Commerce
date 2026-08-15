const fs = require('fs');

const FILE_PATH = 'n8n/zeytech_master_ai_commerce_os.json';

function runDiagnostics() {
  console.log('====================================================================');
  console.log(' N8N WORKFLOW COMPREHENSIVE DIAGNOSTIC AUDIT');
  console.log('====================================================================\n');

  let rawContent;
  try {
    rawContent = fs.readFileSync(FILE_PATH, 'utf8');
    console.log(`[PASS] File exists and read successfully (${(rawContent.length / 1024).toFixed(2)} KB).`);
  } catch (e) {
    console.error(`[FAIL] Could not read file: ${e.message}`);
    return;
  }

  let workflow;
  try {
    workflow = JSON.parse(rawContent);
    console.log(`[PASS] JSON is strictly valid.`);
  } catch (e) {
    console.error(`[FAIL] JSON Parse Error: ${e.message}`);
    return;
  }

  console.log(`\nWorkflow Name: "${workflow.name}"`);
  console.log(`Total Nodes: ${workflow.nodes ? workflow.nodes.length : 0}`);
  console.log(`Total Connection Sources: ${Object.keys(workflow.connections || {}).length}`);

  const nodes = workflow.nodes || [];
  const connections = workflow.connections || {};

  const issues = [];
  const warnings = [];

  // 1. Check Node IDs and Names for Uniqueness
  const idMap = new Map();
  const nameMap = new Map();

  nodes.forEach((node, index) => {
    if (!node.id) {
      issues.push(`Node at index ${index} is missing an 'id'.`);
    } else if (idMap.has(node.id)) {
      issues.push(`Duplicate node id: "${node.id}" on "${node.name}" and "${idMap.get(node.id)}".`);
    } else {
      idMap.set(node.id, node.name);
    }

    if (!node.name) {
      issues.push(`Node at index ${index} is missing a 'name'.`);
    } else if (nameMap.has(node.name)) {
      issues.push(`Duplicate node name: "${node.name}".`);
    } else {
      nameMap.set(node.name, node);
    }

    // Check JS code syntax
    if (node.type === 'n8n-nodes-base.code' && node.parameters && node.parameters.jsCode) {
      try {
        new Function('items', '$input', '$node', '$json', '$', node.parameters.jsCode);
      } catch (err) {
        issues.push(`JS Syntax Error in Code Node "${node.name}": ${err.message}`);
      }
    }
  });

  // 2. Check Connections Validity
  const incomingCounts = new Map();
  const outgoingCounts = new Map();

  nodes.forEach(n => {
    incomingCounts.set(n.name, 0);
    outgoingCounts.set(n.name, 0);
  });

  for (const [sourceName, sourceConns] of Object.entries(connections)) {
    if (!nameMap.has(sourceName)) {
      issues.push(`Connection source "${sourceName}" does not exist in nodes array.`);
    }

    for (const [connType, outputs] of Object.entries(sourceConns)) {
      outputs.forEach((branch, branchIdx) => {
        branch.forEach((target, targetIdx) => {
          if (!nameMap.has(target.node)) {
            issues.push(`Connection target "${target.node}" (from "${sourceName}" [${connType}][${branchIdx}][${targetIdx}]) does not exist in nodes array.`);
          } else {
            incomingCounts.set(target.node, (incomingCounts.get(target.node) || 0) + 1);
            outgoingCounts.set(sourceName, (outgoingCounts.get(sourceName) || 0) + 1);
          }
        });
      });
    }
  }

  // 3. Check for Orphaned Nodes
  nodes.forEach(node => {
    const isTrigger = node.type.includes('webhook') || node.type.includes('chatTrigger') || node.type.includes('cron') || node.type.includes('errorTrigger');
    const isAiSubnode = node.type.includes('lmChat') || node.type.includes('memory') || node.type.includes('toolHttp');
    const inCount = incomingCounts.get(node.name) || 0;
    const outCount = outgoingCounts.get(node.name) || 0;

    if (isTrigger) {
      if (outCount === 0) {
        warnings.push(`Trigger node "${node.name}" has no outgoing connections.`);
      }
    } else if (isAiSubnode) {
      if (outCount === 0) {
        warnings.push(`AI Sub-node "${node.name}" is not attached to any Agent/Supervisor.`);
      }
    } else {
      if (inCount === 0) {
        warnings.push(`Node "${node.name}" has 0 incoming connections (unreachable).`);
      }
    }
  });

  // 4. Check Variable Reference Expressions $('Node Name')
  nodes.forEach(node => {
    const jsonStr = JSON.stringify(node.parameters || {});
    const refMatches = [...jsonStr.matchAll(/\$\(['"]([^'"]+)['"]\)/g)];
    refMatches.forEach(m => {
      const refNodeName = m[1];
      if (!nameMap.has(refNodeName)) {
        issues.push(`Node "${node.name}" references non-existent node: "$('${refNodeName}')".`);
      }
    });
  });

  // 5. Check Output Results
  console.log('\n--------------------------------------------------------------------');
  console.log(`CRITICAL ISSUES FOUND: ${issues.length}`);
  console.log(`WARNINGS / NOTICES: ${warnings.length}`);
  console.log('--------------------------------------------------------------------');

  if (issues.length > 0) {
    console.log('\n[!] CRITICAL ISSUES:');
    issues.forEach((iss, idx) => console.log(`  ${idx + 1}. ${iss}`));
  } else {
    console.log('\n[PASS] Zero critical issues found.');
  }

  if (warnings.length > 0) {
    console.log('\n[*] WARNINGS / RECOMMENDATIONS:');
    warnings.forEach((w, idx) => console.log(`  ${idx + 1}. ${w}`));
  }

  console.log('\n====================================================================');
}

runDiagnostics();
