<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 10: Omnichannel Chat History
// Endpoint: GET /api-chat-history.php
// Query params: ?ticketId=... OR ?sessionId=...
// =============================================================================

error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once(__DIR__ . '/includes/config.php');

$ticketId = intval($_GET['ticketId'] ?? 0);
$sessionId = trim($_GET['sessionId'] ?? '');

if ($ticketId <= 0 && empty($sessionId)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'MISSING_PARAMETERS',
        'message' => 'Please provide either ticketId or sessionId.'
    ]);
    exit;
}

$messages = [];
if ($ticketId > 0) {
    $messages = db_fetch_all(
        "SELECT id, ticket_id, session_id, sender_type, sender_name, channel, message, created_at 
         FROM chat_messages 
         WHERE ticket_id = ? 
         ORDER BY id ASC",
        [$ticketId],
        "i"
    );
} else {
    $messages = db_fetch_all(
        "SELECT id, ticket_id, session_id, sender_type, sender_name, channel, message, created_at 
         FROM chat_messages 
         WHERE session_id = ? 
         ORDER BY id ASC",
        [$sessionId],
        "s"
    );
}

echo json_encode([
    'success' => true,
    'ticketId' => $ticketId > 0 ? $ticketId : null,
    'sessionId' => $sessionId ?: null,
    'count' => count($messages),
    'messages' => $messages
]);
