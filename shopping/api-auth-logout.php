<?php
/**
 * ZeyTech AI Commerce OS — Staff Authentication Logout API (Phase 7)
 * Accepts: Bearer token in Authorization header or JSON payload { token }
 * Returns: { success: true }
 */
header('Content-Type: application/json; charset=utf-8');
require_once(__DIR__ . '/includes/config.php');

$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true) ?: [];

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = trim($input['token'] ?? '');

if (empty($token) && preg_match('/Bearer\s+(\S+)/i', $authHeader, $matches)) {
    $token = trim($matches[1]);
}

if (!empty($token)) {
    db_execute("DELETE FROM staff_sessions WHERE session_token = ?", [$token], "s");
}

echo json_encode([
    'success' => true,
    'message' => 'Staff session terminated successfully.'
]);
