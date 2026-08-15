<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 11: Domestic Carrier Tracking Webhook
// Endpoint: POST /api-shipping-webhook.php
// Ingests live carrier checkpoint scans from CTM, Amana, and Aramex
// =============================================================================

error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Carrier-Signature');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$body = json_decode($rawInput, true) ?? $_POST;

$trackingNumber = trim($body['trackingNumber'] ?? '');
$newStatus = strtoupper(trim($body['status'] ?? 'IN_TRANSIT'));
$location = trim($body['currentLocation'] ?? 'Agence Centrale Casablanca');
$notes = trim($body['notes'] ?? 'Colis pris en charge par le transporteur.');

if (empty($trackingNumber)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'MISSING_TRACKING_NUMBER',
        'message' => 'Tracking number is required.'
    ]);
    exit;
}

$validStatuses = ['LABEL_CREATED', 'IN_TRANSIT', 'OUT_FOR_DELIVERY', 'DELIVERED', 'RETURNED'];
if (!in_array($newStatus, $validStatuses)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'INVALID_STATUS',
        'message' => 'Status must be one of: ' . implode(', ', $validStatuses)
    ]);
    exit;
}

$shipment = db_fetch_one("SELECT * FROM shipping_shipments WHERE tracking_number = ?", [$trackingNumber], "s");
if (!$shipment) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'SHIPMENT_NOT_FOUND',
        'message' => "Shipment with tracking number {$trackingNumber} not found."
    ]);
    exit;
}

// Update shipment status
db_execute(
    "UPDATE shipping_shipments SET status = ?, updated_at = NOW() WHERE tracking_number = ?",
    [$newStatus, $trackingNumber],
    "ss"
);

// If delivered, update order status
if ($newStatus === 'DELIVERED') {
    db_execute("UPDATE orders SET orderStatus = 'DELIVERED' WHERE id = ?", [$shipment['order_id']], "i");
}

// Write to audit log
db_execute(
    "INSERT INTO audit_logs (trace_id, actor, channel, sender_id, decision, confidence, reply, created_at)
     VALUES (?, ?, 'CARRIER_WEBHOOK', ?, ?, 1.00, ?, NOW())",
    [
        'tr_track_' . $shipment['id'],
        $shipment['carrier'],
        'order_' . $shipment['order_id'],
        'CARRIER_' . $newStatus,
        "Carrier update at {$location}: {$notes}"
    ],
    "sssss"
);

echo json_encode([
    'success' => true,
    'trackingNumber' => $trackingNumber,
    'orderId' => $shipment['order_id'],
    'carrier' => $shipment['carrier'],
    'previousStatus' => $shipment['status'],
    'currentStatus' => $newStatus,
    'checkpointLocation' => $location,
    'updatedAt' => date('Y-m-d H:i:s')
]);
