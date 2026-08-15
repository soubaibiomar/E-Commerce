<?php
/**
 * ZeyTech AI Commerce OS — Telegram Bot API Inbound Webhook
 * Ingests updates from Telegram Bot and routes responses.
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$update = json_decode($rawInput, true) ?: [];

$messageObj = $update['message'] ?? ($update['edited_message'] ?? $update);
$chatId = $messageObj['chat']['id'] ?? ($update['chat_id'] ?? null);
$fromObj = $messageObj['from'] ?? [];
$userName = $fromObj['username'] ?? ($fromObj['first_name'] ?? 'Telegram User');
$messageText = trim($messageObj['text'] ?? ($update['text'] ?? ($update['message'] ?? '')));

if (empty($messageText) || empty($chatId)) {
    echo json_encode(['ok' => false, 'description' => 'NO_TEXT_OR_CHAT_ID_PROVIDED']);
    exit();
}

$sessionId = 'tg_' . $chatId;

// Record inbound Telegram customer message in chat_messages table
try {
    db_execute(
        "INSERT INTO chat_messages (session_id, sender_type, sender_name, message, channel, created_at) VALUES (?, 'CUSTOMER', ?, ?, 'TELEGRAM', NOW())",
        [$sessionId, $userName, $messageText],
        "sss"
    );
} catch (Exception $e) {}

// Direct Product Grounding
$msgLower = strtolower($messageText);
$allProds = db_fetch_all("SELECT p.*, i.available_qty FROM products p LEFT JOIN inventory i ON p.id = i.product_id");
$matched = null;
foreach ($allProds as $p) {
    $pWords = explode(' ', strtolower($p['productName']));
    if (stripos($msgLower, strtolower($pWords[0])) !== false && (count($pWords) < 2 || stripos($msgLower, strtolower($pWords[1])) !== false)) {
        $matched = $p;
        break;
    }
}
if (!$matched && !empty($allProds)) {
    $matched = $allProds[0];
}

if ($matched) {
    $pName = $matched['productName'];
    $priceMAD = floatval($matched['productPrice']);
    $stock = intval($matched['available_qty'] ?? 0);
    $specs = json_decode($matched['specifications'] ?? '{}', true) ?: [];

    $replyText = "🤖 **ZeyTech Telegram Hardware Desk:**\n\n" .
                 "• **Product:** {$pName}\n" .
                 "• **Price:** " . number_format($priceMAD, 2) . " MAD ($" . number_format(round($priceMAD / 10.2, 2)) . " USD)\n" .
                 "• **Casablanca Stock:** In Stock ({$stock} units at Hub-A1)\n" .
                 "• **Key Specs:** " . implode(' | ', array_slice(array_values($specs), 0, 3)) . "\n\n" .
                 "Feel free to ask for detailed Fiche Technique specs or place an order.";
} else {
    $replyText = "🤖 Welcome to ZeyTech Telegram Assistant. How can we assist you with hardware or logistics today?";
}

// Record outbound AI reply in chat_messages table
try {
    db_execute(
        "INSERT INTO chat_messages (session_id, sender_type, sender_name, message, channel, created_at) VALUES (?, 'AI_AGENT', 'ZeyTech Telegram Bot', ?, 'TELEGRAM', NOW())",
        [$sessionId, $replyText],
        "ss"
    );
} catch (Exception $e) {}

// Return structured Telegram Bot API response payload
echo json_encode([
    'ok' => true,
    'method' => 'sendMessage',
    'chat_id' => $chatId,
    'text' => $replyText,
    'parse_mode' => 'Markdown',
    'sessionId' => $sessionId,
    'sender' => $userName
]);
