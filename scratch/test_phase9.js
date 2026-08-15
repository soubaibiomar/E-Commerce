const http = require('http');

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

async function runPhase9Tests() {
  console.log('====================================================================');
  console.log(' ZEYTECH AI COMMERCE OS — PHASE 9 (REAL-TIME SSE) VERIFICATION');
  console.log('====================================================================\n');

  let passed = 0;
  let failed = 0;

  // 1. Unauthenticated request rejected
  try {
    const res = await fetch(`${BASE_URL}/api-ops-events.php`);
    if (res.status === 401) {
      logPass('9.1 Unauthenticated GET /api-ops-events.php returns HTTP 401');
      passed++;
    } else {
      logFail('9.1 Unauthenticated GET /api-ops-events.php', `Got status ${res.status}`);
      failed++;
    }
  } catch (err) {
    logFail('9.1 Unauthenticated test', err.message);
    failed++;
  }

  // 2. Invalid token rejected
  try {
    const res = await fetch(`${BASE_URL}/api-ops-events.php?token=invalid_tok_12345`);
    if (res.status === 401) {
      logPass('9.2 Invalid token returns HTTP 401');
      passed++;
    } else {
      logFail('9.2 Invalid token', `Got status ${res.status}`);
      failed++;
    }
  } catch (err) {
    logFail('9.2 Invalid token test', err.message);
    failed++;
  }

  // 3. Valid token connects to SSE Stream
  try {
    const token = await loginAdmin();
    const ssePromise = new Promise((resolve, reject) => {
      const req = http.get(`${BASE_URL}/api-ops-events.php?token=${encodeURIComponent(token)}`, (res) => {
        let buffer = '';
        const isEventStream = (res.headers['content-type'] || '').includes('text/event-stream');
        
        if (!isEventStream) {
          req.destroy();
          return resolve({ isEventStream: false, status: res.status });
        }

        res.on('data', (chunk) => {
          buffer += chunk.toString();
          if (buffer.includes('event: connected') && buffer.includes('event: queue_update')) {
            req.destroy();
            return resolve({ isEventStream: true, receivedEvents: true, raw: buffer });
          }
        });

        setTimeout(() => {
          req.destroy();
          resolve({ isEventStream: true, receivedEvents: buffer.includes('event: connected'), raw: buffer });
        }, 3000);
      });

      req.on('error', (err) => {
        if (err.code === 'ECONNRESET') {
          // Normal when aborted
        } else {
          reject(err);
        }
      });
    });

    const sseResult = await ssePromise;
    if (sseResult.isEventStream && sseResult.receivedEvents) {
      logPass('9.3 Valid staff token establishes SSE text/event-stream with initial events');
      passed++;
    } else {
      logFail('9.3 Valid token SSE stream', JSON.stringify(sseResult));
      failed++;
    }
  } catch (err) {
    logFail('9.3 SSE stream connection', err.message);
    failed++;
  }

  console.log('\n====================================================================');
  console.log(` PHASE 9 TEST RESULTS: ${passed} PASSED, ${failed} FAILED`);
  console.log('====================================================================');

  if (failed > 0) process.exit(1);
}

runPhase9Tests();
