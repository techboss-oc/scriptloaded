<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/helpers.php';
require __DIR__ . '/inc/auth.php';
require __DIR__ . '/inc/store.php';

$currency = scriptloaded_current_currency();
$allProducts = fetch_active_products($pdo);

$priceField = $currency === 'NGN' ? 'price_ngn' : 'price_usd';
$availableCategories = array_values(array_unique(array_filter(array_map(static function ($product) {
	return $product['category'] ?? null;
}, $allProducts))));
sort($availableCategories);

$allPrices = array_values(array_filter(array_map(static function ($product) use ($priceField) {
	return isset($product[$priceField]) ? (float)$product[$priceField] : null;
}, $allProducts), static function ($value) {
	return $value !== null;
}));
$defaultMinPrice = $allPrices ? (int) floor(min($allPrices)) : 0;
$defaultMaxPrice = $allPrices ? (int) ceil(max($allPrices)) : 0;

$requestedCategory = trim((string)($_GET['category'] ?? ''));
if ($requestedCategory !== '' && !in_array($requestedCategory, $availableCategories, true)) {
	$requestedCategory = '';
}

$priceMinInput = $_GET['price_min'] ?? null;
$priceMaxInput = $_GET['price_max'] ?? null;
$priceMin = is_numeric($priceMinInput) ? (float)$priceMinInput : $defaultMinPrice;
$priceMax = is_numeric($priceMaxInput) ? (float)$priceMaxInput : $defaultMaxPrice;
$priceMin = max($defaultMinPrice, $priceMin);
$priceMax = min($defaultMaxPrice, $priceMax);
if ($priceMin > $priceMax) {
	$priceMin = $priceMax;
}

$rawRating = $_GET['rating'] ?? '';
$allowedRatings = ['4', '3'];
$ratingFilter = in_array($rawRating, $allowedRatings, true) ? (float)$rawRating : null;

$allowedSorts = ['newest', 'price_low', 'price_high', 'rating_high'];
$sortChoice = $_GET['sort'] ?? 'newest';
if (!in_array($sortChoice, $allowedSorts, true)) {
	$sortChoice = 'newest';
}

$filters = [
	'category' => $requestedCategory,
	'price_min' => (float) $priceMin,
	'price_max' => (float) $priceMax,
	'rating' => $ratingFilter,
	'rating_choice' => in_array($rawRating, $allowedRatings, true) ? (string)$rawRating : 'any',
	'sort' => $sortChoice,
];
$priceBounds = ['min' => $defaultMinPrice, 'max' => $defaultMaxPrice];

$products = array_values(array_filter($allProducts, static function ($product) use ($filters, $priceField) {
	if ($filters['category'] !== '' && strcasecmp($product['category'] ?? '', $filters['category']) !== 0) {
		return false;
	}
	$price = isset($product[$priceField]) ? (float)$product[$priceField] : 0;
	if ($price < $filters['price_min'] || $price > $filters['price_max']) {
		return false;
	}
	if ($filters['rating'] !== null && (float)($product['rating'] ?? 0) < $filters['rating']) {
		return false;
	}
	return true;
}));

if ($sortChoice === 'price_low') {
	usort($products, static function ($a, $b) use ($priceField) {
		return (float)($a[$priceField] ?? 0) <=> (float)($b[$priceField] ?? 0);
	});
} elseif ($sortChoice === 'price_high') {
	usort($products, static function ($a, $b) use ($priceField) {
		return (float)($b[$priceField] ?? 0) <=> (float)($a[$priceField] ?? 0);
	});
} elseif ($sortChoice === 'rating_high') {
	usort($products, static function ($a, $b) {
		return (float)($b['rating'] ?? 0) <=> (float)($a['rating'] ?? 0);
	});
}

$activeFilters = 0;
if ($filters['category'] !== '') {
	$activeFilters++;
}
if ($filters['rating'] !== null) {
	$activeFilters++;
}
if ($filters['price_min'] > $priceBounds['min'] || $filters['price_max'] < $priceBounds['max']) {
	$activeFilters++;
}
if ($filters['sort'] !== 'newest') {
	$activeFilters++;
}

unset($allProducts);

include __DIR__ . '/templates/listing.php';
