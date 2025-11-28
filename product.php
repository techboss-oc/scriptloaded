<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/helpers.php';
require __DIR__ . '/inc/auth.php';
require __DIR__ . '/inc/store.php';

$currency = scriptloaded_current_currency();
$slugParam = trim((string)($_GET['slug'] ?? ''));
$slug = $slugParam !== '' ? $slugParam : 'ecommerce-website-script';

$product = fetch_product_by_slug($pdo, $slug);

if (!$product) {
    $fallback = fetch_featured_products($pdo, 1);
    $fallbackSlug = $fallback[0]['slug'] ?? null;
    if ($fallbackSlug) {
        if (strcasecmp($slug, $fallbackSlug) !== 0) {
            header('Location: product.php?slug=' . urlencode($fallbackSlug));
            exit;
        }
        $product = fetch_product_by_slug($pdo, $fallbackSlug);
        $slug = $fallbackSlug;
    }
}

if (!$product) {
    http_response_code(404);
    include __DIR__ . '/templates/product_missing.php';
    exit;
}
$relatedProducts = fetch_related_products($pdo, (int)$product['id'], $product['category'] ?? null);

include __DIR__ . '/templates/product.php';
