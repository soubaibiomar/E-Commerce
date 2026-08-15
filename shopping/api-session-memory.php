<?php
/**
 * ZeyTech — Omnichannel Session Continuity & Memory API (Gap 5 & Gap 38)
 * Links Telegram Chat ID, WhatsApp Phone, and Web Sessions to a single unified customer profile and message history.
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true) ?: [];

$action = trim($input['action'] ?? 'resolve_session'); // 'resolve_session', 'link_channel', 'save_message', 'get_history'
$channel = strtoupper(trim($input['channel'] ?? 'WEB'));
$senderId = trim($input['senderId'] ?? $input['phone'] ?? $input['chatId'] ?? '');
$customerId = intval($input['customerId'] ?? 0);
$sessionId = trim($input['sessionId'] ?? '');
$messageText = trim($input['message'] ?? '');
$role = trim($input['role'] ?? 'user');

try {
    // 1. Resolve Omnichannel Customer Identity
    if ($action === 'resolve_session' || $action === 'link_channel') {
        if (empty($senderId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'senderId/phone/chatId is required']);
            exit();
        }

        // Check existing identity map
        $identity = null;
        if ($channel === 'TELEGRAM') {
            $identity = db_fetch_one("SELECT * FROM customer_channel_identities WHERE telegram_chat_id = ?", [$senderId], "s");
        } elseif ($channel === 'WHATSAPP') {
            $identity = db_fetch_one("SELECT * FROM customer_channel_identities WHERE whatsapp_phone = ? OR phone_number = ?", [$senderId, $senderId], "ss");
        } else {
            $identity = db_fetch_one("SELECT * FROM customer_channel_identities WHERE web_session_id = ? OR customer_id = ?", [$senderId, $customerId], "si");
        }

        // If not found, create new link
        if (!$identity) {
            $matchedUser = null;
            if ($customerId > 0) {
                $matchedUser = db_fetch_one("SELECT * FROM users WHERE id = ?", [$customerId], "i");
            } elseif ($channel === 'WHATSAPP') {
                $cleanPhone = preg_replace('/[^0-9]/', '', $senderId);
                $matchedUser = db_fetch_one("SELECT * FROM users WHERE contactno = ? OR contactno = ?", [$cleanPhone, substr($cleanPhone, -9)], "ss");
            }

            $resolvedCustId = $matchedUser ? intval($matchedUser['id']) : ($customerId ?: rand(1000, 9999));
            $resolvedEmail = $matchedUser['email'] ?? null;
            $resolvedPhone = $matchedUser['contactno'] ? strval($matchedUser['contactno']) : ($channel === 'WHATSAPP' ? $senderId : null);

            $tgId = ($channel === 'TELEGRAM') ? $senderId : null;
            $waPhone = ($channel === 'WHATSAPP') ? $senderId : null;
            $webSess = ($channel === 'WEB') ? $senderId : 'sess_' . bin2hex(random_bytes(6));

            db_execute(
                "INSERT INTO customer_channel_identities (customer_id, phone_number, email, telegram_chat_id, whatsapp_phone, web_session_id, last_active_channel) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [$resolvedCustId, $resolvedPhone, $resolvedEmail, $tgId, $waPhone, $webSess, $channel],
                "issssss"
            );

            $identity = [
                'customer_id' => $resolvedCustId,
                'phone_number' => $resolvedPhone,
                'email' => $resolvedEmail,
                'telegram_chat_id' => $tgId,
                'whatsapp_phone' => $waPhone,
                'web_session_id' => $webSess
            ];
        } else {
            // Update last active channel
            db_execute("UPDATE customer_channel_identities SET last_active_channel = ? WHERE id = ?", [$channel, $identity['id']], "si");
        }

        $unifiedSessionKey = 'cust_sess_' . $identity['customer_id'];

        echo json_encode([
            'success' => true,
            'action' => 'resolve_session',
            'unifiedSessionKey' => $unifiedSessionKey,
            'customerId' => $identity['customer_id'],
            'email' => $identity['email'],
            'phone' => $identity['phone_number'],
            'telegramChatId' => $identity['telegram_chat_id'],
            'whatsappPhone' => $identity['whatsapp_phone'],
            'currentChannel' => $channel,
            'sessionContinuityActive' => true
        ]);
        exit();
    }

    // 2. Save Message & Maintain Multi-Channel History (Gap 5 & Gap 38)
    if ($action === 'save_message') {
        if (empty($sessionId) || empty($messageText)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'sessionId and message are required']);
            exit();
        }

        $msgId = 'msg_' . date('YmdHis') . '_' . bin2hex(random_bytes(4));
        
        // Ensure session exists
        db_execute(
            "INSERT INTO chatsessions (id, userId, title) VALUES (?, ?, 'Omnichannel Session') ON DUPLICATE KEY UPDATE updatedAt = NOW()",
            [$sessionId, $customerId ?: null],
            "si"
        );

        db_execute(
            "INSERT INTO chatmessages (id, sessionId, sender, text, role, metadata) VALUES (?, ?, ?, ?, ?, ?)",
            [$msgId, $sessionId, $channel, $messageText, $role, json_encode(['channel' => $channel, 'senderId' => $senderId])],
            "ssssss"
        );

        echo json_encode([
            'success' => true,
            'action' => 'save_message',
            'messageId' => $msgId,
            'sessionId' => $sessionId
        ]);
        exit();
    }

    // 3. Get Full Conversation History Across Channels
    if ($action === 'get_history') {
        if (empty($sessionId)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'sessionId is required']);
            exit();
        }

        $messages = db_fetch_all("SELECT * FROM chatmessages WHERE sessionId = ? ORDER BY createdAt ASC LIMIT 20", [$sessionId], "s");

        echo json_encode([
            'success' => true,
            'action' => 'get_history',
            'sessionId' => $sessionId,
            'count' => count($messages),
            'messages' => array_map(function($m) {
                return [
                    'id' => $m['id'],
                    'sender' => $m['sender'],
                    'role' => $m['role'],
                    'text' => $m['text'],
                    'timestamp' => $m['createdAt']
                ];
            }, $messages)
        ]);
        exit();
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid session action']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
