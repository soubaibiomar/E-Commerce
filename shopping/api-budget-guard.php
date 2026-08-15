<?php
/**
 * ZeyTech — LLM Budget Guard API (Phase 4)
 * Exact contract for budget-guard-check node.
 * Accepts: { scope: "daily", maxSpendUSD: 25 }
 * Returns: { underBudget: boolean }
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: [];

$action = trim($input['action'] ?? 'check');
$maxSpendUSD = floatval($input['maxSpendUSD'] ?? 25.0);

try {
    $today = date('Y-m-d');

    // 1. Action: increment spend
    if ($action === 'increment' || isset($input['spendUSD'])) {
        $spendUSD = floatval($input['spendUSD'] ?? 0.0);
        db_execute(
            "INSERT INTO llm_budget_usage (date, total_spend_usd) VALUES (?, ?) ON DUPLICATE KEY UPDATE total_spend_usd = total_spend_usd + ?",
            [$today, $spendUSD, $spendUSD],
            "sdd"
        );

        $row = db_fetch_one("SELECT total_spend_usd FROM llm_budget_usage WHERE date = ?", [$today], "s");
        echo json_encode([
            'success' => true,
            'action' => 'increment',
            'today' => $today,
            'totalSpendUSD' => floatval($row['total_spend_usd'] ?? 0.0)
        ]);
        exit();
    }

    // 2. Action: check budget
    $row = db_fetch_one("SELECT total_spend_usd FROM llm_budget_usage WHERE date = ?", [$today], "s");
    $currentSpend = floatval($row['total_spend_usd'] ?? 0.0);

    $underBudget = ($currentSpend < $maxSpendUSD);

    echo json_encode([
        'underBudget' => $underBudget,
        'currentSpendUSD' => $currentSpend,
        'maxSpendUSD' => $maxSpendUSD,
        'remainingBudgetUSD' => max(0.0, $maxSpendUSD - $currentSpend),
        'date' => $today
    ]);

} catch (Exception $e) {
    // Fail safe — allow turn if database error
    echo json_encode([
        'underBudget' => true,
        'fallback' => true,
        'error' => $e->getMessage()
    ]);
}
