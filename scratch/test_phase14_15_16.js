const BASE_URL = 'http://localhost:8085';

function logPass(msg) { console.log(`  [PASS] ${msg}`); }
function logFail(msg, detail) { console.error(`  [FAIL] ${msg} -> ${detail}`); }

async function loginAdmin() {
  const res = await fetch(`${BASE_URL}/api-auth-login.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: 'admin@zeytech.com', password: 'AdminPassword2026!' })
  });
  const data = await res.json();
  return data.token;
}

async function runPhases14_15_16Tests() {
  console.log('====================================================================');
  console.log(' ZEYTECH AI COMMERCE OS — PHASES 14, 15, 16 VERIFICATION');
  console.log('====================================================================\n');

  let passed = 0;
  let failed = 0;

  // [1] PHASE 14: Automated Fraud Scoring & Risk Engine
  console.log('[1] PHASE 14: Automated Fraud Scoring & Risk Engine');
  try {
    const res = await fetch(`${BASE_URL}/api-fraud-score.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        orderId: 1,
        amountMAD: 450.00,
        failedAttempts: 0,
        isNewDevice: false
      })
    });
    const data = await res.json();
    if (data.success && data.riskScore < 25 && data.actionTaken === 'AUTO_APPROVED') {
      logPass(`14.1 Nominal transaction scored as ${data.riskLevel} (Score: ${data.riskScore}/100) -> AUTO_APPROVED`);
      passed++;
    } else {
      logFail('14.1 Nominal fraud score', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('14.1 Nominal fraud score test', err.message);
    failed++;
  }

  try {
    const res = await fetch(`${BASE_URL}/api-fraud-score.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        orderId: 99,
        amountMAD: 12500.00,
        failedAttempts: 4,
        isNewDevice: true
      })
    });
    const data = await res.json();
    if (data.success && data.riskScore >= 75 && data.actionTaken === 'FLAGGED_FOR_REVIEW') {
      logPass(`14.2 High-risk transaction scored as ${data.riskLevel} (Score: ${data.riskScore}/100) -> FLAGGED_FOR_REVIEW`);
      passed++;
    } else {
      logFail('14.2 High-risk fraud score', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('14.2 High-risk fraud score test', err.message);
    failed++;
  }

  // [2] PHASE 15: Demand Forecasting & Stockout Predictor
  console.log('\n[2] PHASE 15: Demand Forecasting & Automated Reorders');
  try {
    const res = await fetch(`${BASE_URL}/api-forecasting-insights.php`);
    const data = await res.json();
    if (data.success && data.totalProductsAnalyzed > 0 && Array.isArray(data.insights)) {
      logPass(`15.1 GET /api-forecasting-insights.php analyzed ${data.totalProductsAnalyzed} products with days-to-stockout run-rate`);
      passed++;
    } else {
      logFail('15.1 Forecasting insights', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('15.1 Forecasting insights test', err.message);
    failed++;
  }

  try {
    const adminToken = await loginAdmin();
    const res = await fetch(`${BASE_URL}/api-forecasting-reorder.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${adminToken}`
      },
      body: JSON.stringify({
        productId: 1,
        quantity: 20,
        supplierName: 'ZeyTech Flagship Supplier',
        costMAD: 25000.00
      })
    });
    const data = await res.json();
    if (data.success && data.quantityRestocked === 20 && data.reorderId > 0) {
      logPass(`15.2 POST /api-forecasting-reorder.php created PO #${data.reorderId} and incremented available inventory`);
      passed++;
    } else {
      logFail('15.2 Stock reorder execution', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('15.2 Stock reorder test', err.message);
    failed++;
  }

  // [3] PHASE 16: Customer RFM Segmentation & Campaign Dispatch
  console.log('\n[3] PHASE 16: Customer RFM Segmentation & Campaigns');
  try {
    const res = await fetch(`${BASE_URL}/api-crm-segmentation.php`);
    const data = await res.json();
    if (data.success && data.segmentBreakdown && typeof data.totalAudience === 'number') {
      logPass(`16.1 GET /api-crm-segmentation.php classified audience into RFM segments (VIP: ${data.segmentBreakdown.VIP_HIGH_SPEND})`);
      passed++;
    } else {
      logFail('16.1 CRM segmentation', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('16.1 CRM segmentation test', err.message);
    failed++;
  }

  try {
    const adminToken = await loginAdmin();
    const res = await fetch(`${BASE_URL}/api-crm-campaign-dispatch.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${adminToken}`
      },
      body: JSON.stringify({
        campaignName: 'Summer Tech Deals Casablanca',
        targetSegment: 'VIP_HIGH_SPEND',
        discountPercentage: 20.00,
        channel: 'WHATSAPP'
      })
    });
    const data = await res.json();
    if (data.success && data.promoCode && data.campaignId > 0) {
      logPass(`16.2 POST /api-crm-campaign-dispatch.php generated voucher ${data.promoCode} and queued ${data.messagesQueued} messages`);
      passed++;
    } else {
      logFail('16.2 Campaign dispatch', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('16.2 Campaign dispatch test', err.message);
    failed++;
  }

  console.log('\n====================================================================');
  console.log(` PHASES 14-16 TEST RESULTS: ${passed} PASSED, ${failed} FAILED`);
  console.log('====================================================================');

  if (failed > 0) process.exit(1);
}

runPhases14_15_16Tests();
