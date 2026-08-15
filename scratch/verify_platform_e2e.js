const http = require('http');

async function get(url) {
  return new Promise((resolve, reject) => {
    http.get(url, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => resolve({ status: res.statusCode, headers: res.headers, body: data }));
    }).on('error', err => reject(err));
  });
}

async function post(url, payload) {
  return new Promise((resolve, reject) => {
    const dataString = JSON.stringify(payload);
    const u = new URL(url);
    const options = {
      hostname: u.hostname,
      port: u.port,
      path: u.pathname,
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Content-Length': Buffer.byteLength(dataString)
      }
    };
    const req = http.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => resolve({ status: res.statusCode, headers: res.headers, body: data }));
    });
    req.on('error', err => reject(err));
    req.write(dataString);
    req.end();
  });
}

async function runE2E() {
  console.log("===============================================================================");
  console.log("🚀 STARTING E2E VERIFICATION SUITE — ZEYTECH AI COMMERCE OS");
  console.log("===============================================================================\n");

  let passed = 0;
  let total = 0;

  function assert(name, condition, details = "") {
    total++;
    if (condition) {
      console.log(`✅ [PASS] ${name}`);
      passed++;
    } else {
      console.error(`❌ [FAIL] ${name} — ${details}`);
    }
  }

  // 1. Next.js 14 Storefront Live
  try {
    const res = await get('http://localhost:3000/');
    assert('Next.js 14 Storefront serves HTTP 200', res.status === 200);
    assert('Next.js 14 HTML contains ZeyTech branding', res.body.includes('ZeyTech') || res.body.includes('Loop'));
  } catch (e) {
    assert('Next.js 14 Storefront connectivity', false, e.message);
  }

  // 2. Next.js Products API
  try {
    const res = await get('http://localhost:3000/api/products');
    assert('Products API /api/products returns HTTP 200', res.status === 200);
    const products = JSON.parse(res.body);
    assert('Products catalog contains seeded items', Array.isArray(products) && products.length > 0, `Count: ${products.length}`);
  } catch (e) {
    assert('Products API /api/products validation', false, e.message);
  }

  // 3. AI Analytics / Observability API
  try {
    const res = await get('http://localhost:3000/api/analytics/observability');
    assert('AI Observability API returns HTTP 200', res.status === 200);
  } catch (e) {
    assert('AI Observability API check', false, e.message);
  }

  // 4. Autonomous Daily AI Report Generation Endpoint
  try {
    const res = await post('http://localhost:3000/api/ai/autonomous/daily-report', {});
    assert('Autonomous Daily AI Report endpoint returns HTTP 200', res.status === 200);
    const json = JSON.parse(res.body);
    assert('Daily report contains actionable recommendations', json.success === true && json.report && json.report.actionableRecommendations.length > 0);
  } catch (e) {
    assert('Daily Report generation check', false, e.message);
  }

  // 5. PHP 8.2 / MariaDB Storefront Live
  try {
    const res = await get('http://localhost:8085/');
    assert('PHP Storefront (port 8085) serves HTTP 200', res.status === 200);
    assert('PHP Storefront contains Content-Security-Policy header', !!res.headers['content-security-policy']);
  } catch (e) {
    assert('PHP Storefront connectivity', false, e.message);
  }

  // 6. PHP Dashboard KPIs API
  try {
    const res = await get('http://localhost:8085/api-dashboard-kpis.php');
    assert('PHP Merchant KPI API returns HTTP 200', res.status === 200);
    const json = JSON.parse(res.body);
    assert('PHP KPIs contains valid JSON metrics', json && typeof json === 'object');
  } catch (e) {
    assert('PHP Dashboard KPIs check', false, e.message);
  }

  console.log("\n===============================================================================");
  console.log(`📊 FINAL RESULT: ${passed} / ${total} TESTS PASSED (${Math.round((passed / total) * 100)}%)`);
  console.log("===============================================================================\n");
}

runE2E();