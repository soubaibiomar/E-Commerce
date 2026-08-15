const fs = require('fs');

const raw = fs.readFileSync('n8n/zeytech_master_ai_commerce_os.json', 'utf8');

// Replace any invalid escape like \` with `
let fixed = raw.split('\\`').join('`');

try {
  const parsed = JSON.parse(fixed);
  console.log('JSON Parse SUCCESS!');
  console.log('Node count:', parsed.nodes ? parsed.nodes.length : 0);
  console.log('Connection count:', Object.keys(parsed.connections || {}).length);
  
  // Format with standard 2 spaces
  const cleanJson = JSON.stringify(parsed, null, 2);
  fs.writeFileSync('n8n/zeytech_master_ai_commerce_os.json', cleanJson, 'utf8');
  console.log('Saved clean valid JSON file: n8n/zeytech_master_ai_commerce_os.json');
} catch (e) {
  console.error('Parse failed:', e.message);
}
