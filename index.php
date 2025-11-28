<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/helpers.php';
require __DIR__ . '/inc/auth.php';
require __DIR__ . '/inc/store.php';

$currency = scriptloaded_current_currency();
$featuredProducts = fetch_featured_products($pdo, 4);
$latestProducts = fetch_active_products($pdo, 6);
$featuredCategories = get_product_categories();

include __DIR__ . '/templates/home.php';
