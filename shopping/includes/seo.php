<?php
/**
 * Universal SEO & GEO (Generative Engine Optimization) Module
 * Handles Schema.org JSON-LD, OpenGraph, Twitter Cards, Canonicals & LLM extractability.
 */

if (!function_exists('get_base_url')) {
    function get_base_url() {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8085';
        return $protocol . $host;
    }
}

/**
 * Output Standard Meta Tags + OpenGraph + Twitter Cards + Canonical Link
 */
function render_seo_meta($title, $description, $canonicalPath = '', $ogImage = '', $ogType = 'website') {
    $baseUrl = get_base_url();
    $fullCanonical = !empty($canonicalPath) ? $baseUrl . '/' . ltrim($canonicalPath, '/') : $baseUrl . $_SERVER['REQUEST_URI'];
    
    // Clean URL query params if needed
    $parsed = parse_url($fullCanonical);
    $cleanCanonical = $parsed['scheme'] . '://' . $parsed['host'] . (isset($parsed['port']) ? ':' . $parsed['port'] : '') . $parsed['path'];
    if (isset($parsed['query'])) {
        parse_str($parsed['query'], $qParams);
        // keep only relevant canonical parameters
        $allowed = ['pid', 'cid', 'scid'];
        $cleanParams = array_intersect_key($qParams, array_flip($allowed));
        if (!empty($cleanParams)) {
            $cleanCanonical .= '?' . http_build_query($cleanParams);
        }
    }

    $fullImage = !empty($ogImage) ? (strpos($ogImage, 'http') === 0 ? $ogImage : $baseUrl . '/' . ltrim($ogImage, '/')) : $baseUrl . '/assets/images/banners/hero-banner.jpg';
    
    $cleanDesc = htmlspecialchars(trim(strip_tags($description)), ENT_QUOTES, 'UTF-8');
    $cleanTitle = htmlspecialchars(trim(strip_tags($title)), ENT_QUOTES, 'UTF-8');

    echo "\n    <!-- Primary SEO Meta Tags -->\n";
    echo '    <title>' . $cleanTitle . '</title>' . "\n";
    echo '    <meta name="description" content="' . $cleanDesc . '">' . "\n";
    echo '    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">' . "\n";
    echo '    <link rel="canonical" href="' . htmlspecialchars($cleanCanonical, ENT_QUOTES, 'UTF-8') . '">' . "\n";

    echo "\n    <!-- Open Graph / Facebook / LinkedIn / AI Search -->\n";
    echo '    <meta property="og:type" content="' . htmlspecialchars($ogType, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    echo '    <meta property="og:url" content="' . htmlspecialchars($cleanCanonical, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    echo '    <meta property="og:title" content="' . $cleanTitle . '">' . "\n";
    echo '    <meta property="og:description" content="' . $cleanDesc . '">' . "\n";
    echo '    <meta property="og:image" content="' . htmlspecialchars($fullImage, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    echo '    <meta property="og:site_name" content="Storefront Global">' . "\n";

    echo "\n    <!-- Twitter Card Meta Tags -->\n";
    echo '    <meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '    <meta name="twitter:url" content="' . htmlspecialchars($cleanCanonical, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    echo '    <meta name="twitter:title" content="' . $cleanTitle . '">' . "\n";
    echo '    <meta name="twitter:description" content="' . $cleanDesc . '">' . "\n";
    echo '    <meta name="twitter:image" content="' . htmlspecialchars($fullImage, ENT_QUOTES, 'UTF-8') . '">' . "\n";
}

/**
 * Output Organization & WebSite JSON-LD Schema
 */
function render_organization_schema() {
    $baseUrl = get_base_url();
    $schema = [
        "@context" => "https://schema.org",
        "@graph" => [
            [
                "@type" => "Organization",
                "@id" => $baseUrl . "/#organization",
                "name" => "Storefront Global Shopping",
                "url" => $baseUrl,
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => $baseUrl . "/assets/images/logo.png"
                ],
                "sameAs" => [
                    "https://twitter.com/storefront",
                    "https://facebook.com/storefront"
                ]
            ],
            [
                "@type" => "WebSite",
                "@id" => $baseUrl . "/#website",
                "url" => $baseUrl,
                "name" => "Storefront Global",
                "publisher" => [
                    "@id" => $baseUrl . "/#organization"
                ],
                "potentialAction" => [
                    "@type" => "SearchAction",
                    "target" => [
                        "@type" => "EntryPoint",
                        "urlTemplate" => $baseUrl . "/search-result.php?product={search_term_string}"
                    ],
                    "query-input" => "required name=search_term_string"
                ]
            ]
        ]
    ];

    echo "\n    <!-- Structured Data: Organization & WebSite Schema.org -->\n";
    echo '    <script type="application/ld+json">' . "\n";
    echo json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    echo '    </script>' . "\n";
}

/**
 * Output Rich Product Schema.org JSON-LD (Product, Offer, AggregateRating, AdditionalProperty)
 */
function render_product_schema($product, $reviewCount = 5, $ratingValue = 4.9) {
    if (empty($product)) return;
    
    $baseUrl = get_base_url();
    $curr = get_current_currency();
    $numericPrice = floatval($product['productPrice']) * $curr['rate'];
    $formattedPrice = number_format($numericPrice, $curr['decimals'], '.', '');
    
    $productUrl = $baseUrl . '/product-details.php?pid=' . $product['id'];
    $imageUrl = $baseUrl . '/admin/productimages/' . $product['id'] . '/' . $product['productImage1'];

    $additionalProps = [];
    if (!empty($product['specifications'])) {
        $specs = json_decode($product['specifications'], true);
        if (is_array($specs)) {
            foreach ($specs as $name => $val) {
                $additionalProps[] = [
                    "@type" => "PropertyValue",
                    "name" => $name,
                    "value" => $val
                ];
            }
        }
    }

    $schema = [
        "@context" => "https://schema.org/",
        "@type" => "Product",
        "name" => $product['productName'],
        "image" => [
            $imageUrl
        ],
        "description" => strip_tags($product['productDescription']),
        "sku" => "PROD-" . str_pad($product['id'], 5, "0", STR_PAD_LEFT),
        "mpn" => "MPN-" . $product['id'],
        "brand" => [
            "@type" => "Brand",
            "name" => $product['productCompany'] ?? 'Storefront'
        ],
        "offers" => [
            "@type" => "Offer",
            "url" => $productUrl,
            "priceCurrency" => $curr['code'],
            "price" => $formattedPrice,
            "priceValidUntil" => date('Y-m-d', strtotime('+1 year')),
            "itemCondition" => "https://schema.org/NewCondition",
            "availability" => ($product['productAvailability'] === 'In Stock') ? "https://schema.org/InStock" : "https://schema.org/OutOfStock",
            "seller" => [
                "@type" => "Organization",
                "name" => "Storefront Global"
            ]
        ],
        "aggregateRating" => [
            "@type" => "AggregateRating",
            "ratingValue" => number_format($ratingValue, 1, '.', ''),
            "bestRating" => "5",
            "ratingCount" => strval(max(1, $reviewCount))
        ]
    ];

    if (!empty($additionalProps)) {
        $schema["additionalProperty"] = $additionalProps;
    }

    echo "\n    <!-- Structured Data: Rich Product Schema.org (GEO & Rich Results) -->\n";
    echo '    <script type="application/ld+json">' . "\n";
    echo json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    echo '    </script>' . "\n";
}

/**
 * Output BreadcrumbList Schema.org JSON-LD
 */
function render_breadcrumb_schema($items) {
    $baseUrl = get_base_url();
    $itemList = [];
    $pos = 1;
    
    foreach ($items as $name => $path) {
        $itemList[] = [
            "@type" => "ListItem",
            "position" => $pos,
            "name" => $name,
            "item" => strpos($path, 'http') === 0 ? $path : $baseUrl . '/' . ltrim($path, '/')
        ];
        $pos++;
    }

    $schema = [
        "@context" => "https://schema.org",
        "@type" => "BreadcrumbList",
        "itemListElement" => $itemList
    ];

    echo "\n    <!-- Structured Data: BreadcrumbList Schema.org -->\n";
    echo '    <script type="application/ld+json">' . "\n";
    echo json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    echo '    </script>' . "\n";
}
?>
