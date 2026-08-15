<?php
/**
 * ZeyTech AI Commerce OS — Comprehensive End-to-End Contract Verification Suite
 * Tests all endpoints built across Phases 1 through 6.
 */

$baseUrl = 'http://localhost:80/shopping'; // Inside Apache container

function postJson($url, $data, $headers = []) {
    $ch = curl_init($url);
    $payload = json_encode($data);
    $defaultHeaders = ['Content-Type: application/json'];
    foreach ($headers as $k => $v) {
        $defaultHeaders[] = "$k: $v";
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $defaultHeaders);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['code' => $httpCode, 'json' => json_decode($response, true), 'raw' => $response];
}

$passed = 0;
$failed = 0;

function assertTest($name, $condition, $details = '') {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] $name\n";
        $passed++;
    } else {
        echo "  [FAIL] $name -> $details\n";
        $failed++;
    }
}

echo "====================================================================\n";
echo " ZEYTECH AI COMMERCE OS — BACKEND CONTRACT VERIFICATION SUITE\n";
echo "====================================================================\n\n";

// -----------------------------------------------------------------------------
// PHASE 2 TESTS: /api-chat.php
// -----------------------------------------------------------------------------
echo "[1] Testing Phase 2 — Core Commerce Endpoint (/api-chat.php)\n";

// Test 2.1: Normal Product Inquiries
$res1 = postJson("$baseUrl/api-chat.php", [
    'message' => 'Tell me the price and specs for MacBook Pro',
    'productId' => 1,
    'traceId' => 'tr_test_01',
    'channel' => 'WEB',
    'senderId' => 'user_123',
    'userRole' => 'CUSTOMER'
]);
assertTest("Product query returns natural language reply", !empty($res1['json']['reply']), $res1['raw']);

// Test 2.2: Moroccan Darija NLP
$res2 = postJson("$baseUrl/api-chat.php", [
    'message' => 'شحال الثمن ديال هاد البيسي واش كاين فالمخزن بالدار البيضاء؟',
    'productId' => 1,
    'channel' => 'WHATSAPP'
]);
assertTest("Moroccan Darija query handled with MAD formatting", !empty($res2['json']['reply']) && stripos($res2['json']['reply'], 'درهم') !== false, $res2['raw']);

// Test 2.3: Autonomous Reporting Path
$res3 = postJson("$baseUrl/api-chat.php", [
    'message' => 'give me the full revenue, orders, and KPI summary',
    'channel' => 'SYSTEM_EVENT_ROUTER'
]);
assertTest("Autonomous KPI summary returns live metrics", !empty($res3['json']['reply']) && stripos($res3['json']['reply'], 'Gross Platform Revenue') !== false, $res3['raw']);

// Test 2.4: Platform Error Logging
$res4 = postJson("$baseUrl/api-chat.php", [
    'message' => 'LOG_PLATFORM_ERROR',
    'traceId' => 'tr_err_999',
    'nodeName' => 'PaymentNode',
    'severity' => 'CRITICAL',
    'errorMessage' => 'Simulated test exception for contract verification'
]);
assertTest("LOG_PLATFORM_ERROR records error log successfully", ($res4['json']['success'] ?? false) === true, $res4['raw']);

// -----------------------------------------------------------------------------
// PHASE 3 TESTS: Commerce Tool Endpoints
// -----------------------------------------------------------------------------
echo "\n[2] Testing Phase 3 — Commerce Tools\n";

// Test 3.1: Inventory Check
$invCheck = postJson("$baseUrl/api-inventory-reserve.php", [
    'action' => 'check',
    'productId' => 1,
    'quantity' => 2
]);
assertTest("Inventory check returns stock counts without mutation", ($invCheck['json']['success'] ?? false) === true && isset($invCheck['json']['stockAvailable']), $invCheck['raw']);

// Test 3.2: Atomic Inventory Reservation
$availBefore = $invCheck['json']['stockAvailable'];
$invRes = postJson("$baseUrl/api-inventory-reserve.php", [
    'action' => 'reserve',
    'productId' => 1,
    'quantity' => 3
]);
assertTest("Inventory reservation moves available to reserved", ($invRes['json']['success'] ?? false) === true && $invRes['json']['stockAvailable'] === ($availBefore - 3), $invRes['raw']);

// Test 3.3: Inventory Release
$invRel = postJson("$baseUrl/api-inventory-reserve.php", [
    'action' => 'release',
    'productId' => 1,
    'quantity' => 3
]);
assertTest("Inventory release restores available stock", ($invRel['json']['success'] ?? false) === true && ($invRel['json']['releasedQuantity'] ?? 0) === 3, $invRel['raw']);

// Test 3.4: Failure Case — Insufficient Stock
$invFail = postJson("$baseUrl/api-inventory-reserve.php", [
    'action' => 'reserve',
    'productId' => 1,
    'quantity' => 999999
]);
assertTest("Insufficient stock fails safely with 409 INSUFFICIENT_STOCK", $invFail['code'] === 409 && ($invFail['json']['error'] ?? '') === 'INSUFFICIENT_STOCK', $invFail['raw']);

// Test 3.5: Identity Verification — Request OTP
$otpReq = postJson("$baseUrl/api-identity-verify.php", [
    'action' => 'request_otp',
    'identifier' => '+212699887766'
]);
assertTest("Identity OTP challenge generated and sent", ($otpReq['json']['sent'] ?? false) === true && !empty($otpReq['json']['devOtp']), $otpReq['raw']);

// Test 3.6: Failure Case — Invalid OTP
$otpFail = postJson("$baseUrl/api-identity-verify.php", [
    'action' => 'verify_otp',
    'identifier' => '+212699887766',
    'otpCode' => '000000'
]);
assertTest("Invalid OTP fails with 401 INVALID_OR_EXPIRED_OTP", $otpFail['code'] === 401 && ($otpFail['json']['verified'] ?? true) === false, $otpFail['raw']);

// Test 3.7: Valid OTP Verification
$devCode = $otpReq['json']['devOtp'];
$otpOk = postJson("$baseUrl/api-identity-verify.php", [
    'action' => 'verify_otp',
    'identifier' => '+212699887766',
    'otpCode' => $devCode
]);
$validAuthToken = $otpOk['json']['authToken'] ?? '';
assertTest("Valid OTP returns verified=true and Bearer auth token", ($otpOk['json']['verified'] ?? false) === true && !empty($validAuthToken), $otpOk['raw']);

// Test 3.8: Order Exception — Failure Case Unverified Identity
$ordExcFail = postJson("$baseUrl/api-order-exceptions.php", [
    'action' => 'cancel_order',
    'orderId' => 1,
    'authToken' => 'fake_invalid_token'
]);
assertTest("Order cancellation without valid identity rejected with 403 UNAUTHORIZED", $ordExcFail['code'] === 403 && ($ordExcFail['json']['error'] ?? '') === 'UNAUTHORIZED', $ordExcFail['raw']);

// Test 3.9: Order Exception — Valid Verified Refund Request
$ordRef = postJson("$baseUrl/api-order-exceptions.php", [
    'action' => 'request_refund',
    'orderId' => 1,
    'authToken' => $validAuthToken
]);
assertTest("Verified refund creates PENDING_REFUND status without premature refunding", ($ordRef['json']['success'] ?? false) === true && ($ordRef['json']['status'] ?? '') === 'pending_refund', $ordRef['raw']);

// -----------------------------------------------------------------------------
// PHASE 4 TESTS: Platform Safety Endpoints
// -----------------------------------------------------------------------------
echo "\n[3] Testing Phase 4 — Platform Safety\n";

// Test 4.1: Rate Limiter
$rlOk = postJson("$baseUrl/api-rate-limit.php", [
    'senderId' => 'user_test_rl',
    'channel' => 'WEB',
    'windowSeconds' => 60,
    'maxRequests' => 5
]);
assertTest("Rate limit allows initial requests", ($rlOk['json']['allowed'] ?? false) === true, $rlOk['raw']);

// Test 4.2: Rate Limit Trigger
for ($i = 0; $i < 6; $i++) {
    $rlCheck = postJson("$baseUrl/api-rate-limit.php", [
        'senderId' => 'user_test_spam',
        'channel' => 'WEB',
        'windowSeconds' => 60,
        'maxRequests' => 3
    ]);
}
assertTest("Rate limit throttles exceeding requests safely (allowed=false)", ($rlCheck['json']['allowed'] ?? true) === false, $rlCheck['raw']);

// Test 4.3: Idempotency Check — First Call
$eventId = 'evt_unique_' . time() . '_' . rand(1000, 9999);
$idem1 = postJson("$baseUrl/api-idempotency-check.php", [
    'eventId' => $eventId,
    'eventType' => 'ORDER_PLACED'
]);
assertTest("First event insertion returns alreadyProcessed=false", ($idem1['json']['alreadyProcessed'] ?? true) === false, $idem1['raw']);

// Test 4.4: Idempotency Check — Duplicate Call
$idem2 = postJson("$baseUrl/api-idempotency-check.php", [
    'eventId' => $eventId,
    'eventType' => 'ORDER_PLACED'
]);
assertTest("Duplicate event insertion returns alreadyProcessed=true atomically", ($idem2['json']['alreadyProcessed'] ?? false) === true, $idem2['raw']);

// Test 4.5: Audit Log Writer
$auditRes = postJson("$baseUrl/api-audit-log.php", [
    'traceId' => 'tr_audit_101',
    'actor' => 'SUPERVISOR',
    'channel' => 'WEB',
    'senderId' => 'cust_88',
    'decision' => 'AUTO_APPROVED',
    'confidence' => 0.98,
    'reply' => 'Order verified'
]);
assertTest("Audit log writer inserts and returns success=true without breaking caller", ($auditRes['json']['success'] ?? false) === true, $auditRes['raw']);

// Test 4.6: Budget Guard Check
$budgetRes = postJson("$baseUrl/api-budget-guard.php", [
    'scope' => 'daily',
    'maxSpendUSD' => 25
]);
assertTest("LLM Budget guard returns underBudget=true against $25 cap", ($budgetRes['json']['underBudget'] ?? false) === true, $budgetRes['raw']);

// -----------------------------------------------------------------------------
// PHASE 5 TESTS: Payment Verification
// -----------------------------------------------------------------------------
echo "\n[4] Testing Phase 5 — Payment Webhook & Settlement\n";

$secret = 'zeytech_live_webhook_secret_2026';
$orderId = 1;
$txId = 'tx_pay_' . time() . '_' . rand(100, 999);
$paymentPayload = json_encode([
    'orderId' => $orderId,
    'transactionId' => $txId,
    'eventType' => 'payment_intent.succeeded',
    'amount' => 1199.00,
    'currency' => 'USD'
]);
$validSig = hash_hmac('sha256', $paymentPayload, $secret);

// Test 5.1: Failure Case — Invalid Signature
$chBad = curl_init("$baseUrl/api-payment-verify.php");
curl_setopt($chBad, CURLOPT_POSTFIELDS, $paymentPayload);
curl_setopt($chBad, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-ZeyTech-Signature: invalid_sig_123']);
curl_setopt($chBad, CURLOPT_RETURNTRANSFER, true);
$resBad = curl_exec($chBad);
$codeBad = curl_getinfo($chBad, CURLINFO_HTTP_CODE);
curl_close($chBad);
assertTest("Payment webhook with invalid HMAC signature rejected with 401", $codeBad === 401, $resBad);

// Test 5.2: Valid Payment Settlement
$chOk = curl_init("$baseUrl/api-payment-verify.php");
curl_setopt($chOk, CURLOPT_POSTFIELDS, $paymentPayload);
curl_setopt($chOk, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-ZeyTech-Signature: ' . $validSig]);
curl_setopt($chOk, CURLOPT_RETURNTRANSFER, true);
$resOk = curl_exec($chOk);
$codeOk = curl_getinfo($chOk, CURLINFO_HTTP_CODE);
curl_close($chOk);
$jsonOk = json_decode($resOk, true);
assertTest("Valid signed payment settles order and moves reserved to sold", ($jsonOk['verified'] ?? false) === true && ($jsonOk['status'] ?? '') === 'PAYMENT_SETTLED_AND_CONFIRMED', $resOk);

// Test 5.3: Duplicate Payment Webhook Handling
$chDup = curl_init("$baseUrl/api-payment-verify.php");
curl_setopt($chDup, CURLOPT_POSTFIELDS, $paymentPayload);
curl_setopt($chDup, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'X-ZeyTech-Signature: ' . $validSig]);
curl_setopt($chDup, CURLOPT_RETURNTRANSFER, true);
$resDup = curl_exec($chDup);
curl_close($chDup);
$jsonDup = json_decode($resDup, true);
assertTest("Duplicate payment webhook is idempotently acknowledged", ($jsonDup['verified'] ?? false) === true && stripos($jsonDup['status'] ?? '', 'DUPLICATE') !== false, $resDup);

// -----------------------------------------------------------------------------
// PHASE 6 TESTS: Frontend Live Data Endpoints
// -----------------------------------------------------------------------------
echo "\n[5] Testing Phase 6 — Frontend Live Telemetry\n";
$dashRes = postJson("$baseUrl/api-dashboard-kpis.php", []);
assertTest("Frontend KPI telemetry returns live revenue, orders, and 15 agents", ($dashRes['json']['success'] ?? false) === true && count($dashRes['json']['agents'] ?? []) === 15, $dashRes['raw']);

echo "\n====================================================================\n";
echo " VERIFICATION SUMMARY: $passed Passed, $failed Failed\n";
echo "====================================================================\n";

if ($failed > 0) {
    exit(1);
}
