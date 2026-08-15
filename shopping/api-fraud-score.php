<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 14: AI Fraud Scoring & Risk Engine
// Endpoint: POST /api-fraud-score.php
// Connected to Agent 11 (Fraud Detection Agent)
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

$orderId = intval($body['orderId'] ?? 1);
$amountMAD = floatval($body['amountMAD'] ?? 1500.00);
$failedAttempts = intval($body['failedAttempts'] ?? 0);
$isNewDevice = !empty($body['isNewDevice']);
$ipAddress = trim($body['ipAddress'] ?? '196.200.150.10');

// Calculate risk score based on multi-factor heuristics
$score = 5; // Base nominal score
$factors = [];

if ($amountMAD >= 5000) {
    $score += 35;
    $factors[] = 'HIGH_VALUE_TRANSACTION (> 5,000 MAD)';
}

if ($failedAttempts >= 3) {
    $score += 40;
    $factors[] = "MULTIPLE_FAILED_OTP_ATTEMPTS ({$failedAttempts} tries)";
} elseif ($failedAttempts > 0) {
    $score += 15;
    $factors[] = "RETRY_OTP_ATTEMPT ({$failedAttempts} tries)";
}

if ($isNewDevice) {
    $score += 15;
    $factors[] = 'NEW_UNRECOGNIZED_DEVICE';
}

$score = min(100, $score);

// Determine risk level & action
$riskLevel = 'LOW';
$actionTaken = 'AUTO_APPROVED';

if ($score >= 75) {
    $riskLevel = 'CRITICAL';
    $actionTaken = 'FLAGGED_FOR_REVIEW';
} elseif ($score >= 45) {
    $riskLevel = 'HIGH';
    $actionTaken = 'FLAGGED_FOR_REVIEW';
} elseif ($score >= 25) {
    $riskLevel = 'MEDIUM';
    $actionTaken = 'AUTO_APPROVED';
}

$factorsJson = json_encode($factors);

$db = get_db_connection();

// Record risk score
db_execute(
    "INSERT INTO fraud_risk_scores (order_id, risk_score, risk_level, risk_factors, action_taken, created_at)
     VALUES (?, ?, ?, ?, ?, NOW())",
    [$orderId, $score, $riskLevel, $factorsJson, $actionTaken],
    "iisss"
);

$scoreId = mysqli_insert_id($db);

// If flagged, ensure a manager approval ticket exists in ops_approval_queue
if ($actionTaken === 'FLAGGED_FOR_REVIEW') {
    $existingTicket = db_fetch_one("SELECT id FROM ops_approval_queue WHERE target_id = ? AND status = 'PENDING_APPROVAL'", [$orderId], "i");
    if (!$existingTicket) {
        $traceId = 'tr_fraud_' . time() . '_' . rand(100, 999);
        db_execute(
            "INSERT INTO ops_approval_queue (trace_id, customer, channel, amount_mad, reason, flags, status, action_type, target_id, created_at)
             VALUES (?, ?, 'WEB', ?, ?, ?, 'PENDING_APPROVAL', 'HIGH_RISK_ORDER', ?, NOW())",
            [$traceId, 'Customer #' . $orderId, $amountMAD, "Automated Risk Engine Flag: Score {$score}/100 ({$riskLevel})", $factorsJson, $orderId],
            "ssdssi"
        );
    }
}

echo json_encode([
    'success' => true,
    'agent' => 'Agent 11: Fraud Detection Agent',
    'scoreId' => $scoreId,
    'orderId' => $orderId,
    'amountMAD' => $amountMAD,
    'riskScore' => $score,
    'riskLevel' => $riskLevel,
    'actionTaken' => $actionTaken,
    'riskFactors' => $factors,
    'evaluatedAt' => date('Y-m-d H:i:s')
]);
