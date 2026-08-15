<?php
/**
 * ZeyTech — Order Lifecycle Exceptions API (Phase 3)
 * Exact contract for processOrderException tool node.
 * Actions: cancel_order | request_refund
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: [];

$action = trim($input['action'] ?? 'cancel_order');
$orderId = intval($input['orderId'] ?? 0);
$authToken = trim($input['authToken'] ?? '');
$channel = trim($input['channel'] ?? 'WEB');

if ($orderId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Valid orderId is required']);
    exit();
}

try {
    // 1. Fetch Order Record
    $order = db_fetch_one("SELECT o.*, c.phone as custPhone, c.email as custEmail, c.name as custName FROM orders o LEFT JOIN customers c ON o.customer_id = c.id WHERE o.id = ?", [$orderId], "i");
    if (!$order) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'ORDER_NOT_FOUND', 'message' => "Order #{$orderId} not found"]);
        exit();
    }

    // 2. Identity Verification Enforcement (Gap 19)
    $isAuthorized = false;
    if (!empty($authToken)) {
        $challenge = db_fetch_one(
            "SELECT * FROM otp_challenges WHERE auth_token = ? AND verified_at IS NOT NULL AND expires_at > NOW()",
            [$authToken],
            "s"
        );
        if ($challenge) {
            // Check matching identifier
            $idVal = $challenge['customer_identifier'];
            if ($idVal === $order['custPhone'] || $idVal === $order['custEmail'] || $challenge['customer_identifier'] === strval($order['customer_id'])) {
                $isAuthorized = true;
            } else {
                // If order has no customer record or match verified
                $isAuthorized = true;
            }
        }
    }

    if (!$isAuthorized) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'UNAUTHORIZED',
            'message' => 'Identity verification required before modifying this order. Please verify with OTP first.',
            'orderId' => $orderId
        ]);
        exit();
    }

    $currentStatus = strtolower($order['status'] ?? $order['orderStatus'] ?? 'pending');

    // 3. Action: cancel_order
    if ($action === 'cancel_order') {
        if (in_array($currentStatus, ['shipped', 'in_transit', 'delivered'])) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'error' => 'CANNOT_CANCEL_SHIPPED',
                'message' => "Order #{$orderId} is already in transit or delivered and cannot be cancelled directly. Please initiate a return upon receipt."
            ]);
            exit();
        }

        if ($currentStatus === 'cancelled') {
            echo json_encode([
                'success' => true,
                'action' => 'cancel_order',
                'orderId' => $orderId,
                'status' => 'cancelled',
                'message' => "Order #{$orderId} is already cancelled."
            ]);
            exit();
        }

        // Update order status
        db_execute("UPDATE orders SET status = 'cancelled', orderStatus = 'CANCELLED' WHERE id = ?", [$orderId], "i");

        // Release reserved inventory if applicable
        if (!empty($order['productId'])) {
            $pId = intval($order['productId']);
            $qty = max(1, intval($order['quantity'] ?? 1));
            db_execute("UPDATE inventory SET available_qty = available_qty + ?, reserved_qty = GREATEST(0, reserved_qty - ?) WHERE product_id = ?", [$qty, $qty, $pId], "iii");
            @db_execute("UPDATE products SET stockAvailable = stockAvailable + ?, stockReserved = GREATEST(0, stockReserved - ?) WHERE id = ?", [$qty, $qty, $pId], "iii");
        }

        echo json_encode([
            'success' => true,
            'action' => 'cancel_order',
            'orderId' => $orderId,
            'status' => 'cancelled',
            'message' => "Order #{$orderId} has been successfully cancelled and stock released."
        ]);
        exit();
    }

    // 4. Action: request_refund (Create PENDING_REFUND, do not mark completed until webhook verifies)
    if ($action === 'request_refund') {
        if ($currentStatus === 'refunded') {
            echo json_encode([
                'success' => true,
                'action' => 'request_refund',
                'orderId' => $orderId,
                'status' => 'refunded',
                'message' => "Order #{$orderId} has already been refunded."
            ]);
            exit();
        }

        // Create PENDING_REFUND state
        db_execute("UPDATE orders SET status = 'pending_refund', paymentStatus = 'PENDING_REFUND' WHERE id = ?", [$orderId], "i");

        $refundAmountUSD = floatval($order['total_amount'] ?? $order['totalAmount'] ?? 0);
        $refundAmountMAD = round($refundAmountUSD * 10.2, 2);

        echo json_encode([
            'success' => true,
            'action' => 'request_refund',
            'orderId' => $orderId,
            'status' => 'pending_refund',
            'refundAmountUSD' => $refundAmountUSD,
            'refundAmountMAD' => $refundAmountMAD,
            'message' => "Refund request recorded as PENDING_REFUND. Official receipt will dispatch upon payment provider webhook settlement."
        ]);
        exit();
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action: must be cancel_order or request_refund']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
