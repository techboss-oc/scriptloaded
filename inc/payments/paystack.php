<?php
declare(strict_types=1);


function scriptloaded_process_paystack_payment(array $order, array $context = []): array
{
    // Use test or live keys based on PAYMENT_ENV
    $env = strtolower($_ENV['PAYMENT_ENV'] ?? 'live');
    $publicKey = $env === 'test' ? ($_ENV['PAYSTACK_TEST_PUBLIC_KEY'] ?? '') : ($_ENV['PAYSTACK_PUBLIC_KEY'] ?? '');
    $secretKey = $env === 'test' ? ($_ENV['PAYSTACK_TEST_SECRET_KEY'] ?? '') : ($_ENV['PAYSTACK_SECRET_KEY'] ?? '');
    if ($secretKey === '' || $publicKey === '') {
        return scriptloaded_demo_payment_response('paystack');
    }

    $amount = (int)($order['amount'] * 100); // Paystack expects kobo
    $email = $order['buyer_email'] ?? ($order['email'] ?? '');
    if ($email === '' && isset($_SESSION['user'])) {
        $email = $_SESSION['user']['email'] ?? '';
    }
    $callback_url = $context['success_url'] ?? site_url('user/purchases.php');
    $webhook_url = site_url('api/payment_webhook.php');

    $fields = [
        'email' => $email,
        'amount' => $amount,
        'currency' => $order['currency'],
        'reference' => $order['id'] . '-' . bin2hex(random_bytes(4)),
        'callback_url' => $callback_url,
        // Optionally: 'metadata' => ['order_id' => $order['id'], ...]
    ];

    $ch = curl_init('https://api.paystack.co/transaction/initialize');
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

    if ($httpcode === 200 && isset($result['status']) && $result['status'] && isset($result['data']['authorization_url'])) {
        return [
            'status' => 'pending',
            'redirect_url' => $result['data']['authorization_url'],
            'gateway_ref' => $fields['reference'],
            'message' => 'Redirecting to Paystack...'
        ];
    }
    return [
        'status' => 'failed',
        'message' => 'Paystack error: ' . ($result['message'] ?? 'Unable to initialize payment.'),
    ];
}
