<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 15: AI Demand Forecasting & Stockout Predictor
// Endpoint: GET /api-forecasting-insights.php
// Connected to Agent 10 (Forecasting) & Agent 6 (Inventory)
// =============================================================================

error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once(__DIR__ . '/includes/config.php');

$products = db_fetch_all(
    "SELECT p.id, p.productName, p.productPrice,
            COALESCE(i.available_qty, 0) AS availableStock,
            COALESCE(i.reserved_qty, 0) AS reservedStock,
            COALESCE(i.sold_qty, 0) AS soldStock
     FROM products p
     LEFT JOIN inventory i ON p.id = i.product_id
     ORDER BY p.id ASC"
);

$forecasts = [];
$totalCriticalStockouts = 0;

foreach ($products as $p) {
    $avail = intval($p['availableStock']);
    $sold = intval($p['soldStock']);
    // Simulated 30-day velocity run-rate
    $dailyVelocity = max(0.5, round(($sold > 0 ? $sold / 14.0 : 1.2), 2));
    $daysToStockout = round($avail / $dailyVelocity, 1);
    
    $urgency = 'NOMINAL';
    if ($daysToStockout <= 5) {
        $urgency = 'CRITICAL';
        $totalCriticalStockouts++;
    } elseif ($daysToStockout <= 14) {
        $urgency = 'WARNING';
    }

    $recommendedReorderQty = max(20, round($dailyVelocity * 30)); // 30-day replenishment buffer

    $forecasts[] = [
        'productId' => intval($p['id']),
        'productName' => $p['productName'],
        'availableStock' => $avail,
        'reservedStock' => intval($p['reservedStock']),
        'soldStock' => $sold,
        'estimatedDailyVelocity' => $dailyVelocity,
        'daysToStockout' => $daysToStockout,
        'stockoutUrgency' => $urgency,
        'recommendedReorderQty' => $recommendedReorderQty,
        'estimatedCostMAD' => round($recommendedReorderQty * floatval($p['productPrice']) * 10.20 * 0.70, 2) // wholesale MAD cost
    ];
}

echo json_encode([
    'success' => true,
    'agent' => 'Agent 10: Forecasting Agent & Agent 6: Inventory Agent',
    'fulfillmentHub' => 'Casablanca Central Hub (Hub-A1)',
    'totalProductsAnalyzed' => count($forecasts),
    'criticalStockoutsCount' => $totalCriticalStockouts,
    'insights' => $forecasts,
    'generatedAt' => date('Y-m-d H:i:s')
]);
