<?php
// Centralized Security Utility Functions

// Ensure session is started with secure settings
if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        ini_set('session.cookie_httponly', '1');
        ini_set('session.use_only_cookies', '1');
        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
    }
    session_start();
}

/**
 * Safe HTML escaping wrapper
 * @param mixed $str
 * @return string
 */
function e($str) {
    if ($str === null || $str === false) {
        return '';
    }
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate or retrieve CSRF token from session
 * @return string
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generate hidden HTML input field containing CSRF token
 * @return string
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Verify CSRF token from POST request
 * @return bool
 */
function csrf_verify() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }
    }
    return true;
}

/**
 * Send HTTP Security Headers
 */
function send_security_headers() {
    if (!headers_sent()) {
        header("X-Frame-Options: SAMEORIGIN");
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header("X-XSS-Protection: 1; mode=block");
        header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://fonts.gstatic.com https://cdnjs.cloudflare.com data: blob:; img-src 'self' data: blob: https:;");
    }
}

send_security_headers();

/**
 * Hash a password using bcrypt
 * @param string $password
 * @return string
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify a password with backward compatibility for legacy MD5 hashes.
 * If verified via legacy MD5, invokes callback to upgrade stored hash to bcrypt.
 * @param string $password
 * @param string $storedHash
 * @param callable|null $rehashCallback
 * @return bool
 */
function verify_and_rehash_password($password, $storedHash, $rehashCallback = null) {
    if (empty($password) || empty($storedHash)) {
        return false;
    }

    // 1. Try modern password_verify (bcrypt/argon2)
    if (password_verify($password, $storedHash)) {
        if (password_needs_rehash($storedHash, PASSWORD_BCRYPT, ['cost' => 12])) {
            $newHash = hash_password($password);
            if (is_callable($rehashCallback)) {
                call_user_func($rehashCallback, $newHash);
            }
        }
        return true;
    }

    // 2. Legacy fallback for unsalted MD5 hashes (32 hex characters)
    if (strlen($storedHash) === 32 && ctype_xdigit($storedHash)) {
        if (hash_equals(strtolower($storedHash), md5($password))) {
            // Automatically upgrade password to bcrypt
            $newHash = hash_password($password);
            if (is_callable($rehashCallback)) {
                call_user_func($rehashCallback, $newHash);
            }
            return true;
        }
    }

    return false;
}

/**
 * Validate and securely upload an image file
 * @param array $file $_FILES['input_name']
 * @param string $targetDir Target folder (must exist or be creatable)
 * @param array $allowedExts Whitelist of extensions
 * @param int $maxBytes Max file size in bytes (default 2MB)
 * @return array ['success' => bool, 'filename' => string, 'error' => string]
 */
function validate_and_upload_image($file, $targetDir, $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'], $maxBytes = 2097152) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'filename' => '', 'error' => 'File upload failed or no file selected.'];
    }

    if ($file['size'] > $maxBytes) {
        return ['success' => false, 'filename' => '', 'error' => 'File exceeds maximum size limit of ' . round($maxBytes / 1048576, 1) . 'MB.'];
    }

    $clientFilename = $file['name'];
    $extension = strtolower(pathinfo($clientFilename, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExts, true)) {
        return ['success' => false, 'filename' => '', 'error' => 'Invalid file extension. Allowed: ' . implode(', ', $allowedExts)];
    }

    // Validate MIME type with finfo
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($mime, $allowedMimes, true)) {
            return ['success' => false, 'filename' => '', 'error' => 'Invalid file MIME type.'];
        }
    }

    // Validate image integrity with getimagesize
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        return ['success' => false, 'filename' => '', 'error' => 'File is not a valid image.'];
    }

    // Ensure target directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Generate safe randomized filename
    $safeFilename = bin2hex(random_bytes(16)) . '.' . $extension;
    $targetPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $safeFilename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'filename' => $safeFilename, 'error' => ''];
    } else {
        return ['success' => false, 'filename' => '', 'error' => 'Failed to move uploaded file.'];
    }
}
?>
