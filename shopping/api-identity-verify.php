<?php
/**
 * ZeyTech — Identity Verification & OTP Challenge API (Phase 3)
 * Exact contract for verifyCustomerIdentity tool node.
 * Actions: request_otp | verify_otp
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: [];

$action = trim($input['action'] ?? 'request_otp');
$identifier = trim($input['identifier'] ?? $input['phone'] ?? $input['email'] ?? '');
$otpCode = trim($input['otpCode'] ?? '');

if (empty($identifier)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'identifier (phone or email) is required']);
    exit();
}

try {
    // 1. Action: request_otp
    if ($action === 'request_otp') {
        // Generate 6-digit code and unique auth token
        $code = str_pad(strval(random_int(100000, 999999)), 6, '0', STR_PAD_LEFT);
        $authToken = 'auth_' . bin2hex(random_bytes(16));
        $expiresAt = date('Y-m-d H:i:s', time() + 600); // 10 minutes TTL

        // Invalidate old unverified challenges for this identifier
        db_execute("DELETE FROM otp_challenges WHERE customer_identifier = ? AND verified_at IS NULL", [$identifier], "s");

        // Insert new challenge
        db_execute(
            "INSERT INTO otp_challenges (customer_identifier, otp_code, auth_token, expires_at) VALUES (?, ?, ?, ?)",
            [$identifier, $code, $authToken, $expiresAt],
            "ssss"
        );

        // Stub/channel dispatch handler (WhatsApp / SMS / Email)
        // In real deployment, dispatch via Twilio / WhatsApp Cloud API / SendGrid
        error_log("[ZeyTech OTP] Sent code {$code} to {$identifier}");

        echo json_encode([
            'success' => true,
            'sent' => true,
            'identifier' => $identifier,
            'expiresAt' => $expiresAt
        ]);
        exit();
    }

    // 2. Action: verify_otp
    if ($action === 'verify_otp') {
        if (empty($otpCode)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'verified' => false, 'error' => 'otpCode is required']);
            exit();
        }

        $challenge = db_fetch_one(
            "SELECT * FROM otp_challenges WHERE customer_identifier = ? AND otp_code = ? AND expires_at > NOW() AND verified_at IS NULL ORDER BY created_at DESC LIMIT 1",
            [$identifier, $otpCode],
            "ss"
        );

        if (!$challenge) {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'verified' => false,
                'error' => 'INVALID_OR_EXPIRED_OTP',
                'message' => 'The verification code provided is incorrect or has expired.'
            ]);
            exit();
        }

        // Mark challenge verified
        $now = date('Y-m-d H:i:s');
        db_execute("UPDATE otp_challenges SET verified_at = ? WHERE id = ?", [$now, $challenge['id']], "si");

        // Mark customer verified if customer exists
        db_execute("UPDATE customers SET verified_at = ? WHERE phone = ? OR email = ?", [$now, $identifier, $identifier], "sss");

        echo json_encode([
            'success' => true,
            'verified' => true,
            'authToken' => $challenge['auth_token'],
            'verifiedAt' => $now,
            'message' => 'Identity successfully verified.'
        ]);
        exit();
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action: must be request_otp or verify_otp']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
