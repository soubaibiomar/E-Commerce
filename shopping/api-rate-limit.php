<?php
/**
 * ZeyTech — Gateway Rate Limiter API (Phase 4)
 * Exact contract for rate-limit-check node.
 * Accepts: { senderId, channel, windowSeconds, maxRequests }
 * Returns: { allowed: boolean }
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: [];

$senderId = trim($input['senderId'] ?? 'ANONYMOUS');
$channel = trim($input['channel'] ?? 'WEB');
$windowSeconds = max(1, intval($input['windowSeconds'] ?? 60));
$maxRequests = max(1, intval($input['maxRequests'] ?? 20));

try {
    // 1. Insert current request event
    db_execute(
        "INSERT INTO rate_limit_events (sender_id, channel, request_timestamp) VALUES (?, ?, NOW())",
        [$senderId, $channel],
        "ss"
    );

    // 2. Count requests in trailing window using native database interval to prevent timezone skew
    $countRow = db_fetch_one(
        "SELECT COUNT(*) as req_count FROM rate_limit_events WHERE sender_id = ? AND request_timestamp >= (NOW() - INTERVAL ? SECOND)",
        [$senderId, $windowSeconds],
        "si"
    );

    $count = intval($countRow['req_count'] ?? 1);
    $allowed = ($count <= $maxRequests);

    // 3. Periodic cleanup of stale events (> 1 hour old)
    if (random_int(1, 20) === 1) {
        @db_execute("DELETE FROM rate_limit_events WHERE request_timestamp < (NOW() - INTERVAL 1 HOUR)");
    }

    echo json_encode([
        'allowed' => $allowed,
        'senderId' => $senderId,
        'currentRequests' => $count,
        'maxRequests' => $maxRequests,
        'windowSeconds' => $windowSeconds
    ]);

} catch (Exception $e) {
    // Fail open for gateway resilience
    echo json_encode(['allowed' => true, 'fallback' => true, 'error' => $e->getMessage()]);
}
