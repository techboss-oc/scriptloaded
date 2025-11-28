<?php
require __DIR__ . '/inc/config.php';
require __DIR__ . '/inc/helpers.php';
require __DIR__ . '/inc/auth.php';
require __DIR__ . '/inc/csrf.php';
require __DIR__ . '/inc/store.php';
require __DIR__ . '/inc/payments/manager.php';

$currentUser = require_user();
$slug = trim((string)($_GET['slug'] ?? $_POST['slug'] ?? ''));
if ($slug === '') {
    header('Location: ' . site_url('listing.php'));
    exit;
}
$product = fetch_product_by_slug($pdo, $slug);
if (!$product) {
    header('Location: ' . site_url('listing.php'));
    exit;
}

$currency = scriptloaded_current_currency();
$gateways = scriptloaded_get_payment_gateways();
$selectedGateway = $_POST['gateway'] ?? 'offline';
if (!isset($gateways[$selectedGateway])) {
    $selectedGateway = 'offline';
}
$csrfToken = generate_csrf();
$errors = [];
$success = null;
$downloadLink = null;
$licenseKey = null;
$orderReference = null;
$orderAmount = scriptloaded_calculate_product_price($product, $currency);
$billingMethods = fetch_billing_methods($pdo, (int)$currentUser['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validate_csrf($token)) {
        $errors[] = 'Invalid session token. Please refresh and try again.';
    }
    $postedCurrency = normalize_currency($_POST['currency'] ?? $currency);
    $currency = $postedCurrency;
    $selectedGateway = $_POST['gateway'] ?? 'offline';
    if (!isset($gateways[$selectedGateway])) {
        $errors[] = 'Please select a supported payment method.';
    }
    if (!$errors) {
        $order = scriptloaded_create_checkout_order($pdo, (int)$currentUser['id'], $product, $currency, $selectedGateway);
        $context = [
            'success_url' => site_url('user/purchases.php'),
            'cancel_url' => site_url('checkout.php?slug=' . urlencode($product['slug'])),
        ];
        $result = scriptloaded_process_payment($selectedGateway, $order, $context);
        $status = strtolower((string)($result['status'] ?? 'completed'));
        if ($status === 'completed') {
            $completed = scriptloaded_complete_order($pdo, $order, $result['gateway_ref'] ?? null);
            $success = $result['message'] ?? 'Payment successful. Your download link is ready.';
            $downloadLink = site_url('user/download.php?token=' . urlencode($completed['download_token']));
            $licenseKey = $completed['license_key'] ?? null;
            $orderReference = $completed['gateway_ref'] ?? $result['gateway_ref'] ?? null;
        } elseif ($status === 'pending' && !empty($result['redirect_url'])) {
            header('Location: ' . $result['redirect_url']);
            exit;
        } else {
            $errors[] = $result['message'] ?? 'Unable to process payment. Please try again.';
            scriptloaded_fail_order($pdo, (int)$order['id'], $result['gateway_ref'] ?? null);
        }
    }
    $orderAmount = scriptloaded_calculate_product_price($product, $currency);
}

include __DIR__ . '/templates/checkout.php';
