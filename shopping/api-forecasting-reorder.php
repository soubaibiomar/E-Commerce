<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 15: Automated Stock Replenishment PO
// Endpoint: POST /api-forecasting-reorder.php
// Creates automated restock orders & reconciles inventory at Hub-A1
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

$staff = require_staff_auth(['manager', 'admin']);

$rawInput = file_get_contents('php://input');
$body = json_decode($rawInput, true) ?? $_POST;

$productId = intval($body['productId'] ?? 0);
$quantity = intval($body['quantity'] ?? 50);
$supplier = trim($body['supplierName'] ?? 'ZeyTech Global Supply');
$costMAD = floatval($body['costMAD'] ?? 5000.00);

if ($productId <= 0 || $quantity <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'INVALID_PARAMETERS',
        'message' => 'Valid productId and quantity are required.'
    ]);
    exit;
}

$product = db_fetch_one("SELECT * FROM products WHERE id = ?", [$productId], "i");
if (!$product) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'PRODUCT_NOT_FOUND',
        'message' => "Product #{$productId} not found."
    ]);
    exit;
}

$db = get_db_connection();

// 1. Insert reorder record
db_execute(
    "INSERT INTO inventory_reorders (product_id, quantity_ordered, supplier_name, cost_mad, status, created_at)
     VALUES (?, ?, ?, ?, 'RECEIVED', NOW())",
    [$productId, $quantity, $supplier, $costMAD],
    "iisd"
);
$reorderId = mysqli_insert_id($db);

// 2. Increment available inventory
db_execute(
    "INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty, updated_at)
     VALUES (?, ?, 0, 0, NOW())
     ON DUPLICATE KEY UPDATE available_qty = available_qty + VALUES(available_qty), updated_at = NOW()",
    [$productId, $quantity],
    "ii"
);

// 3. Write to audit logs
db_execute(
    "INSERT INTO audit_logs (trace_id, actor, channel, sender_id, decision, confidence, reply, created_at)
     VALUES (?, ?, 'RESTOCK_ENGINE', ?, 'INVENTORY_REORDER_RECEIVED', 1.00, ?, NOW())",
    [
        'tr_reorder_' . $reorderId,
        $staff['name'] . ' (' . ucfirst($staff['role']) . ')',
        'staff_' . $staff['id'],
        "Restocked {$quantity} units for {$product['productName']} (PO #{$reorderId}). Total Cost: {$costMAD} MAD."
    ],
    "ssss"
);

// Fetch updated inventory count
$inv = db_fetch_one("SELECT available_qty FROM inventory WHERE product_id = ?", [$productId], "i");

echo json_encode([
    'success' => true,
    'reorderId' => $reorderId,
    'productId' => $productId,
    'productName' => $product['productName'],
    'quantityRestocked' => $quantity,
    'currentAvailableStock' => intval($inv['available_qty'] ?? $quantity),
    'costMAD' => $costMAD,
    'authorizedBy' => $staff['name'],
    'message' => "Successfully restocked {$quantity} units for {$product['productName']}."
]);
