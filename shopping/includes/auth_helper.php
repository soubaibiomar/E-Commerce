<?php
/**
 * ZeyTech AI Commerce OS — Staff Authentication & RBAC Helper (Phase 7)
 */
require_once(__DIR__ . '/config.php');

/**
 * Authenticates the incoming request and optionally checks required roles.
 * Returns the authenticated staff user array on success.
 * Emits HTTP 401 on missing/expired session, or HTTP 403 on insufficient role.
 *
 * @param array $allowedRoles Array of allowed roles (e.g. ['manager', 'admin'])
 * @return array
 */
function require_staff_auth($allowedRoles = []) {
    // 1. Extract bearer token from Authorization header or X-Staff-Token
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token = '';

    if (preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
        $token = trim($matches[1]);
    } elseif (!empty($headers['X-Staff-Token'])) {
        $token = trim($headers['X-Staff-Token']);
    } elseif (!empty($_SERVER['HTTP_X_STAFF_TOKEN'])) {
        $token = trim($_SERVER['HTTP_X_STAFF_TOKEN']);
    }

    if (empty($token)) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'UNAUTHENTICATED',
            'message' => 'Authentication required. Missing Bearer token in Authorization header.'
        ]);
        exit();
    }

    // 2. Validate token against active sessions in database (with expiry check)
    $session = db_fetch_one(
        "SELECT s.*, u.name, u.status as user_status FROM staff_sessions s JOIN staff_users u ON s.staff_id = u.id WHERE s.session_token = ? AND s.expires_at > NOW()",
        [$token],
        "s"
    );

    if (!$session || ($session['user_status'] ?? 'active') !== 'active') {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'error' => 'SESSION_EXPIRED_OR_INVALID',
            'message' => 'Your staff session has expired or is invalid. Please log in again.'
        ]);
        exit();
    }

    // 3. Check Role Privileges if specified
    if (!empty($allowedRoles) && !in_array($session['role'], $allowedRoles, true)) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'error' => 'FORBIDDEN',
            'message' => "Insufficient permissions for role '{$session['role']}'. Required: " . implode(' or ', $allowedRoles)
        ]);
        exit();
    }

    return [
        'staffId' => intval($session['staff_id']),
        'email' => $session['email'],
        'name' => $session['name'],
        'role' => $session['role'],
        'token' => $token
    ];
}
