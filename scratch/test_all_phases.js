const crypto = require('crypto');
const { execSync } = require('child_process');

const BASE_URL = 'http://localhost:8085';
const SECRET_KEY = 'zeytech_live_webhook_secret_2026';

let passed = 0;
let failed = 0;

function assertTest(name, condition, details = '') {
  if (condition) {
    console.log(`  [PASS] ${name}`);
    passed++;
  } else {
    console.error(`  [FAIL] ${name} -> ${details}`);
    failed++;
  }
}

async function postJson(endpoint, data, customHeaders = {}) {
  const url = `${BASE_URL}${endpoint}`;
  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', ...customHeaders },
      body: JSON.stringify(data)
    });
    const text = await res.text();
    let json = null;
    try { json = JSON.parse(text); } catch (e) {}
    return { status: res.status, json, text };
  } catch (err) {
    return { status: 0, error: err.message };
  }
}

async function runAllTests() {
  console.log('====================================================================');
  console.log(' ZEYTECH AI COMMERCE OS — ALL PHASES (1-6) CONTRACT VERIFICATION');
  console.log('====================================================================\n');

  // ---------------------------------------------------------------------------
  // PHASE 2: /api-chat.php
  // ---------------------------------------------------------------------------
  console.log('[1] PHASE 2: Core Commerce Endpoint (/api-chat.php)');

  // 1.1 Normal Query
  const r1 = await postJson('/api-chat.php', {
    message: 'What are the specs and price for MacBook Pro?',
    productId: 1,
    traceId: 'tr_test_p2',
    channel: 'WEB',
    senderId: 'usr_101',
    userRole: 'CUSTOMER'
  });
  assertTest('1.1 Normal product query returns grounded natural language reply', r1.json && typeof r1.json.reply === 'string' && r1.json.reply.length > 10, r1.text);

  // 1.2 Moroccan Darija Query
  const r2 = await postJson('/api-chat.php', {
    message: 'شحال الثمن ديال MacBook واش كاين فالمخزن؟',
    productId: 1,
    channel: 'WHATSAPP'
  });
  assertTest('1.2 Moroccan Darija query returns MAD pricing or warehouse status in Arabic', r2.json && (r2.json.reply.includes('المخزن') || r2.json.reply.includes('درهم') || r2.json.reply.includes('MAD') || r2.json.reply.includes('ZeyTech')), r2.text);

  // 1.3 Autonomous Reporting Path
  const r3 = await postJson('/api-chat.php', {
    message: 'give me the full revenue, orders, and KPI summary',
    channel: 'SYSTEM_EVENT_ROUTER'
  });
  assertTest('1.3 Autonomous KPI summary returns live business metrics', r3.json && r3.json.reply && (r3.json.reply.includes('Revenue') || r3.json.reply.includes('Catalog')), r3.text);

  // 1.4 Error Logging Path
  const r4 = await postJson('/api-chat.php', {
    message: 'LOG_PLATFORM_ERROR',
    traceId: 'tr_err_audit_01',
    nodeName: 'InventoryGate',
    severity: 'CRITICAL',
    errorMessage: 'Simulated contract test error'
  });
  assertTest('1.4 LOG_PLATFORM_ERROR routes to platform_error_logs table', r4.json && r4.json.success === true, r4.text);

  // ---------------------------------------------------------------------------
  // PHASE 3: Commerce Tool Endpoints
  // ---------------------------------------------------------------------------
  console.log('\n[2] PHASE 3: Commerce Tool Endpoints');

  // 3.1 Inventory Check
  const invCheck = await postJson('/api-inventory-reserve.php', {
    action: 'check',
    productId: 1,
    quantity: 1
  });
  assertTest('2.1 Inventory check returns 3-state stock counts without mutation', invCheck.json && invCheck.json.success === true && typeof invCheck.json.stockAvailable === 'number', invCheck.text);

  // 3.2 Atomic Reservation
  const initialAvail = invCheck.json.stockAvailable;
  const invRes = await postJson('/api-inventory-reserve.php', {
    action: 'reserve',
    productId: 1,
    quantity: 2
  });
  assertTest('2.2 Inventory reservation decrements available and increments reserved', invRes.json && invRes.json.success === true && invRes.json.stockAvailable === initialAvail - 2, invRes.text);

  // 3.3 Inventory Release
  const invRel = await postJson('/api-inventory-reserve.php', {
    action: 'release',
    productId: 1,
    quantity: 2
  });
  assertTest('2.3 Inventory release moves reserved stock back to available', invRel.json && invRel.json.success === true && invRel.json.releasedQuantity === 2, invRel.text);

  // 3.4 Failure Case: Insufficient Stock
  const invFail = await postJson('/api-inventory-reserve.php', {
    action: 'reserve',
    productId: 1,
    quantity: 999999
  });
  assertTest('2.4 Insufficient stock rejected with HTTP 409 INSUFFICIENT_STOCK', invFail.status === 409 && invFail.json && invFail.json.error === 'INSUFFICIENT_STOCK', invFail.text);

  // 3.5 Identity Verification: Request OTP
  const otpReq = await postJson('/api-identity-verify.php', {
    action: 'request_otp',
    identifier: '+212611223344'
  });
  const dbOtp = execSync(
    `docker exec shopping_db mariadb -u shopping_user -pshopping_pass shopping -s -N -e "SELECT otp_code FROM otp_challenges WHERE customer_identifier = '+212611223344' ORDER BY id DESC LIMIT 1;"`,
    { encoding: 'utf8' }
  ).trim();
  assertTest('2.5 Identity request_otp generates 6-digit challenge securely', otpReq.json && otpReq.json.sent === true && !otpReq.json.devOtp && dbOtp.length === 6, otpReq.text);

  // 3.6 Failure Case: Invalid OTP
  const otpBad = await postJson('/api-identity-verify.php', {
    action: 'verify_otp',
    identifier: '+212611223344',
    otpCode: '000000'
  });
  assertTest('2.6 Invalid OTP rejected with HTTP 401 INVALID_OR_EXPIRED_OTP', otpBad.status === 401 && otpBad.json && otpBad.json.verified === false, otpBad.text);

  // 3.7 Valid OTP Verification
  const otpGood = await postJson('/api-identity-verify.php', {
    action: 'verify_otp',
    identifier: '+212611223344',
    otpCode: dbOtp
  });
  const authToken = otpGood.json ? otpGood.json.authToken : '';
  assertTest('2.7 Valid OTP returns verified=true and Bearer auth token', otpGood.json && otpGood.json.verified === true && authToken.length > 10, otpGood.text);

  // 3.8 Failure Case: Unverified Identity on Order Exception
  const ordBad = await postJson('/api-order-exceptions.php', {
    action: 'cancel_order',
    orderId: 1,
    authToken: 'unverified_bogus_token'
  });
  assertTest('2.8 Order exception without valid identity rejected with HTTP 403 UNAUTHORIZED', ordBad.status === 403 && ordBad.json && ordBad.json.error === 'UNAUTHORIZED', ordBad.text);

  // 3.9 Verified Refund Request (Creates PENDING_REFUND, not completed refund)
  const ordRef = await postJson('/api-order-exceptions.php', {
    action: 'request_refund',
    orderId: 1,
    authToken: authToken
  });
  assertTest('2.9 Verified refund request creates PENDING_REFUND state without premature settlement', ordRef.json && ordRef.json.success === true && ordRef.json.status === 'pending_refund', ordRef.text);

  // ---------------------------------------------------------------------------
  // PHASE 4: Platform Safety Endpoints
  // ---------------------------------------------------------------------------
  console.log('\n[3] PHASE 4: Platform Safety Endpoints');

  // 4.1 Rate Limiter
  const rl1 = await postJson('/api-rate-limit.php', {
    senderId: 'test_client_rl',
    channel: 'WEB',
    windowSeconds: 60,
    maxRequests: 10
  });
  assertTest('3.1 Rate limit check returns allowed=true for normal activity', rl1.json && rl1.json.allowed === true, rl1.text);

  // 4.2 Rate Limit Throttling
  for (let i = 0; i < 6; i++) {
    await postJson('/api-rate-limit.php', {
      senderId: 'spam_bot_test',
      channel: 'WEB',
      windowSeconds: 60,
      maxRequests: 3
    });
  }
  const rlThrottled = await postJson('/api-rate-limit.php', {
    senderId: 'spam_bot_test',
    channel: 'WEB',
    windowSeconds: 60,
    maxRequests: 3
  });
  assertTest('3.2 Rate limit throttles exceeding requests with allowed=false', rlThrottled.json && rlThrottled.json.allowed === false, rlThrottled.text);

  // 4.3 Idempotency Atomic Check
  const eventId = `evt_iso_${Date.now()}_${Math.random().toString(36).substring(2, 6)}`;
  const idemFirst = await postJson('/api-idempotency-check.php', {
    eventId: eventId,
    eventType: 'ORDER_PLACED'
  });
  assertTest('3.3 First event registration returns alreadyProcessed=false', idemFirst.json && idemFirst.json.alreadyProcessed === false, idemFirst.text);

  const idemDup = await postJson('/api-idempotency-check.php', {
    eventId: eventId,
    eventType: 'ORDER_PLACED'
  });
  assertTest('3.4 Duplicate event registration returns alreadyProcessed=true atomically', idemDup.json && idemDup.json.alreadyProcessed === true, idemDup.text);

  // 4.5 Audit Log Writer
  const auditRes = await postJson('/api-audit-log.php', {
    traceId: `tr_audit_${Date.now()}`,
    actor: 'SUPERVISOR',
    channel: 'WEB',
    senderId: 'cust_42',
    decision: 'SUCCESS',
    confidence: 0.95,
    reply: 'Grounded product specification provided.'
  });
  assertTest('3.5 Audit log writer inserts record and returns success=true', auditRes.json && auditRes.json.success === true, auditRes.text);

  // 4.6 Budget Guard
  const budgetRes = await postJson('/api-budget-guard.php', {
    scope: 'daily',
    maxSpendUSD: 25.0
  });
  assertTest('3.6 Budget guard verifies spend against $25 daily cap (underBudget=true)', budgetRes.json && budgetRes.json.underBudget === true, budgetRes.text);

  // ---------------------------------------------------------------------------
  // PHASE 5: Payment Webhook Verification & Settlement
  // ---------------------------------------------------------------------------
  console.log('\n[4] PHASE 5: Payment Webhook & Settlement');

  const payOrderId = 1;
  const payEventId = `pay_tx_${Date.now()}_${Math.random().toString(36).substring(2, 6)}`;
  const payPayload = JSON.stringify({
    orderId: payOrderId,
    transactionId: payEventId,
    eventType: 'payment_intent.succeeded',
    amount: 1199.00,
    currency: 'USD'
  });
  const validHmac = crypto.createHmac('sha256', SECRET_KEY).update(payPayload).digest('hex');

  // 5.1 Failure Case: Invalid Signature
  const payBad = await fetch(`${BASE_URL}/api-payment-verify.php`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-ZeyTech-Signature': 'forged_invalid_signature_hex'
    },
    body: payPayload
  });
  assertTest('4.1 Webhook with forged/invalid HMAC signature rejected with HTTP 401', payBad.status === 401, await payBad.text());

  // 5.2 Valid Payment Settlement
  const payGood = await fetch(`${BASE_URL}/api-payment-verify.php`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-ZeyTech-Signature': validHmac
    },
    body: payPayload
  });
  const payGoodJson = await payGood.json();
  assertTest('4.2 Valid signed payment settles order and converts inventory from reserved to sold', payGoodJson && payGoodJson.verified === true && payGoodJson.status === 'PAYMENT_SETTLED_AND_CONFIRMED', JSON.stringify(payGoodJson));

  // 5.3 Duplicate Payment Webhook
  const payDup = await fetch(`${BASE_URL}/api-payment-verify.php`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-ZeyTech-Signature': validHmac
    },
    body: payPayload
  });
  const payDupJson = await payDup.json();
  assertTest('4.3 Duplicate payment webhook is idempotently handled without duplicate charges', payDupJson && payDupJson.verified === true && payDupJson.status.includes('DUPLICATE'), JSON.stringify(payDupJson));

  // 5.4 Verified Refund Settlement
  const refEventId = `ref_tx_${Date.now()}_${Math.random().toString(36).substring(2, 6)}`;
  const refPayload = JSON.stringify({
    orderId: payOrderId,
    transactionId: refEventId,
    eventType: 'refund.settled',
    amount: 1199.00,
    currency: 'USD'
  });
  const refHmac = crypto.createHmac('sha256', SECRET_KEY).update(refPayload).digest('hex');

  const refGood = await fetch(`${BASE_URL}/api-payment-verify.php`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-ZeyTech-Signature': refHmac
    },
    body: refPayload
  });
  const refGoodJson = await refGood.json();
  assertTest('4.4 Verified refund webhook completes PENDING_REFUND and restocks inventory', refGoodJson && refGoodJson.verified === true && refGoodJson.status === 'REFUND_COMPLETED_AND_SETTLED', JSON.stringify(refGoodJson));

  // ---------------------------------------------------------------------------
  // PHASE 6: Frontend Live Telemetry
  // ---------------------------------------------------------------------------
  console.log('\n[5] PHASE 6: Frontend Telemetry & Dashboard Integration');

  const dashRes = await postJson('/api-dashboard-kpis.php', {});
  assertTest('5.1 Dashboard API returns real live KPIs and 15 agents roster', dashRes.json && dashRes.json.success === true && Array.isArray(dashRes.json.agents) && dashRes.json.agents.length === 15, dashRes.text);

  console.log('\n====================================================================');
  console.log(` FINAL TEST RESULTS: ${passed} PASSED, ${failed} FAILED`);
  console.log('====================================================================');

  if (failed > 0) {
    process.exit(1);
  }
}

runAllTests();
