<?php
if (!defined('DB_SERVER')) {
    define('DB_SERVER', getenv('DB_HOST') ?: (getenv('DB_SERVER') ?: 'localhost'));
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ''));
    define('DB_NAME', getenv('DB_NAME') ?: (getenv('DB_DATABASE') ?: 'shopping'));
    define('DB_PORT', getenv('DB_PORT') ? intval(getenv('DB_PORT')) : 3306);
}

// Set standard timezone
date_default_timezone_set('Asia/Kolkata');

require_once __DIR__ . '/currency.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/seo.php';

/**
 * Helper to safely resolve product image URL
 */
function get_product_image_url($row, $imgNum = 1) {
    if (empty($row)) return 'assets/images/blank.gif';
    $imgField = 'productImage' . $imgNum;
    $imgName = $row[$imgField] ?? ($row['productImage1'] ?? '');
    
    if (empty($imgName)) {
        return 'assets/images/blank.gif';
    }
    
    if (strpos($imgName, 'http://') === 0 || strpos($imgName, 'https://') === 0) {
        return $imgName;
    }
    
    $pid = intval($row['id'] ?? 0);
    return "admin/productimages/" . $pid . "/" . $imgName;
}
?>