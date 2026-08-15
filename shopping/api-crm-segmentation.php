<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 16: Customer RFM Segmentation Engine
// Endpoint: GET /api-crm-segmentation.php
// Connected to Agent 12 (CRM & Retention Agent)
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

$customers = db_fetch_all(
    "SELECT u.id, u.name, u.email, u.contactno,
            COUNT(o.id) AS totalOrders,
            COALESCE(SUM(p.productPrice * o.quantity), 0) AS totalSpendUSD,
            COALESCE(SUM(p.productPrice * o.quantity * 10.20), 0) AS totalSpendMAD
     FROM users u
     LEFT JOIN orders o ON u.id = o.userId
     LEFT JOIN products p ON o.productId = p.id
     GROUP BY u.id
     ORDER BY totalSpendUSD DESC"
);

$segments = [
    'VIP_HIGH_SPEND' => [],
    'ACTIVE_REGULAR' => [],
    'CHURN_RISK' => [],
    'NEW_LEAD' => []
];

foreach ($customers as $c) {
    $orders = intval($c['totalOrders']);
    $spendMAD = floatval($c['totalSpendMAD']);

    $customerData = [
        'id' => intval($c['id']),
        'name' => $c['name'],
        'email' => $c['email'],
        'phone' => $c['contactno'] ?: '+212600112233',
        'totalOrders' => $orders,
        'totalSpendMAD' => round($spendMAD, 2)
    ];

    if ($spendMAD >= 10000 || $orders >= 5) {
        $segments['VIP_HIGH_SPEND'][] = $customerData;
    } elseif ($orders >= 1) {
        $segments['ACTIVE_REGULAR'][] = $customerData;
    } else {
        $segments['NEW_LEAD'][] = $customerData;
    }
}

echo json_encode([
    'success' => true,
    'agent' => 'Agent 12: CRM & Retention Agent',
    'totalAudience' => count($customers),
    'segmentBreakdown' => [
        'VIP_HIGH_SPEND' => count($segments['VIP_HIGH_SPEND']),
        'ACTIVE_REGULAR' => count($segments['ACTIVE_REGULAR']),
        'CHURN_RISK' => count($segments['CHURN_RISK']),
        'NEW_LEAD' => count($segments['NEW_LEAD'])
    ],
    'segments' => $segments,
    'generatedAt' => date('Y-m-d H:i:s')
]);
