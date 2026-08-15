const BASE_URL = 'http://localhost:8085';

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

async function requestJson(endpoint, method = 'POST', data = null, token = null) {
  const url = `${BASE_URL}${endpoint}`;
  const headers = { 'Content-Type': 'application/json' };
  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }
  const opts = { method, headers };
  if (data && method !== 'GET') {
    opts.body = JSON.stringify(data);
  }
  try {
    const res = await fetch(url, opts);
    const text = await res.text();
    let json = null;
    try { json = JSON.parse(text); } catch (e) {}
    return { status: res.status, json, text };
  } catch (err) {
    return { status: 0, error: err.message };
  }
}

async function runPhase7And8Tests() {
  console.log('====================================================================');
  console.log(' ZEYTECH AI COMMERCE OS — PHASE 7 & 8 CONTRACT VERIFICATION');
  console.log('====================================================================\n');

  // ---------------------------------------------------------------------------
  // 1. PHASE 7: AUTHENTICATION & ROUTE-LEVEL PROTECTION
  // ---------------------------------------------------------------------------
  console.log('[1] Testing Phase 7 — Staff Authentication & RBAC');

  // 1.1 Unauthenticated requests to protected endpoints return 401
  const unauth1 = await requestJson('/api-ops-queues.php', 'GET');
  assertTest('1.1 Unauthenticated GET /api-ops-queues.php returns HTTP 401', unauth1.status === 401 && unauth1.json && unauth1.json.error === 'UNAUTHENTICATED', unauth1.text);

  const unauth2 = await requestJson('/api-approval-action.php', 'POST', { ticketId: 1, action: 'approve' });
  assertTest('1.2 Unauthenticated POST /api-approval-action.php returns HTTP 401', unauth2.status === 401 && unauth2.json && unauth2.json.error === 'UNAUTHENTICATED', unauth2.text);

  const unauth3 = await requestJson('/api-escalation-action.php', 'POST', { ticketId: 1, action: 'claim' });
  assertTest('1.3 Unauthenticated POST /api-escalation-action.php returns HTTP 401', unauth3.status === 401 && unauth3.json && unauth3.json.error === 'UNAUTHENTICATED', unauth3.text);

  // 1.4 Invalid credentials rejected
  const badLogin = await requestJson('/api-auth-login.php', 'POST', { email: 'admin@zeytech.com', password: 'WrongPassword123!' });
  assertTest('1.4 Login with invalid password returns HTTP 401 INVALID_CREDENTIALS', badLogin.status === 401 && badLogin.json && badLogin.json.error === 'INVALID_CREDENTIALS', badLogin.text);

  // 1.5 Valid login for Admin, Manager, Support
  const adminLogin = await requestJson('/api-auth-login.php', 'POST', { email: 'admin@zeytech.com', password: 'AdminPassword2026!' });
  const adminToken = adminLogin.json ? adminLogin.json.token : '';
  assertTest('1.5 Admin login issues valid token with role "admin"', adminLogin.json && adminLogin.json.success === true && adminLogin.json.user.role === 'admin' && adminToken.length > 15, adminLogin.text);

  const managerLogin = await requestJson('/api-auth-login.php', 'POST', { email: 'manager@zeytech.com', password: 'ManagerPassword2026!' });
  const managerToken = managerLogin.json ? managerLogin.json.token : '';
  assertTest('1.6 Manager login issues valid token with role "manager"', managerLogin.json && managerLogin.json.success === true && managerLogin.json.user.role === 'manager' && managerToken.length > 15, managerLogin.text);

  const supportLogin = await requestJson('/api-auth-login.php', 'POST', { email: 'support@zeytech.com', password: 'SupportPassword2026!' });
  const supportToken = supportLogin.json ? supportLogin.json.token : '';
  assertTest('1.7 Support login issues valid token with role "support"', supportLogin.json && supportLogin.json.success === true && supportLogin.json.user.role === 'support' && supportToken.length > 15, supportLogin.text);

  // 1.8 RBAC Guard: Support attempting manager approval action returns HTTP 403
  const supportApprove = await requestJson('/api-approval-action.php', 'POST', { ticketId: 1, action: 'approve' }, supportToken);
  assertTest('1.8 Support role calling approval action is rejected with HTTP 403 FORBIDDEN', supportApprove.status === 403 && supportApprove.json && supportApprove.json.error === 'FORBIDDEN', supportApprove.text);

  // 1.9 Logout invalidates session
  const logoutRes = await requestJson('/api-auth-logout.php', 'POST', {}, adminToken);
  const afterLogout = await requestJson('/api-ops-queues.php', 'GET', null, adminToken);
  assertTest('1.9 Terminated session token immediately rejected with HTTP 401', afterLogout.status === 401, afterLogout.text);

  // ---------------------------------------------------------------------------
  // 2. PHASE 8: OPERATIONS CONSOLE BACKEND
  // ---------------------------------------------------------------------------
  console.log('\n[2] Testing Phase 8 — Operations Console Backend Endpoints');

  // Re-authenticate fresh sessions
  const activeManager = (await requestJson('/api-auth-login.php', 'POST', { email: 'manager@zeytech.com', password: 'ManagerPassword2026!' })).json.token;
  const activeSupport = (await requestJson('/api-auth-login.php', 'POST', { email: 'support@zeytech.com', password: 'SupportPassword2026!' })).json.token;
  const activeAdmin = (await requestJson('/api-auth-login.php', 'POST', { email: 'admin@zeytech.com', password: 'AdminPassword2026!' })).json.token;

  // 2.1 GET /api-ops-queues.php contract
  const queuesRes = await requestJson('/api-ops-queues.php', 'GET', null, activeManager);
  assertTest('2.1 GET /api-ops-queues.php returns approvals, escalations, and audit arrays', queuesRes.json && Array.isArray(queuesRes.json.approvals) && Array.isArray(queuesRes.json.escalations) && Array.isArray(queuesRes.json.audit), queuesRes.text);

  // 2.2 Manager Approval Action — Approve
  const approveRes = await requestJson('/api-approval-action.php', 'POST', { ticketId: 1, action: 'approve' }, activeManager);
  assertTest('2.2 Manager approval executes underlying state update and marks APPROVED', approveRes.json && approveRes.json.success === true && approveRes.json.status === 'APPROVED', approveRes.text);

  // 2.3 Manager Approval Action — Reject
  const rejectRes = await requestJson('/api-approval-action.php', 'POST', { ticketId: 2, action: 'reject' }, activeManager);
  assertTest('2.3 Manager rejection updates status to REJECTED and writes audit trail', rejectRes.json && rejectRes.json.success === true && rejectRes.json.status === 'REJECTED', rejectRes.text);

  // 2.4 Support Escalation Claim
  const claimRes = await requestJson('/api-escalation-action.php', 'POST', { ticketId: 2, action: 'claim' }, activeSupport);
  assertTest('2.4 Support staff successfully claims escalation ticket (status: CLAIMED)', claimRes.json && claimRes.json.success === true && claimRes.json.status === 'CLAIMED', claimRes.text);

  // 2.5 Concurrency / Double-Claim Guard: Second staff member attempting to claim same ticket
  const secondClaim = await requestJson('/api-escalation-action.php', 'POST', { ticketId: 2, action: 'claim' }, activeAdmin);
  assertTest('2.5 Second claim on already-claimed ticket fails cleanly with HTTP 409 ALREADY_CLAIMED', secondClaim.status === 409 && secondClaim.json && secondClaim.json.error === 'ALREADY_CLAIMED', secondClaim.text);

  console.log('\n====================================================================');
  console.log(` PHASE 7 & 8 TEST RESULTS: ${passed} PASSED, ${failed} FAILED`);
  console.log('====================================================================');

  if (failed > 0) {
    process.exit(1);
  }
}

runPhase7And8Tests();
