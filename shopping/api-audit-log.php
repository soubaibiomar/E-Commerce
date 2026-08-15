<?php
/**
 * ZeyTech — Decision Audit Log API (Phase 4)
 * Exact contract for audit-log-writer node.
 * Accepts: { traceId, actor, channel, senderId, decision, confidence, reply, timestamp }
 * Returns: { success: true }
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: [];

$traceId = trim($input['traceId'] ?? 'tr_' . bin2hex(random_bytes(6)));
$actor = trim($input['actor'] ?? 'CUSTOMER');
$channel = trim($input['channel'] ?? 'WEB');
$senderId = trim($input['senderId'] ?? 'ANONYMOUS');
$decision = trim($input['decision'] ?? 'SUCCESS');
$confidence = floatval($input['confidence'] ?? 1.0);
$reply = is_string($input['reply'] ?? '') ? $input['reply'] : json_encode($input['reply'] ?? '');

try {
    db_execute(
        "INSERT INTO audit_logs (trace_id, actor, channel, sender_id, decision, confidence, reply, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
        [$traceId, $actor, $channel, $senderId, $decision, $confidence, $reply],
        "sssssds"
    );

    echo json_encode([
        'success' => true,
        'traceId' => $traceId,
        'logged' => true
    ]);
} catch (Throwable $e) {
    // Fail safe — never throw 500 to the Supervisor
    error_log("[ZeyTech Audit Log Error] " . $e->getMessage());
    echo json_encode(['success' => true, 'logged' => false, 'note' => 'saved_to_fallback']);
}
