<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 13: Bundle Application & Promo Validation
// Endpoint: POST /api-bundle-apply.php
// Validates and applies dynamic bundle discounts to cart checkout
// =============================================================================

error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$body = json_decode($rawInput, true) ?? $_POST;

$bundleId = intval($body['bundleId'] ?? 0);
if ($bundleId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'MISSING_BUNDLE_ID',
        'message' => 'Valid bundleId is required.'
    ]);
    exit;
}

$bundle = db_fetch_one("SELECT * FROM product_bundles WHERE id = ? AND status = 'ACTIVE'", [$bundleId], "i");
if (!$bundle) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'BUNDLE_NOT_FOUND',
        'message' => "Bundle #{$bundleId} is invalid or inactive."
    ]);
    exit;
}

$mainProduct = db_fetch_one("SELECT * FROM products WHERE id = ?", [$bundle['main_product_id']], "i");
$itemIds = array_filter(array_map('intval', explode(',', $bundle['bundled_product_ids'])));
$totalRegularPrice = floatval($mainProduct['productPrice'] ?? 0);

if (!empty($itemIds)) {
    $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
    $types = str_repeat('i', count($itemIds));
    $items = db_fetch_all("SELECT productPrice FROM products WHERE id IN ($placeholders)", $itemIds, $types);
    foreach ($items as $it) {
        $totalRegularPrice += floatval($it['productPrice']);
    }
}

$discountPct = floatval($bundle['discount_percentage']);
$discountAmount = $totalRegularPrice * ($discountPct / 100.0);
$finalPriceUSD = $totalRegularPrice - $discountAmount;
$finalPriceMAD = $finalPriceUSD * 10.20;

echo json_encode([
    'success' => true,
    'appliedBundle' => [
        'bundleId' => intval($bundle['id']),
        'bundleName' => $bundle['bundle_name'],
        'discountPercentage' => $discountPct,
        'originalTotalUSD' => round($totalRegularPrice, 2),
        'originalTotalMAD' => round($totalRegularPrice * 10.20, 2),
        'discountedTotalUSD' => round($finalPriceUSD, 2),
        'discountedTotalMAD' => round($finalPriceMAD, 2),
        'totalSavingsMAD' => round($discountAmount * 10.20, 2)
    ],
    'message' => "Bundle '{$bundle['bundle_name']}' successfully applied with {$discountPct}% discount."
]);
