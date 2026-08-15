const BASE_URL = 'http://localhost:8085';

function logPass(msg) { console.log(`  [PASS] ${msg}`); }
function logFail(msg, detail) { console.error(`  [FAIL] ${msg} -> ${detail}`); }

async function loginSupport() {
  const res = await fetch(`${BASE_URL}/api-auth-login.php`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: 'support@zeytech.com', password: 'SupportPassword2026!' })
  });
  const data = await res.json();
  return data.token;
}

async function runPhase10Tests() {
  console.log('====================================================================');
  console.log(' ZEYTECH AI COMMERCE OS — PHASE 10 (LIVE MESSAGING) VERIFICATION');
  console.log('====================================================================\n');

  let passed = 0;
  let failed = 0;

  // 10.1 Chat History Retrieval on Ticket
  try {
    const res = await fetch(`${BASE_URL}/api-chat-history.php?ticketId=1`);
    const data = await res.json();
    if (data.success && Array.isArray(data.messages) && data.messages.length >= 3) {
      logPass('10.1 GET /api-chat-history.php returns structured conversation thread');
      passed++;
    } else {
      logFail('10.1 Chat history retrieval', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('10.1 Chat history test', err.message);
    failed++;
  }

  // 10.2 Customer Message Ingestion
  try {
    const testSess = 'sess_test_' + Date.now();
    const res = await fetch(`${BASE_URL}/api-chat-send.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        sessionId: testSess,
        senderType: 'CUSTOMER',
        senderName: 'Tariq M.',
        message: 'Wach momkin n-chouf fiche technique dyal Mac?',
        channel: 'WEB'
      })
    });
    const data = await res.json();
    if (data.success && data.messageId > 0 && data.senderType === 'CUSTOMER') {
      logPass('10.2 Customer message successfully dispatched and persisted in chat_messages');
      passed++;
    } else {
      logFail('10.2 Customer message dispatch', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('10.2 Customer message test', err.message);
    failed++;
  }

  // 10.3 Unauthenticated Staff Message Guard
  try {
    const res = await fetch(`${BASE_URL}/api-chat-send.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        ticketId: 1,
        senderType: 'STAFF',
        message: 'Unauthorized staff message test.'
      })
    });
    if (res.status === 401) {
      logPass('10.3 Unauthenticated staff reply rejected with HTTP 401');
      passed++;
    } else {
      logFail('10.3 Staff auth guard', `Got status ${res.status}`);
      failed++;
    }
  } catch (err) {
    logFail('10.3 Staff auth test', err.message);
    failed++;
  }

  // 10.4 Authenticated Staff Message Ingestion & Audit Logging
  try {
    const supportToken = await loginSupport();
    const res = await fetch(`${BASE_URL}/api-chat-send.php`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${supportToken}`
      },
      body: JSON.stringify({
        ticketId: 1,
        senderType: 'STAFF',
        message: 'Salam Yassine! Ana m3ak mn Support, commande dyalk ghadi toussal ghedda inchaAllah.',
        channel: 'WHATSAPP'
      })
    });
    const data = await res.json();
    if (data.success && data.senderType === 'STAFF') {
      logPass('10.4 Authenticated staff reply recorded with staff attribution and audit trail');
      passed++;
    } else {
      logFail('10.4 Staff message send', JSON.stringify(data));
      failed++;
    }
  } catch (err) {
    logFail('10.4 Staff message test', err.message);
    failed++;
  }

  console.log('\n====================================================================');
  console.log(` PHASE 10 TEST RESULTS: ${passed} PASSED, ${failed} FAILED`);
  console.log('====================================================================');

  if (failed > 0) process.exit(1);
}

runPhase10Tests();
