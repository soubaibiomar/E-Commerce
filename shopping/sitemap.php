<?php
/**
 * Dynamic XML Sitemap Generator for SEO & GEO crawlers
 * Outputs standard sitemap protocol 0.9
 */
require_once __DIR__ . '/includes/config.php';

header("Content-Type: application/xml; charset=utf-8");

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8085';
$baseUrl = $protocol . $host;

$now = date('c');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// 1. Homepage
echo '  <url>' . "\n";
echo '    <loc>' . htmlspecialchars($baseUrl . '/index.php', ENT_XML1) . '</loc>' . "\n";
echo '    <lastmod>' . $now . '</lastmod>' . "\n";
echo '    <changefreq>daily</changefreq>' . "\n";
echo '    <priority>1.0</priority>' . "\n";
echo '  </url>' . "\n";

// 2. Categories
$categories = db_fetch_all("SELECT id, updationDate, creationDate FROM category");
foreach ($categories as $cat) {
    $date = !empty($cat['updationDate']) ? date('c', strtotime($cat['updationDate'])) : (!empty($cat['creationDate']) ? date('c', strtotime($cat['creationDate'])) : $now);
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($baseUrl . '/category.php?cid=' . $cat['id'], ENT_XML1) . '</loc>' . "\n";
    echo '    <lastmod>' . $date . '</lastmod>' . "\n";
    echo '    <changefreq>daily</changefreq>' . "\n";
    echo '    <priority>0.8</priority>' . "\n";
    echo '  </url>' . "\n";
}

// 3. Subcategories
$subcategories = db_fetch_all("SELECT id, updationDate, creationDate FROM subcategory");
foreach ($subcategories as $sc) {
    $date = !empty($sc['updationDate']) ? date('c', strtotime($sc['updationDate'])) : (!empty($sc['creationDate']) ? date('c', strtotime($sc['creationDate'])) : $now);
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($baseUrl . '/sub-category.php?scid=' . $sc['id'], ENT_XML1) . '</loc>' . "\n";
    echo '    <lastmod>' . $date . '</lastmod>' . "\n";
    echo '    <changefreq>weekly</changefreq>' . "\n";
    echo '    <priority>0.7</priority>' . "\n";
    echo '  </url>' . "\n";
}

// 4. Products
$products = db_fetch_all("SELECT id, updationDate, postingDate FROM products WHERE productAvailability='In Stock'");
foreach ($products as $p) {
    $date = !empty($p['updationDate']) ? date('c', strtotime($p['updationDate'])) : (!empty($p['postingDate']) ? date('c', strtotime($p['postingDate'])) : $now);
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($baseUrl . '/product-details.php?pid=' . $p['id'], ENT_XML1) . '</loc>' . "\n";
    echo '    <lastmod>' . $date . '</lastmod>' . "\n";
    echo '    <changefreq>daily</changefreq>' . "\n";
    echo '    <priority>0.9</priority>' . "\n";
    echo '  </url>' . "\n";
}

echo '</urlset>' . "\n";
?>
