<?php
// Payment gateway webhook endpoint for production use
// Place this file at /webhook/payment_gateway.php or /api/payment_webhook.php as needed
//
// This script should be protected (e.g. by IP allowlist or HMAC signature verification)
// and should update the order status in the database based on the gateway's callback.

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/helpers.php';
require_once __DIR__ . '/../inc/store.php';

// Example: Accept POST JSON payload
$payload = file_get_contents('php://input');
$data = json_decode($payload, true);

// Log raw payload for debugging (remove in production)
// file_put_contents(__DIR__ . '/../logs/webhook.log', $payload . "\n", FILE_APPEND);


// Paystack webhook signature validation
if (isset($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'])) {
    $paystackSecret = $_ENV['PAYSTACK_SECRET_KEY'] ?? '';
    $env = strtolower($_ENV['PAYMENT_ENV'] ?? 'live');
    if ($env === 'test') {
        $paystackSecret = $_ENV['PAYSTACK_TEST_SECRET_KEY'] ?? $paystackSecret;
    }
    $signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'];
    $computed = hash_hmac('sha512', $payload, $paystackSecret);
    if (!hash_equals($signature, $computed)) {
        // Invalid signature
        file_put_contents(__DIR__ . '/../logs/webhook.log', "Paystack signature failed\n", FILE_APPEND);
        http_response_code(401);
        echo 'Invalid signature';
        exit;
    }
    $event = $data['event'] ?? '';
    $ref = $data['data']['reference'] ?? '';
    $status = $data['data']['status'] ?? '';
    if ($event === 'charge.success' && $status === 'success' && $ref) {
        $pdo = $pdo ?? null;
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE gateway_ref = :ref LIMIT 1');
        $stmt->execute(['ref' => $ref]);
        $order = $stmt->fetch();
        if ($order && $order['status'] !== 'completed') {
            scriptloaded_complete_order($pdo, $order, $ref, 'completed');
            http_response_code(200);
            echo 'OK';
            exit;
        }
    }
}


// Flutterwave webhook hash validation
if (isset($_SERVER['HTTP_VERIF_HASH'])) {
    $flutterwaveSecret = $_ENV['FLUTTERWAVE_SECRET_KEY'] ?? '';
    $env = strtolower($_ENV['PAYMENT_ENV'] ?? 'live');
    if ($env === 'test') {
        $flutterwaveSecret = $_ENV['FLUTTERWAVE_TEST_SECRET_KEY'] ?? $flutterwaveSecret;
    }
    $hash = $_SERVER['HTTP_VERIF_HASH'];
    if ($hash !== $flutterwaveSecret) {
        file_put_contents(__DIR__ . '/../logs/webhook.log', "Flutterwave hash failed\n", FILE_APPEND);
        http_response_code(401);
        echo 'Invalid hash';
        exit;
    }
    $event = $data['event'] ?? '';
    $ref = $data['data']['tx_ref'] ?? '';
    $status = $data['data']['status'] ?? '';
    if ($event === 'charge.completed' && $status === 'successful' && $ref) {
        $pdo = $pdo ?? null;
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE gateway_ref = :ref LIMIT 1');
        $stmt->execute(['ref' => $ref]);
        $order = $stmt->fetch();
        if ($order && $order['status'] !== 'completed') {
            scriptloaded_complete_order($pdo, $order, $ref, 'completed');
            http_response_code(200);
            echo 'OK';
            exit;
        }
    }
}

// Fallback: Not handled
http_response_code(400);
echo 'Webhook not handled.';
