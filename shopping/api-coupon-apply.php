<?php
/**
 * ZeyTech AI Commerce OS — Dynamic Promo & Coupon Engine
 * Validates promotional discount codes, calculates regional savings, and outputs multi-currency settlement.
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: [];

$code = strtoupper(trim($input['couponCode'] ?? ($input['code'] ?? ($_GET['code'] ?? ''))));
$subtotalMAD = floatval($input['subtotal'] ?? ($input['amount'] ?? 0));
$shippingMAD = floatval($input['shipping'] ?? 0);
$cartItems = $input['items'] ?? [];

if (empty($code)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'COUPON_CODE_REQUIRED', 'message' => 'Please enter a valid coupon code.']);
    exit();
}

$validCoupons = [
    'ZEYTECH10VIP' => [
        'type' => 'PERCENT',
        'value' => 10,
        'minOrder' => 0,
        'desc' => '10% VIP Executive Discount across all hardware categories',
        'freeShipping' => false
    ],
    'MAROC2026' => [
        'type' => 'FIXED',
        'value' => 250,
        'minOrder' => 2000,
        'desc' => '250 MAD Flat Discount on orders over 2,000 MAD',
        'freeShipping' => false
    ],
    'HUB_CASABLANCA' => [
        'type' => 'FREE_SHIPPING',
        'value' => 0,
        'minOrder' => 500,
        'desc' => '100% Free Express Delivery across Morocco from Casablanca Central Hub-A1',
        'freeShipping' => true
    ],
    'RAMADAN2026' => [
        'type' => 'PERCENT',
        'value' => 15,
        'minOrder' => 1000,
        'desc' => '15% Seasonal Festive Discount on Smart Home & Audio Acoustics',
        'freeShipping' => false
    ]
];

if (!isset($validCoupons[$code])) {
    http_response_code(404);
    echo json_encode([
        'success' => false,
        'error' => 'INVALID_COUPON_CODE',
        'message' => "Coupon '{$code}' is invalid or has expired."
    ]);
    exit();
}

$coupon = $validCoupons[$code];

if ($subtotalMAD < $coupon['minOrder']) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'error' => 'MINIMUM_ORDER_NOT_MET',
        'message' => "Coupon '{$code}' requires a minimum order of " . number_format($coupon['minOrder']) . " MAD. Your current subtotal is " . number_format($subtotalMAD) . " MAD."
    ]);
    exit();
}

$discountAmountMAD = 0;
$finalShippingMAD = $shippingMAD;

if ($coupon['type'] === 'PERCENT') {
    $discountAmountMAD = round(($subtotalMAD * $coupon['value']) / 100, 2);
} elseif ($coupon['type'] === 'FIXED') {
    $discountAmountMAD = min($subtotalMAD, floatval($coupon['value']));
}

if ($coupon['freeShipping']) {
    $finalShippingMAD = 0;
}

$finalTotalMAD = max(0, ($subtotalMAD - $discountAmountMAD) + $finalShippingMAD);

echo json_encode([
    'success' => true,
    'couponCode' => $code,
    'description' => $coupon['desc'],
    'originalSubtotalMAD' => $subtotalMAD,
    'discountAmountMAD' => $discountAmountMAD,
    'originalShippingMAD' => $shippingMAD,
    'finalShippingMAD' => $finalShippingMAD,
    'finalTotalMAD' => $finalTotalMAD,
    'multiCurrency' => [
        'MAD' => $finalTotalMAD,
        'USD' => round($finalTotalMAD / 10.20, 2),
        'EUR' => round($finalTotalMAD / 11.10, 2)
    ],
    'savingsSummary' => "You saved " . number_format($discountAmountMAD + ($shippingMAD - $finalShippingMAD), 2) . " MAD with code {$code}!"
]);
