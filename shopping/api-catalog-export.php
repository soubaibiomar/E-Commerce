<?php
// =============================================================================
// ZeyTech AI Commerce OS — Phase 12: Catalog Export Engine
// Endpoint: GET /api-catalog-export.php
// Exports complete product catalog with categories, stock, and multi-currency pricing
// =============================================================================

error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once(__DIR__ . '/includes/config.php');

$format = strtolower($_GET['format'] ?? 'json');

$rows = db_fetch_all(
    "SELECT p.id, p.productName, p.productCompany, 
            p.productPrice AS priceMAD,
            ROUND(p.productPrice / 10.20, 2) AS priceUSD, 
            ROUND(p.productPrice / 11.10, 2) AS priceEUR,
            p.productPriceBeforeDiscount, p.productDescription, p.description_fr,
            p.productImage1, p.productImage2, p.productImage3,
            p.productModel AS sku, p.productAvailability, p.specifications,
            COALESCE(c.categoryName, 'Electronics') AS categoryName,
            COALESCE(s.subcategory, 'General') AS subcategoryName,
            COALESCE(i.available_qty, 0) AS stockAvailable,
            COALESCE(i.reserved_qty, 0) AS stockReserved,
            COALESCE(i.sold_qty, 0) AS stockSold
     FROM products p
     LEFT JOIN category c ON p.category = c.id
     LEFT JOIN subcategory s ON p.subCategory = s.id
     LEFT JOIN inventory i ON p.id = i.product_id
     ORDER BY p.id ASC"
);

if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="zeytech_catalog_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Product Name', 'Company', 'Category', 'SKU', 'Price MAD', 'Price USD', 'Price EUR', 'Stock Available', 'Stock Reserved', 'Stock Sold', 'Availability']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['id'],
            $r['productName'],
            $r['productCompany'],
            $r['categoryName'],
            $r['sku'],
            $r['priceMAD'],
            $r['priceUSD'],
            $r['priceEUR'],
            $r['stockAvailable'],
            $r['stockReserved'],
            $r['stockSold'],
            $r['productAvailability']
        ]);
    }
    fclose($out);
    exit;
}

echo json_encode([
    'success' => true,
    'totalProducts' => count($rows),
    'currencyRates' => [
        'MAD' => 1.00,
        'USD' => 0.098,
        'EUR' => 0.090
    ],
    'catalog' => $rows
]);
