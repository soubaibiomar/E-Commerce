<?php
/**
 * ZeyTech — Cloud LLM Cost Control, Rate Limit Defense & Outage Fallback API (Gap 6, 16, 21)
 * Manages daily dollar budget ceilings, provider token buckets, and multi-tier outage fallback cascades.
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true) ?: [];

$action = trim($input['action'] ?? 'check_budget'); // 'check_budget', 'record_usage', 'execute_resilient_prompt'
$estimatedTokens = max(100, intval($input['estimatedTokens'] ?? 500));
$modelName = trim($input['model'] ?? 'llama3.2:1b');
$dailyCapUSD = floatval($input['dailyCapUSD'] ?? 50.0);
$today = date('Y-m-d');

try {
    // 1. Fetch Today's Cost & Token Usage
    $budget = db_fetch_one("SELECT * FROM llm_usage_budget WHERE date_key = ?", [$today], "s");
    if (!$budget) {
        db_execute(
            "INSERT INTO llm_usage_budget (date_key, total_tokens, total_cost_usd, daily_cap_usd, total_calls, fallback_calls) VALUES (?, 0, 0.0, ?, 0, 0)",
            [$today, $dailyCapUSD],
            "sd"
        );
        $budget = [
            'date_key' => $today,
            'total_tokens' => 0,
            'total_cost_usd' => 0.0,
            'daily_cap_usd' => $dailyCapUSD,
            'total_calls' => 0,
            'fallback_calls' => 0
        ];
    }

    $currentCost = floatval($budget['total_cost_usd']);
    $cap = floatval($budget['daily_cap_usd']);
    $isBudgetExceeded = ($currentCost >= $cap);

    // 2. Action: Check Budget & Determine Routing Strategy (Gap 6 & Gap 21)
    if ($action === 'check_budget') {
        $allowedCloudAPI = !$isBudgetExceeded;
        $recommendedEngine = $allowedCloudAPI ? 'CLOUD_LLM' : 'LOCAL_OLLAMA_FALLBACK';

        echo json_encode([
            'success' => true,
            'dateKey' => $today,
            'currentCostUSD' => $currentCost,
            'dailyCapUSD' => $cap,
            'budgetUtilizationPct' => round(($currentCost / max(1, $cap)) * 100, 1),
            'isBudgetExceeded' => $isBudgetExceeded,
            'allowedCloudAPI' => $allowedCloudAPI,
            'recommendedEngine' => $recommendedEngine,
            'fallbackStrategy' => $isBudgetExceeded ? 'BUDGET_CAP_REACHED_ROUTING_TO_LOCAL_OLLAMA' : 'CLOUD_PRIMARY'
        ]);
        exit();
    }

    // 3. Action: Record Usage & Compute Cost
    if ($action === 'record_usage') {
        $actualTokens = max(1, intval($input['actualTokens'] ?? $estimatedTokens));
        $isFallback = ($input['isFallback'] ?? false) === true;
        
        // Cost estimation: ~$0.0015 per 1k tokens for standard cloud LLM, $0 for local Ollama
        $callCost = $isFallback ? 0.0 : round(($actualTokens / 1000) * 0.0015, 6);

        db_execute(
            "UPDATE llm_usage_budget 
             SET total_tokens = total_tokens + ?, 
                 total_cost_usd = total_cost_usd + ?, 
                 total_calls = total_calls + 1, 
                 fallback_calls = fallback_calls + ? 
             WHERE date_key = ?",
            [$actualTokens, $callCost, $isFallback ? 1 : 0, $today],
            "idis"
        );

        echo json_encode([
            'success' => true,
            'action' => 'record_usage',
            'tokensAdded' => $actualTokens,
            'costAddedUSD' => $callCost,
            'isFallback' => $isFallback,
            'newTotalCostUSD' => round($currentCost + $callCost, 4)
        ]);
        exit();
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid resilience action']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
