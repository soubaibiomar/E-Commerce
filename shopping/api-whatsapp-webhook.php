<?php
/**
 * ZeyTech AI Commerce OS — WhatsApp Business Cloud API Inbound Webhook
 * Handles Meta webhook verification handshake and real-time customer messaging.
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$verifyToken = getenv('WHATSAPP_VERIFY_TOKEN') ?: 'zeytech_whatsapp_verify_secret_2026';

// 1. Meta Webhook Verification Handshake (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $mode = $_GET['hub_mode'] ?? ($_GET['hub.mode'] ?? '');
    $token = $_GET['hub_verify_token'] ?? ($_GET['hub.verify_token'] ?? '');
    $challenge = $_GET['hub_challenge'] ?? ($_GET['hub.challenge'] ?? '');

    if ($mode === 'subscribe' && $token === $verifyToken) {
        http_response_code(200);
        echo $challenge;
        exit();
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'INVALID_VERIFY_TOKEN']);
        exit();
    }
}

// 2. Inbound Message Ingestion (POST)
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true) ?: [];

// Extract message details (handles both native Meta Cloud API payload and simulated direct payload)
$entry = $data['entry'][0] ?? null;
$changes = $entry['changes'][0]['value'] ?? null;
$messageObj = $changes['messages'][0] ?? ($data['message'] ?? null);
$contact = $changes['contacts'][0] ?? null;

$fromNumber = $messageObj['from'] ?? ($data['from'] ?? ($data['phone'] ?? '+212661000000'));
$customerName = $contact['profile']['name'] ?? ($data['name'] ?? 'WhatsApp Customer');
$messageText = trim($messageObj['text']['body'] ?? ($data['text'] ?? ($data['message'] ?? '')));
$msgId = $messageObj['id'] ?? ('wamid.' . bin2hex(random_bytes(8)));

if (empty($messageText)) {
    echo json_encode(['status' => 'ignored', 'reason' => 'EMPTY_MESSAGE_OR_STATUS_UPDATE']);
    exit();
}

$sessionId = 'wa_' . preg_replace('/[^0-9]/', '', $fromNumber);

// Record inbound WhatsApp customer message in chat_messages table
try {
    db_execute(
        "INSERT INTO chat_messages (session_id, sender_type, sender_name, message, channel, created_at) VALUES (?, 'CUSTOMER', ?, ?, 'WHATSAPP', NOW())",
        [$sessionId, $customerName, $messageText],
        "sss"
    );
} catch (Exception $e) {}

// Direct Product & Stock Grounding
$msgLower = strtolower($messageText);
$isDarija = (preg_match('/(شحال|بشحال|واش|مزيان|الثمن|المخزن|بغيت|ديال|شنو|عفاك|كاين|درهم|خويا)/iu', $messageText) === 1);

// Find matching product
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

    if ($isDarija) {
        $replyText = "مرحباً بك في ZeyTech عبر واتساب! 📱\n\n" .
                     "• **المنتج:** {$pName}\n" .
                     "• **الثمن الرسمي:** " . number_format($priceMAD, 2) . " درهم مغربي (MAD)\n" .
                     "• **المخزون المتوفر:** متوفر في المخزن ({$stock} قطعة جاهزة للإرسال الفوري من مخزن الدار البيضاء Hub-A1).\n" .
                     "• **كوبون خصم:** يمكنك استعمال الكود **ZEYTECH10VIP** للحصول على خصم 10%!\n\n" .
                     "واش بغيتي نثبتو ليك الطلب دابا مع التوصيل السريع لعنوانك؟";
    } else {
        $replyText = "Welcome to ZeyTech WhatsApp Support! 📱\n\n" .
                     "• **Product:** {$pName}\n" .
                     "• **Price:** " . number_format($priceMAD, 2) . " MAD\n" .
                     "• **Casablanca Stock:** In Stock ({$stock} units available at Hub-A1).\n" .
                     "• **VIP Promo:** Use code **ZEYTECH10VIP** at checkout for 10% off.\n\n" .
                     "Would you like us to reserve a unit for express 24h dispatch?";
    }
} else {
    $replyText = "Marhaba! Welcome to ZeyTech Casablanca. How can our AI Sales Engineering team assist you today?";
}

// Record outbound AI reply in chat_messages table
try {
    db_execute(
        "INSERT INTO chat_messages (session_id, sender_type, sender_name, message, channel, created_at) VALUES (?, 'AI_AGENT', 'ZeyTech WhatsApp AI', ?, 'WHATSAPP', NOW())",
        [$sessionId, $replyText],
        "ss"
    );
} catch (Exception $e) {}

// Return structured WhatsApp Cloud API response payload
echo json_encode([
    'success' => true,
    'channel' => 'WHATSAPP',
    'recipient' => $fromNumber,
    'recipientName' => $customerName,
    'sessionId' => $sessionId,
    'messageId' => $msgId,
    'reply' => $replyText,
    'meta_response_format' => [
        'messaging_product' => 'whatsapp',
        'to' => $fromNumber,
        'type' => 'text',
        'text' => ['body' => $replyText]
    ]
]);
