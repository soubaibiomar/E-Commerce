<?php
/**
 * ZeyTech AI Commerce OS — Staff Authentication Login API (Phase 7)
 * Accepts: { email, password }
 * Returns: { success: true, token, user: { id, email, name, role }, expiresAt }
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: [];

$email = trim(strtolower($input['email'] ?? ''));
$password = trim($input['password'] ?? '');

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'MISSING_FIELDS',
        'message' => 'Both email and password are required.'
    ]);
    exit();
}

try {
    // 1. Fetch user from staff_users
    $user = db_fetch_one("SELECT * FROM staff_users WHERE email = ? AND status = 'active' LIMIT 1", [$email], "s");

    if (!$user || !password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'INVALID_CREDENTIALS',
            'message' => 'Invalid email or password.'
        ]);
        exit();
    }

    // 2. Invalidate any older sessions for this user to maintain strict session hygiene
    db_execute("DELETE FROM staff_sessions WHERE staff_id = ? OR expires_at <= NOW()", [$user['id']], "i");

    // 3. Issue new cryptographically secure 4-hour session token
    $token = 'zt_sess_' . bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', time() + (4 * 3600)); // 4 hours TTL

    db_execute(
        "INSERT INTO staff_sessions (session_token, staff_id, email, role, expires_at) VALUES (?, ?, ?, ?, ?)",
        [$token, $user['id'], $user['email'], $user['role'], $expiresAt],
        "sisss"
    );

    echo json_encode([
        'success' => true,
        'token' => $token,
        'user' => [
            'id' => intval($user['id']),
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role']
        ],
        'expiresAt' => $expiresAt,
        'message' => 'Staff session successfully established.'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
