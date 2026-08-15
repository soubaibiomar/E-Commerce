<?php
/**
 * ZeyTech AI Commerce OS — Operations Console Queues API (Phase 8)
 * Contract: GET /api-ops-queues.php
 * Requires authentication (any logged-in staff role: support, manager, admin)
 * Returns: { approvals: [...], escalations: [...], audit: [...] }
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/auth_helper.php');

// 1. Enforce Authentication (Any valid staff role allowed to view queues)
$staff = require_staff_auth();

try {
    // 2. Fetch PENDING_APPROVAL records (7b. Manager Approval Gate branch)
    $approvalRows = db_query("SELECT id, trace_id, customer, channel, amount_mad, reason, flags, status, action_type, target_id FROM ops_approval_queue WHERE status = 'PENDING_APPROVAL' ORDER BY id DESC");
    $approvals = [];
    if (!empty($approvalRows)) {
        foreach ($approvalRows as $r) {
            $flagsDecoded = is_string($r['flags']) ? json_decode($r['flags'], true) : $r['flags'];
            $approvals[] = [
                'id' => intval($r['id']),
                'traceId' => $r['trace_id'],
                'customer' => $r['customer'],
                'channel' => $r['channel'],
                'amountMAD' => floatval($r['amount_mad']),
                'reason' => $r['reason'],
                'flags' => $flagsDecoded ?: [],
                'status' => $r['status']
            ];
        }
    }

    // 3. Fetch HITL Escalation Queue records (7a. HITL Support Escalation Queue branch)
    $escalationRows = db_query("SELECT e.id, e.trace_id, e.customer, e.channel, e.confidence, e.message, e.flags, e.status, e.claimed_by, u.name as claimedByName FROM ops_escalation_queue e LEFT JOIN staff_users u ON e.claimed_by = u.id WHERE e.status IN ('OPEN', 'CLAIMED') ORDER BY e.id DESC");
    $escalations = [];
    if (!empty($escalationRows)) {
        foreach ($escalationRows as $r) {
            $flagsDecoded = is_string($r['flags']) ? json_decode($r['flags'], true) : $r['flags'];
            $escalations[] = [
                'id' => intval($r['id']),
                'traceId' => $r['trace_id'],
                'customer' => $r['customer'],
                'channel' => $r['channel'],
                'confidence' => floatval($r['confidence']),
                'message' => $r['message'],
                'flags' => $flagsDecoded ?: [],
                'status' => $r['status'],
                'claimedBy' => $r['claimedByName'] ?? null
            ];
        }
    }

    // 4. Fetch Recent Audit Logs (Phase 4 audit_logs table)
    $auditRows = db_query("SELECT trace_id, actor, decision, reply, created_at FROM audit_logs ORDER BY id DESC LIMIT 20");
    $audit = [];
    if (!empty($auditRows)) {
        foreach ($auditRows as $r) {
            $audit[] = [
                'traceId' => $r['trace_id'],
                'actor' => $r['actor'],
                'decision' => $r['decision'],
                'status' => $r['decision'],
                'reply' => $r['reply'],
                'createdAt' => $r['created_at']
            ];
        }
    }

    echo json_encode([
        'approvals' => $approvals,
        'escalations' => $escalations,
        'audit' => $audit,
        'authenticatedUser' => [
            'name' => $staff['name'],
            'email' => $staff['email'],
            'role' => $staff['role']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
