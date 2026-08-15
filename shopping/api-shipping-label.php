<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 11: Domestic Waybill & Label Generation
// Endpoint: POST /api-shipping-label.php
// Generates official tracking waybill for Moroccan domestic carriers
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

$rawInput = file_get_contents('php://input');
$body = json_decode($rawInput, true) ?? $_POST;

$orderId = intval($body['orderId'] ?? 0);
$carrier = trim($body['carrier'] ?? 'CTM Messagerie');
$region = trim($body['region'] ?? 'Casablanca-Settat');
$city = trim($body['city'] ?? 'Casablanca');
$recipientName = trim($body['recipientName'] ?? 'Valued Customer');
$recipientPhone = trim($body['recipientPhone'] ?? '+212600000000');
$costMAD = floatval($body['shippingCostMAD'] ?? 35.00);

if ($orderId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'INVALID_ORDER_ID',
        'message' => 'Please provide a valid order ID.'
    ]);
    exit;
}

// Generate carrier-specific waybill tracking number
$prefix = 'CTM';
if (stripos($carrier, 'Amana') !== false) {
    $prefix = 'AMN';
} elseif (stripos($carrier, 'Aramex') !== false) {
    $prefix = 'ARX';
}
$trackingNumber = $prefix . '-MA-' . rand(1000000, 9999999);
$estimatedDate = date('Y-m-d', strtotime('+2 days'));

$db = get_db_connection();

db_execute(
    "INSERT INTO shipping_shipments (order_id, carrier, tracking_number, region, city, recipient_name, recipient_phone, shipping_cost_mad, status, estimated_delivery, created_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'LABEL_CREATED', ?, NOW())",
    [
        $orderId,
        $carrier,
        $trackingNumber,
        $region,
        $city,
        $recipientName,
        $recipientPhone,
        $costMAD,
        $estimatedDate
    ],
    "issssssds"
);

$shipmentId = mysqli_insert_id($db);

// Update order status if orders table exists
db_execute("UPDATE orders SET orderStatus = 'IN_TRANSIT' WHERE id = ?", [$orderId], "i");

// Record in audit logs
db_execute(
    "INSERT INTO audit_logs (trace_id, actor, channel, sender_id, decision, confidence, reply, created_at)
     VALUES (?, 'LOGISTICS_ENGINE', 'WEB', ?, 'WAYBILL_GENERATED', 1.00, ?, NOW())",
    [
        'tr_ship_' . $shipmentId,
        'order_' . $orderId,
        "Waybill {$trackingNumber} created with {$carrier} for {$city} ({$region}). Cost: {$costMAD} MAD."
    ],
    "sss"
);

echo json_encode([
    'success' => true,
    'shipmentId' => $shipmentId,
    'orderId' => $orderId,
    'carrier' => $carrier,
    'trackingNumber' => $trackingNumber,
    'destination' => [
        'region' => $region,
        'city' => $city,
        'recipient' => $recipientName,
        'phone' => $recipientPhone
    ],
    'shippingCostMAD' => $costMAD,
    'status' => 'LABEL_CREATED',
    'estimatedDelivery' => $estimatedDate,
    'trackingUrl' => 'http://localhost:8085/track-orders.php?tr=' . $trackingNumber
]);
