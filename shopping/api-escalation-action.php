<?php
/**
 * ZeyTech AI Commerce OS — Support Escalation Claim API (Phase 8)
 * Contract: POST /api-escalation-action.php
 * Accepts: { ticketId, action: "claim" }
 * Requires: Authentication (any role allowed to claim)
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/auth_helper.php');

// 1. Enforce Authentication (Any logged-in staff member may claim a ticket)
$staff = require_staff_auth();

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: [];

$ticketId = intval($input['ticketId'] ?? 0);
$action = strtolower(trim($input['action'] ?? ''));

if ($ticketId <= 0 || $action !== 'claim') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'INVALID_INPUT',
        'message' => 'Valid ticketId and action ("claim") are required.'
    ]);
    exit();
}

try {
    $con = get_db_connection();

    // 2. Perform Atomic Claim with Double-Claim Mutex
    $stmt = mysqli_prepare($con, "UPDATE ops_escalation_queue SET status = 'CLAIMED', claimed_by = ?, claimed_at = NOW() WHERE id = ? AND status = 'OPEN'");
    mysqli_stmt_bind_param($stmt, "ii", $staff['staffId'], $ticketId);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    if ($affected === 0) {
        // Check why it failed
        $current = db_fetch_one(
            "SELECT e.*, u.name as currentClaimedByName FROM ops_escalation_queue e LEFT JOIN staff_users u ON e.claimed_by = u.id WHERE e.id = ?",
            [$ticketId],
            "i"
        );

        if (!$current) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'TICKET_NOT_FOUND',
                'message' => "Escalation ticket #{$ticketId} was not found."
            ]);
            exit();
        }

        http_response_code(409);
        echo json_encode([
            'success' => false,
            'error' => 'ALREADY_CLAIMED',
            'message' => "This ticket has already been claimed by " . ($current['currentClaimedByName'] ?? 'another staff member') . "."
        ]);
        exit();
    }

    // 3. Write Audit Trail
    $ticket = db_fetch_one("SELECT trace_id FROM ops_escalation_queue WHERE id = ?", [$ticketId], "i");
    $traceId = $ticket['trace_id'] ?? ('tr_esc_' . $ticketId);

    db_execute(
        "INSERT INTO audit_logs (trace_id, actor, channel, sender_id, decision, confidence, reply, created_at) VALUES (?, 'STAFF_SUPPORT', 'OPS_CONSOLE', ?, 'TICKET_CLAIMED', 1.0, ?, NOW())",
        [$traceId, $staff['email'], "Staff member {$staff['name']} claimed ticket #{$ticketId}."],
        "sss"
    );

    echo json_encode([
        'success' => true,
        'action' => 'claim',
        'ticketId' => $ticketId,
        'status' => 'CLAIMED',
        'claimedBy' => $staff['name'],
        'message' => "Ticket #{$ticketId} successfully claimed by {$staff['name']}."
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
