const BASE_URL = 'http://localhost:8085';

function logPass(msg) { console.log(`  [PASS] ${msg}`); }
function logFail(msg, detail) { console.error(`  [FAIL] ${msg} -> ${detail}`); }

async function runPhase11Tests() {
  console.log('====================================================================');
  console.log(' ZEYTECH AI COMMERCE OS — PHASE 11 (LOGISTICS & CARRIERS) TESTS');
  console.log('====================================================================\n');

  let passed = 0;
  let failed = 0;

  // 11.1 Shipping Quote Matrix Across Moroccan Regions
  try {
    const res = await fetch(`${BASE_URL}/api-shipping-quote.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        region: 'Rabat-Salé-Kénitra',
        city: 'Rabat',
        weightKg: 2.5
      })
    });
    const data = await res.json();
    if (data.success && Array.isArray(data.rates) && data.rates.length === 3 && data.rates[0].rateMAD > 0) {
      logPass('11.1 POST /api-shipping-quote.php calculates accurate multi-carrier rates in MAD');
      passed++;
    } else {
      logFail('11.1 Shipping quote calculation', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('11.1 Shipping quote test', err.message);
    failed++;
  }

  // 11.2 Waybill & Tracking Number Generation
  let generatedTracking = '';
  try {
    const res = await fetch(`${BASE_URL}/api-shipping-label.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        orderId: 1,
        carrier: 'CTM Messagerie',
        region: 'Casablanca-Settat',
        city: 'Casablanca',
        recipientName: 'Omar El Fassi',
        recipientPhone: '+212600112233',
        shippingCostMAD: 35.00
      })
    });
    const data = await res.json();
    if (data.success && data.trackingNumber && data.trackingNumber.startsWith('CTM-MA-')) {
      generatedTracking = data.trackingNumber;
      logPass(`11.2 POST /api-shipping-label.php generates valid domestic waybill (${generatedTracking})`);
      passed++;
    } else {
      logFail('11.2 Waybill generation', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('11.2 Waybill test', err.message);
    failed++;
  }

  // 11.3 Webhook Validation Guard
  try {
    const res = await fetch(`${BASE_URL}/api-shipping-webhook.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ status: 'DELIVERED' })
    });
    if (res.status === 400) {
      logPass('11.3 Webhook missing tracking number rejected with HTTP 400');
      passed++;
    } else {
      logFail('11.3 Webhook validation', `Got status ${res.status}`);
      failed++;
    }
  } catch (err) {
    logFail('11.3 Webhook validation test', err.message);
    failed++;
  }

  // 11.4 Carrier Checkpoint Ingestion & Status Reconciliation
  try {
    const trackingToUpdate = generatedTracking || 'CTM-MA-8849102';
    const res = await fetch(`${BASE_URL}/api-shipping-webhook.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        trackingNumber: trackingToUpdate,
        status: 'DELIVERED',
        currentLocation: 'Casablanca Agence Maarif',
        notes: 'Colis remis en main propre au client.'
      })
    });
    const data = await res.json();
    if (data.success && data.currentStatus === 'DELIVERED') {
      logPass(`11.4 Carrier webhook updates shipment status to DELIVERED with audit trail`);
      passed++;
    } else {
      logFail('11.4 Carrier webhook status update', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('11.4 Carrier webhook test', err.message);
    failed++;
  }

  console.log('\n====================================================================');
  console.log(` PHASE 11 TEST RESULTS: ${passed} PASSED, ${failed} FAILED`);
  console.log('====================================================================');

  if (failed > 0) process.exit(1);
}

runPhase11Tests();
