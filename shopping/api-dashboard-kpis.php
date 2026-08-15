<?php
/**
 * ZeyTech — Frontend Read-Only Dashboard & Telemetry API (Phase 6)
 * Provides authenticated/read-only live KPI summaries, agent status, and audit ledgers.
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

try {
    // 1. Live Business Metrics
    $prodCountRow = db_fetch_one("SELECT COUNT(*) as c FROM products");
    $orderStats = db_fetch_one("SELECT COUNT(*) as total_orders, COALESCE(SUM(total_amount), 0) as total_rev FROM orders WHERE status != 'cancelled'");
    $invStats = db_fetch_one("SELECT COALESCE(SUM(available_qty), 0) as total_avail, COALESCE(SUM(reserved_qty), 0) as total_res, COALESCE(SUM(sold_qty), 0) as total_sold FROM inventory");
    $recentOrders = db_query("SELECT id, customer_id, status, total_amount, currency, created_at FROM orders ORDER BY id DESC LIMIT 5");

    $skus = intval($prodCountRow['c'] ?? 0);
    $totalOrders = intval($orderStats['total_orders'] ?? 0);
    $revenueUSD = floatval($orderStats['total_rev'] ?? 0);
    $revenueMAD = round($revenueUSD * 10.2, 2);
    $availStock = intval($invStats['total_avail'] ?? 0);
    $resStock = intval($invStats['total_res'] ?? 0);
    $soldStock = intval($invStats['total_sold'] ?? 0);

    // 2. Budget status
    $today = date('Y-m-d');
    $budgetRow = db_fetch_one("SELECT total_spend_usd FROM llm_budget_usage WHERE date = ?", [$today], "s");
    $dailySpend = floatval($budgetRow['total_spend_usd'] ?? 0.0);

    // 3. Recent Audit Logs & Error Logs
    $auditLogs = db_query("SELECT trace_id, actor, channel, sender_id, decision, confidence, created_at FROM audit_logs ORDER BY id DESC LIMIT 8");
    $errorLogs = db_query("SELECT trace_id, node_name, severity, error_message, created_at FROM platform_error_logs ORDER BY id DESC LIMIT 5");

    // 4. 15 Specialized Agents Roster
    $agents = [
        ['id' => 1, 'name' => 'Sales Agent', 'status' => 'ONLINE', 'role' => 'Product discovery, comparison & margin offers', 'channel' => 'Omnichannel'],
        ['id' => 2, 'name' => 'Customer Support Agent', 'status' => 'ONLINE', 'role' => 'FAQs, returns & warranty assistance', 'channel' => 'Web / WA'],
        ['id' => 3, 'name' => 'Product Expert Agent', 'status' => 'ONLINE', 'role' => 'Fiche Technique & 3D WebGL specifications', 'channel' => 'Storefront'],
        ['id' => 4, 'name' => 'Recommendation Agent', 'status' => 'ONLINE', 'role' => 'Personalized & next-best-product embeddings', 'channel' => 'AI Router'],
        ['id' => 5, 'name' => 'Order Management Agent', 'status' => 'ONLINE', 'role' => 'Live tracking & cancellation guardrails', 'channel' => 'Web / WA'],
        ['id' => 6, 'name' => 'Inventory Agent', 'status' => 'ONLINE', 'role' => '3-State stock & warehouse Hub-A1 monitoring', 'channel' => 'Internal'],
        ['id' => 7, 'name' => 'Pricing & Promo Agent', 'status' => 'ONLINE', 'role' => 'Discounts & coupon ZEYTECH10VIP validation', 'channel' => 'Supervisor'],
        ['id' => 8, 'name' => 'Marketing Agent', 'status' => 'ONLINE', 'role' => 'Campaign copy & dynamic customer segmentation', 'channel' => 'Outbound'],
        ['id' => 9, 'name' => 'Analytics Agent', 'status' => 'ONLINE', 'role' => 'Gross platform revenue, AOV & conversion KPIs', 'channel' => 'Cron / BI'],
        ['id' => 10, 'name' => 'Forecasting Agent', 'status' => 'ONLINE', 'role' => 'Demand velocity & 7-day stockout prediction', 'channel' => 'Cron 24h'],
        ['id' => 11, 'name' => 'Fraud Detection Agent', 'status' => 'ONLINE', 'role' => 'Transaction heuristic risk scoring (0-100)', 'channel' => 'Gate Keeper'],
        ['id' => 12, 'name' => 'CRM & Retention Agent', 'status' => 'ONLINE', 'role' => 'Customer 360 profile & lifetime value insights', 'channel' => 'Omnichannel'],
        ['id' => 13, 'name' => 'Content Generation Agent', 'status' => 'ONLINE', 'role' => 'SEO descriptions & Moroccan Darija localization', 'channel' => 'Supervisor'],
        ['id' => 14, 'name' => 'Admin Copilot', 'status' => 'ONLINE', 'role' => 'Executive operational assistant & telemetry summary', 'channel' => 'Telegram'],
        ['id' => 15, 'name' => 'Notification Agent', 'status' => 'ONLINE', 'role' => 'Multi-lingual push notifications & customer dispatch', 'channel' => 'TG / WA']
    ];

    echo json_encode([
        'success' => true,
        'kpis' => [
            'totalRevenueUSD' => $revenueUSD,
            'totalRevenueMAD' => $revenueMAD,
            'totalOrders' => $totalOrders,
            'activeSKUs' => $skus,
            'stockAvailable' => $availStock,
            'stockReserved' => $resStock,
            'stockSold' => $soldStock,
            'dailyLlmSpendUSD' => $dailySpend,
            'dailyLlmCapUSD' => 25.00
        ],
        'agents' => $agents,
        'recentOrders' => $recentOrders ?: [],
        'auditLogs' => $auditLogs ?: [],
        'errorLogs' => $errorLogs ?: [],
        'timestamp' => date('Y-m-d H:i:s')
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
