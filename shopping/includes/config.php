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
 * Bulletproof helper to safely resolve product image URL with intelligent fallbacks
 */
function get_product_image_url($row, $imgNum = 1) {
    if (empty($row)) {
        return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect width='400' height='300' fill='%23121e36'/><text x='50%25' y='50%25' fill='%2394a3b8' font-family='sans-serif' font-size='14' text-anchor='middle' dy='.3em'>ZeyTech Hardware</text></svg>";
    }
    
    $pid = intval($row['id'] ?? 0);
    $imgField = 'productImage' . $imgNum;
    $imgName = trim($row[$imgField] ?? ($row['productImage1'] ?? ''));
    
    // 1. If absolute URL (e.g. CDN or placeholder)
    if (strpos($imgName, 'http://') === 0 || strpos($imgName, 'https://') === 0 || strpos($imgName, 'data:') === 0) {
        return $imgName;
    }
    
    $baseDir = __DIR__ . '/../admin/productimages/' . $pid . '/';
    
    // 2. Direct folder check for exact filename in DB
    if (!empty($imgName) && file_exists($baseDir . $imgName)) {
        return "admin/productimages/" . $pid . "/" . rawurlencode($imgName);
    }
    
    // 3. Check for standard photo names (img_main.jpg, img_angle.jpg, img_detail.jpg)
    $namedJpg = ($imgNum == 1) ? 'img_main.jpg' : (($imgNum == 2) ? 'img_angle.jpg' : 'img_detail.jpg');
    if (file_exists($baseDir . $namedJpg)) {
        return "admin/productimages/" . $pid . "/" . $namedJpg;
    }
    
    // 4. Check for generated SVG (product_X_Y.svg)
    $svgName = 'product_' . $pid . '_' . $imgNum . '.svg';
    if (file_exists($baseDir . $svgName)) {
        return "admin/productimages/" . $pid . "/" . $svgName;
    }
    
    // 5. Scan product folder for any valid image file
    if (is_dir($baseDir)) {
        $files = scandir($baseDir);
        foreach ($files as $f) {
            if ($f !== '.' && $f !== '..' && preg_match('/\.(jpg|jpeg|png|webp|svg)$/i', $f)) {
                return "admin/productimages/" . $pid . "/" . rawurlencode($f);
            }
        }
    }
    
    // 6. Direct flat file in admin/productimages/
    if (!empty($imgName) && file_exists(__DIR__ . '/../admin/productimages/' . $imgName)) {
        return "admin/productimages/" . rawurlencode($imgName);
    }
    
    // 7. Guaranteed inline SVG fallback with product brand & name
    $name = htmlspecialchars($row['productName'] ?? 'ZeyTech Product', ENT_QUOTES);
    $company = htmlspecialchars($row['productCompany'] ?? 'ZeyTech', ENT_QUOTES);
    return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='400' height='300' viewBox='0 0 400 300'><rect width='400' height='300' fill='%230b162c'/><rect x='8' y='8' width='384' height='284' rx='6' fill='%23121e36' stroke='%23c59b43' stroke-width='1.5'/><text x='50%25' y='42%25' fill='%23d9b45d' font-family='sans-serif' font-size='28' text-anchor='middle'>⚡</text><text x='50%25' y='60%25' fill='%23ffffff' font-family='sans-serif' font-weight='bold' font-size='13' text-anchor='middle'>$name</text><text x='50%25' y='74%25' fill='%2394a3b8' font-family='sans-serif' font-size='11' text-anchor='middle'>$company &bull; Casablanca Stock</text></svg>";
}
?>