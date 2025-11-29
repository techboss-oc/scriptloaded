<?php
declare(strict_types=1);


function scriptloaded_process_flutterwave_payment(array $order, array $context = []): array
{
    $env = strtolower($_ENV['PAYMENT_ENV'] ?? 'live');
    $publicKey = $env === 'test' ? ($_ENV['FLUTTERWAVE_TEST_PUBLIC_KEY'] ?? '') : ($_ENV['FLUTTERWAVE_PUBLIC_KEY'] ?? '');
    $secretKey = $env === 'test' ? ($_ENV['FLUTTERWAVE_TEST_SECRET_KEY'] ?? '') : ($_ENV['FLUTTERWAVE_SECRET_KEY'] ?? '');
    if ($secretKey === '' || $publicKey === '') {
        return scriptloaded_demo_payment_response('flutterwave');
    }

    $amount = (float)$order['amount'];
    $email = $order['buyer_email'] ?? ($order['email'] ?? '');
    if ($email === '' && isset($_SESSION['user'])) {
        $email = $_SESSION['user']['email'] ?? '';
    }
    $callback_url = $context['success_url'] ?? site_url('user/purchases.php');
    $webhook_url = site_url('api/payment_webhook.php');

    $fields = [
        'tx_ref' => $order['id'] . '-' . bin2hex(random_bytes(4)),
        'amount' => $amount,
        'currency' => $order['currency'],
        'redirect_url' => $callback_url,
        'customer' => [
            'email' => $email,
        ],
        'customizations' => [
            'title' => 'Scriptloaded Marketplace',
            'description' => 'Order #' . $order['id'],
        ],
    ];

    $ch = curl_init('https://api.flutterwave.com/v3/payments');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $secretKey,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $result = json_decode($response, true);

    if ($httpcode === 200 && isset($result['status']) && $result['status'] === 'success' && isset($result['data']['link'])) {
        return [
            'status' => 'pending',
            'redirect_url' => $result['data']['link'],
            'gateway_ref' => $fields['tx_ref'],
            'message' => 'Redirecting to Flutterwave...'
        ];
    }
    return [
        'status' => 'failed',
        'message' => 'Flutterwave error: ' . ($result['message'] ?? 'Unable to initialize payment.'),
    ];
}
