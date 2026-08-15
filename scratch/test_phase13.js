const BASE_URL = 'http://localhost:8085';

function logPass(msg) { console.log(`  [PASS] ${msg}`); }
function logFail(msg, detail) { console.error(`  [FAIL] ${msg} -> ${detail}`); }

async function runPhase13Tests() {
  console.log('====================================================================');
  console.log(' ZEYTECH AI COMMERCE OS — PHASE 13 (RECOMMENDATIONS & BUNDLES) TESTS');
  console.log('====================================================================\n');

  let passed = 0;
  let failed = 0;

  // 13.1 AI Recommendations & Smart Bundle Retrieval
  try {
    const res = await fetch(`${BASE_URL}/api-recommendations.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ productId: 1 })
    });
    const data = await res.json();
    if (data.success && data.smartBundle && data.smartBundle.bundleId === 1 && data.smartBundle.savingsMAD > 0) {
      logPass(`13.1 POST /api-recommendations.php calculates dynamic bundle with ${data.smartBundle.savingsMAD} MAD savings`);
      passed++;
    } else {
      logFail('13.1 Recommendations retrieval', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('13.1 Recommendations test', err.message);
    failed++;
  }

  // 13.2 Non-Existent Product Guard
  try {
    const res = await fetch(`${BASE_URL}/api-recommendations.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ productId: 999999 })
    });
    if (res.status === 404) {
      logPass('13.2 Non-existent product ID rejected with HTTP 404');
      passed++;
    } else {
      logFail('13.2 Product 404 guard', `Got status ${res.status}`);
      failed++;
    }
  } catch (err) {
    logFail('13.2 Product 404 test', err.message);
    failed++;
  }

  // 13.3 Dynamic Bundle Discount Application
  try {
    const res = await fetch(`${BASE_URL}/api-bundle-apply.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ bundleId: 1 })
    });
    const data = await res.json();
    if (data.success && data.appliedBundle && data.appliedBundle.discountPercentage > 0) {
      logPass(`13.3 POST /api-bundle-apply.php applies ${data.appliedBundle.discountPercentage}% discount to cart totals`);
      passed++;
    } else {
      logFail('13.3 Bundle apply execution', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('13.3 Bundle apply test', err.message);
    failed++;
  }

  // 13.4 Invalid Bundle Guard
  try {
    const res = await fetch(`${BASE_URL}/api-bundle-apply.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ bundleId: 99999 })
    });
    if (res.status === 404) {
      logPass('13.4 Invalid bundle ID rejected with HTTP 404');
      passed++;
    } else {
      logFail('13.4 Invalid bundle guard', `Got status ${res.status}`);
      failed++;
    }
  } catch (err) {
    logFail('13.4 Invalid bundle test', err.message);
    failed++;
  }

  console.log('\n====================================================================');
  console.log(` PHASE 13 TEST RESULTS: ${passed} PASSED, ${failed} FAILED`);
  console.log('====================================================================');

  if (failed > 0) process.exit(1);
}

runPhase13Tests();
