<?php
/**
 * ZeyTech — Cryptographic Payment Verification API (Phase 5)
 * Exact contract for payment provider verification.
 * Accepts: raw webhook payload with HMAC-SHA256 signature
 * Returns: { verified: boolean, orderId: number }
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_ZEYTECH_SIGNATURE'] ?? $_SERVER['HTTP_X_SIGNATURE'] ?? $_SERVER['HTTP_X_PAYMENT_SIGNATURE'] ?? '';
$secretKey = getenv('ZEYTECH_WEBHOOK_SECRET') ?: 'zeytech_live_webhook_secret_2026';

$input = json_decode($rawInput, true) ?: [];

$orderId = intval($input['orderId'] ?? $input['order_id'] ?? 0);
$providerEventId = trim($input['eventId'] ?? $input['provider_event_id'] ?? $input['transactionId'] ?? ('evt_' . time()));
$eventType = trim($input['eventType'] ?? $input['event'] ?? 'payment_intent.succeeded');
$amount = floatval($input['amount'] ?? 0.0);
$currency = trim($input['currency'] ?? 'USD');

if ($orderId <= 0) {
    http_response_code(400);
    echo json_encode(['verified' => false, 'error' => 'Valid orderId is required']);
    exit();
}

try {
    // 1. Cryptographic HMAC Signature Verification
    $expectedSignature = hash_hmac('sha256', $rawInput, $secretKey);
    $isSignatureValid = false;

    if (!empty($signature) && hash_equals($expectedSignature, $signature)) {
        $isSignatureValid = true;
    } elseif (!empty($input['signature']) && hash_equals($expectedSignature, $input['signature'])) {
        $isSignatureValid = true;
    }

    if (!$isSignatureValid) {
        http_response_code(401);
        echo json_encode([
            'verified' => false,
            'orderId' => $orderId,
            'error' => 'INVALID_SIGNATURE',
            'message' => 'Cryptographic signature mismatch. Webhook rejected.'
        ]);
        exit();
    }

    $con = get_db_connection();
    mysqli_begin_transaction($con);

    // 2. Idempotent Payment Event Recording
    $isDuplicate = false;
    try {
        $stmt = mysqli_prepare($con, "INSERT INTO payment_events (provider_event_id, order_id, verified, amount, currency, verified_at) VALUES (?, ?, 1, ?, ?, NOW())");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sids", $providerEventId, $orderId, $amount, $currency);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    } catch (Throwable $peErr) {
        if ($peErr->getCode() == 1062 || stripos($peErr->getMessage(), 'Duplicate entry') !== false) {
            $isDuplicate = true;
        } else {
            throw $peErr;
        }
    }

    if ($isDuplicate) {
        mysqli_rollback($con);
        echo json_encode([
            'verified' => true,
            'orderId' => $orderId,
            'status' => 'DUPLICATE_PAYMENT_WEBHOOK_ACKNOWLEDGED',
            'note' => 'Payment event was previously verified and applied.'
        ]);
        exit();
    }

    // 3. Fetch Order Details
    $order = db_fetch_one("SELECT * FROM orders WHERE id = ?", [$orderId], "i");
    if (!$order) {
        mysqli_rollback($con);
        http_response_code(404);
        echo json_encode(['verified' => false, 'orderId' => $orderId, 'error' => 'ORDER_NOT_FOUND']);
        exit();
    }

    $productId = intval($order['productId'] ?? 1);
    $qty = max(1, intval($order['quantity'] ?? 1));

    // 4. Handle Event Types
    if ($eventType === 'payment_intent.succeeded' || $eventType === 'charge.succeeded' || $eventType === 'PAYMENT_SUCCESS') {
        // Convert order to confirmed
        mysqli_query($con, "UPDATE orders SET status = 'confirmed', orderStatus = 'CONFIRMED', paymentStatus = 'PAID' WHERE id = {$orderId}");

        // Convert inventory reservation from reserved to sold
        mysqli_query($con, "UPDATE inventory SET reserved_qty = GREATEST(0, reserved_qty - {$qty}), sold_qty = sold_qty + {$qty} WHERE product_id = {$productId}");
        @mysqli_query($con, "UPDATE products SET stockReserved = GREATEST(0, stockReserved - {$qty}), stockSold = stockSold + {$qty} WHERE id = {$productId}");

        mysqli_commit($con);

        echo json_encode([
            'verified' => true,
            'orderId' => $orderId,
            'status' => 'PAYMENT_SETTLED_AND_CONFIRMED',
            'inventoryUpdated' => 'RESERVED_TO_SOLD',
            'productId' => $productId,
            'quantity' => $qty
        ]);
        exit();
    }

    if ($eventType === 'refund.settled' || $eventType === 'charge.refunded' || $eventType === 'REFUND_SUCCESS') {
        // Only place a refund is ever marked completed
        mysqli_query($con, "UPDATE orders SET status = 'refunded', orderStatus = 'REFUNDED', paymentStatus = 'REFUNDED' WHERE id = {$orderId}");

        // Restock inventory to available if goods were returned
        mysqli_query($con, "UPDATE inventory SET available_qty = available_qty + {$qty}, sold_qty = GREATEST(0, sold_qty - {$qty}) WHERE product_id = {$productId}");

        mysqli_commit($con);

        echo json_encode([
            'verified' => true,
            'orderId' => $orderId,
            'status' => 'REFUND_COMPLETED_AND_SETTLED',
            'inventoryRestocked' => true,
            'productId' => $productId,
            'quantity' => $qty
        ]);
        exit();
    }

    mysqli_commit($con);
    echo json_encode([
        'verified' => true,
        'orderId' => $orderId,
        'status' => 'EVENT_ACKNOWLEDGED'
    ]);

} catch (Exception $e) {
    if (isset($con)) @mysqli_rollback($con);
    http_response_code(500);
    echo json_encode(['verified' => false, 'error' => $e->getMessage()]);
}
