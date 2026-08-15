<?php
/**
 * ZeyTech AI Commerce OS — Manager Approval Gate Action API (Phase 8)
 * Contract: POST /api-approval-action.php
 * Accepts: { ticketId, action: "approve" | "reject" }
 * Requires: Authentication AND role `manager` or `admin` (HTTP 403 for `support`)
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/auth_helper.php');

// 1. Enforce RBAC: Only 'manager' or 'admin' may approve or reject sensitive actions
$staff = require_staff_auth(['manager', 'admin']);

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: [];

$ticketId = intval($input['ticketId'] ?? 0);
$action = strtolower(trim($input['action'] ?? ''));

if ($ticketId <= 0 || !in_array($action, ['approve', 'reject'], true)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'INVALID_INPUT',
        'message' => 'Valid ticketId and action ("approve" or "reject") are required.'
    ]);
    exit();
}

try {
    // 2. Fetch pending approval record
    $ticket = db_fetch_one("SELECT * FROM ops_approval_queue WHERE id = ?", [$ticketId], "i");

    if (!$ticket) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'TICKET_NOT_FOUND',
            'message' => "Approval ticket #{$ticketId} was not found."
        ]);
        exit();
    }

    if ($ticket['status'] !== 'PENDING_APPROVAL') {
        http_response_code(409);
        echo json_encode([
            'success' => false,
            'error' => 'ALREADY_DECIDED',
            'message' => "Approval ticket #{$ticketId} has already been marked as {$ticket['status']}."
        ]);
        exit();
    }

    $traceId = $ticket['trace_id'];
    $actorRole = 'STAFF_' . strtoupper($staff['role']);

    // 3. Execution on APPROVE
    if ($action === 'approve') {
        $actionType = strtoupper($ticket['action_type'] ?? 'REFUND');
        $targetId = intval($ticket['target_id'] ?? 0);

        // Execute underlying logic (Refund, Cancellation, Price Change)
        if ($actionType === 'REFUND' && $targetId > 0) {
            // Update order status to pending_refund / approved refund
            db_execute("UPDATE orders SET status = 'pending_refund', paymentStatus = 'PENDING_REFUND' WHERE id = ?", [$targetId], "i");
        } elseif ($actionType === 'CANCELLATION' && $targetId > 0) {
            db_execute("UPDATE orders SET status = 'cancelled', orderStatus = 'CANCELLED' WHERE id = ?", [$targetId], "i");
        }

        // Update approval queue record
        db_execute(
            "UPDATE ops_approval_queue SET status = 'APPROVED', approved_by = ?, decided_at = NOW() WHERE id = ?",
            [$staff['staffId'], $ticketId],
            "ii"
        );

        // Write Audit Log Trail
        db_execute(
            "INSERT INTO audit_logs (trace_id, actor, channel, sender_id, decision, confidence, reply, created_at) VALUES (?, ?, 'OPS_CONSOLE', ?, ?, 1.0, ?, NOW())",
            [$traceId, $actorRole, $staff['email'], "MANAGER_APPROVED_{$actionType}", "Manager {$staff['name']} ({$staff['role']}) approved high-value action for ticket #{$ticketId}."],
            "sssss"
        );

        echo json_encode([
            'success' => true,
            'action' => 'approve',
            'ticketId' => $ticketId,
            'status' => 'APPROVED',
            'decidedBy' => $staff['name'],
            'message' => "Ticket #{$ticketId} successfully approved by {$staff['name']}."
        ]);
        exit();
    }

    // 4. Execution on REJECT
    if ($action === 'reject') {
        // Update approval queue record
        db_execute(
            "UPDATE ops_approval_queue SET status = 'REJECTED', approved_by = ?, decided_at = NOW() WHERE id = ?",
            [$staff['staffId'], $ticketId],
            "ii"
        );

        // Write Audit Log Trail & Dispatch Notification Trail
        db_execute(
            "INSERT INTO audit_logs (trace_id, actor, channel, sender_id, decision, confidence, reply, created_at) VALUES (?, ?, 'OPS_CONSOLE', ?, 'MANAGER_REJECTED_TICKET', 1.0, ?, NOW())",
            [$traceId, $actorRole, $staff['email'], "Manager {$staff['name']} rejected high-value ticket #{$ticketId}. Customer {$ticket['customer']} notified via {$ticket['channel']}."],
            "ssss"
        );

        echo json_encode([
            'success' => true,
            'action' => 'reject',
            'ticketId' => $ticketId,
            'status' => 'REJECTED',
            'decidedBy' => $staff['name'],
            'message' => "Ticket #{$ticketId} rejected. Customer {$ticket['customer']} will receive automated status notification."
        ]);
        exit();
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
