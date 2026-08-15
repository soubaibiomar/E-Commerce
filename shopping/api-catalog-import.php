<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 12: Automated Product Catalog Ingestion
// Endpoint: POST /api-catalog-import.php
// Bulk product importer with validation & 3-state inventory sync
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
$body = json_decode($rawInput, true) ?? [];

$products = $body['products'] ?? [];
if (empty($products) || !is_array($products)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'INVALID_PAYLOAD',
        'message' => 'Please provide an array of products in payload.'
    ]);
    exit;
}

$importedCount = 0;
$errors = [];
$importedIds = [];

$db = get_db_connection();

foreach ($products as $idx => $p) {
    $name = trim($p['productName'] ?? '');
    $catId = intval($p['categoryId'] ?? 1);
    $price = floatval($p['productPrice'] ?? 0);
    $priceBefore = floatval($p['productPriceBeforeDiscount'] ?? $price);
    $desc = trim($p['productDescription'] ?? '');
    $img = trim($p['productImage1'] ?? 'product_default.jpg');
    $stock = intval($p['stockQuantity'] ?? 10);
    $shipping = floatval($p['shippingCharge'] ?? 0);

    if (empty($name) || $price <= 0) {
        $errors[] = "Item at index {$idx}: Missing name or valid price.";
        continue;
    }

    try {
        // Insert product
        db_execute(
            "INSERT INTO products (category, productName, productCompany, productPrice, productPriceBeforeDiscount, productDescription, productImage1, shippingCharge, postingDate)
             VALUES (?, ?, 'ZeyTech Flagship', ?, ?, ?, ?, ?, NOW())",
            [$catId, $name, $price, $priceBefore, $desc, $img, $shipping],
            "isddssd"
        );
        $newPid = mysqli_insert_id($db);

        // Sync 3-state inventory table
        db_execute(
            "INSERT INTO inventory (product_id, available_qty, reserved_qty, sold_qty, updated_at)
             VALUES (?, ?, 0, 0, NOW())
             ON DUPLICATE KEY UPDATE available_qty = available_qty + VALUES(available_qty), updated_at = NOW()",
            [$newPid, $stock],
            "ii"
        );

        $importedIds[] = $newPid;
        $importedCount++;
    } catch (Throwable $e) {
        $errors[] = "Item at index {$idx} ({$name}): " . $e->getMessage();
    }
}

// Record in audit log
db_execute(
    "INSERT INTO audit_logs (trace_id, actor, channel, sender_id, decision, confidence, reply, created_at)
     VALUES (?, ?, 'CATALOG_IMPORT', ?, 'BULK_IMPORT_COMPLETED', 1.00, ?, NOW())",
    [
        'tr_import_' . time(),
        $staff['name'] . ' (' . ucfirst($staff['role']) . ')',
        'staff_' . $staff['id'],
        "Imported {$importedCount} products into catalog. Errors: " . count($errors)
    ],
    "ssss"
);

echo json_encode([
    'success' => true,
    'importedCount' => $importedCount,
    'importedIds' => $importedIds,
    'errorsCount' => count($errors),
    'errors' => $errors,
    'executedBy' => $staff['name']
]);
