<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 10: Omnichannel Message Dispatch
// Endpoint: POST /api-chat-send.php
// Allows Storefront Customers and Console Staff to send live messages
// =============================================================================

error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once(__DIR__ . '/includes/config.php');
include_once(__DIR__ . '/includes/auth_helper.php');

$rawInput = file_get_contents('php://input');
$body = json_decode($rawInput, true) ?? $_POST;

$ticketId = intval($body['ticketId'] ?? 0);
$sessionId = trim($body['sessionId'] ?? '');
$message = trim($body['message'] ?? '');
$senderType = strtoupper(trim($body['senderType'] ?? 'CUSTOMER'));
$senderName = trim($body['senderName'] ?? '');
$channel = strtoupper(trim($body['channel'] ?? 'WEB'));

if (empty($message)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'EMPTY_MESSAGE',
        'message' => 'Message body cannot be empty.'
    ]);
    exit;
}

if ($ticketId <= 0 && empty($sessionId)) {
    $sessionId = 'sess_' . bin2hex(random_bytes(8));
}

// Security Check: If sending as STAFF, enforce staff authentication
if ($senderType === 'STAFF') {
    $staff = require_staff_auth();
    if (empty($senderName)) {
        $senderName = $staff['name'] . ' (' . ucfirst($staff['role']) . ')';
    }
} else {
    if (empty($senderName)) {
        $senderName = 'Customer';
    }
    $senderType = 'CUSTOMER';
}

$db = get_db_connection();

// Insert into chat_messages
db_execute(
    "INSERT INTO chat_messages (ticket_id, session_id, sender_type, sender_name, channel, message, created_at)
     VALUES (?, ?, ?, ?, ?, ?, NOW())",
    [
        $ticketId > 0 ? $ticketId : null,
        $sessionId,
        $senderType,
        $senderName,
        $channel,
        $message
    ],
    "isssss"
);

$msgId = mysqli_insert_id($db);

// If message sent by staff on an escalation ticket, record audit log
if ($senderType === 'STAFF' && $ticketId > 0) {
    db_execute(
        "INSERT INTO audit_logs (trace_id, actor, channel, sender_id, decision, confidence, reply, created_at)
         VALUES (?, ?, ?, ?, 'STAFF_REPLY_SENT', 1.00, ?, NOW())",
        [
            'tr_msg_' . $msgId,
            $senderName,
            $channel,
            'staff_' . ($staff['id'] ?? 0),
            substr($message, 0, 250)
        ],
        "sssss"
    );
}

echo json_encode([
    'success' => true,
    'messageId' => $msgId,
    'ticketId' => $ticketId > 0 ? $ticketId : null,
    'sessionId' => $sessionId,
    'senderType' => $senderType,
    'senderName' => $senderName,
    'channel' => $channel,
    'message' => $message,
    'createdAt' => date('Y-m-d H:i:s')
]);
