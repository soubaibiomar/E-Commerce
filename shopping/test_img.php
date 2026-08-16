<?php
require_once __DIR__ . '/includes/config.php';

$products = db_fetch_all("SELECT id, productName, productImage1 FROM products LIMIT 10");
foreach ($products as $p) {
    echo $p['id'] . ' [' . $p['productName'] . '] => ' . get_product_image_url($p, 1) . PHP_EOL;
}
