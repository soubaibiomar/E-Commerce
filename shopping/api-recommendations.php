<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 13: Recommendations & Dynamic Bundles
// Endpoint: POST /api-recommendations.php
// Connected to Agent 4 (Recommendations) & Agent 7 (Pricing & Promo)
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

$productId = intval($body['productId'] ?? 1);
$category = trim($body['category'] ?? '');

// 1. Fetch main product
$mainProduct = db_fetch_one("SELECT * FROM products WHERE id = ?", [$productId], "i");
if (!$mainProduct) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'PRODUCT_NOT_FOUND',
        'message' => "Product #{$productId} not found."
    ]);
    exit;
}

// 2. Fetch complementary cross-sells in same/related category
$crossSells = db_fetch_all(
    "SELECT id, productName, productPrice, productPriceBeforeDiscount, productImage1 
     FROM products 
     WHERE id != ? AND (category = ? OR id IN (2, 3))
     LIMIT 4",
    [$productId, $mainProduct['category']],
    "ii"
);

// 3. Fetch active smart bundles
$bundle = db_fetch_one(
    "SELECT * FROM product_bundles WHERE main_product_id = ? AND status = 'ACTIVE' LIMIT 1",
    [$productId],
    "i"
);

$bundleDetails = null;
if ($bundle) {
    $itemIds = array_filter(array_map('intval', explode(',', $bundle['bundled_product_ids'])));
    $bundledItems = [];
    $totalRegularPrice = floatval($mainProduct['productPrice']);

    if (!empty($itemIds)) {
        $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
        $types = str_repeat('i', count($itemIds));
        $bundledItems = db_fetch_all("SELECT id, productName, productPrice, productImage1 FROM products WHERE id IN ($placeholders)", $itemIds, $types);
        foreach ($bundledItems as $bi) {
            $totalRegularPrice += floatval($bi['productPrice']);
        }
    }

    $discountPct = floatval($bundle['discount_percentage']);
    $discountAmount = $totalRegularPrice * ($discountPct / 100.0);
    $bundlePriceUSD = $totalRegularPrice - $discountAmount;
    $bundlePriceMAD = $bundlePriceUSD * 10.20;

    $bundleDetails = [
        'bundleId' => intval($bundle['id']),
        'bundleName' => $bundle['bundle_name'],
        'discountPercentage' => $discountPct,
        'originalPriceUSD' => round($totalRegularPrice, 2),
        'originalPriceMAD' => round($totalRegularPrice * 10.20, 2),
        'bundlePriceUSD' => round($bundlePriceUSD, 2),
        'bundlePriceMAD' => round($bundlePriceMAD, 2),
        'savingsMAD' => round($discountAmount * 10.20, 2),
        'bundledProducts' => $bundledItems
    ];
}

echo json_encode([
    'success' => true,
    'agent' => 'Agent 4: Recommendations & Agent 7: Pricing',
    'mainProduct' => [
        'id' => intval($mainProduct['id']),
        'name' => $mainProduct['productName'],
        'priceUSD' => floatval($mainProduct['productPrice']),
        'priceMAD' => round(floatval($mainProduct['productPrice']) * 10.20, 2)
    ],
    'recommendedCrossSells' => $crossSells,
    'smartBundle' => $bundleDetails,
    'generatedAt' => date('Y-m-d H:i:s')
]);
