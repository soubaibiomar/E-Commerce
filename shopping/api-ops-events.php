<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 9: Real-Time SSE Stream for Operations Console
// Endpoint: GET /api-ops-events.php
// Provides low-latency Server-Sent Events push for tickets, approvals, and audits
// =============================================================================

error_reporting(0);
session_start();

// Disable output buffering
if (function_exists('apache_setenv')) {
    apache_setenv('no-gzip', '1');
}
ini_set('zlib.output_compression', '0');
ini_set('implicit_flush', '1');
while (ob_get_level()) {
    ob_end_flush();
}
ob_implicit_flush(true);

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no');
header('Access-Control-Allow-Origin: *');

include_once(__DIR__ . '/includes/config.php');
include_once(__DIR__ . '/includes/auth_helper.php');

// Authenticate via Authorization header or ?token= query param (for EventSource)
$token = '';
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
if (!empty($authHeader) && preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
    $token = $matches[1];
} elseif (!empty($_GET['token'])) {
    $token = trim($_GET['token']);
}

if (empty($token)) {
    http_response_code(401);
    echo "event: error\n";
    echo "data: " . json_encode(['error' => 'UNAUTHENTICATED', 'message' => 'Missing token.']) . "\n\n";
    flush();
    exit;
}

$session = db_fetch_one("SELECT * FROM staff_sessions WHERE session_token = ? AND expires_at > NOW()", [$token], "s");
if (!$session) {
    http_response_code(401);
    echo "event: error\n";
    echo "data: " . json_encode(['error' => 'SESSION_EXPIRED_OR_INVALID', 'message' => 'Invalid or expired session.']) . "\n\n";
    flush();
    exit;
}

// Initial Connection Event
echo "event: connected\n";
echo "data: " . json_encode([
    'connected' => true,
    'user' => [
        'email' => $session['email'],
        'role' => $session['role']
    ],
    'timestamp' => date('c')
]) . "\n\n";
flush();

// Track last known counts to send events on change
$lastApprovalCount = -1;
$lastEscalationCount = -1;
$iterations = 0;
$maxIterations = 20; // 20 cycles (approx 20-30s per connection then EventSource auto-reconnects)

while ($iterations < $maxIterations) {
    if (connection_aborted()) {
        break;
    }

    $approvalRow = db_fetch_one("SELECT COUNT(*) AS cnt FROM ops_approval_queue WHERE status = 'PENDING_APPROVAL'");
    $escalationRow = db_fetch_one("SELECT COUNT(*) AS cnt FROM ops_escalation_queue WHERE status = 'OPEN'");

    $curApprovalCount = intval($approvalRow['cnt'] ?? 0);
    $curEscalationCount = intval($escalationRow['cnt'] ?? 0);

    if ($curApprovalCount !== $lastApprovalCount || $curEscalationCount !== $lastEscalationCount) {
        $lastApprovalCount = $curApprovalCount;
        $lastEscalationCount = $curEscalationCount;

        echo "event: queue_update\n";
        echo "data: " . json_encode([
            'pendingApprovals' => $curApprovalCount,
            'openEscalations' => $curEscalationCount,
            'timestamp' => date('c')
        ]) . "\n\n";
        flush();
    } else {
        // Heartbeat Ping
        echo ": ping\n\n";
        flush();
    }

    $iterations++;
    sleep(1);
}
