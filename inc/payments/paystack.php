<?php
declare(strict_types=1);

function scriptloaded_process_paystack_payment(array $order, array $context = []): array
{
    $secretKey = $_ENV['PAYSTACK_SECRET_KEY'] ?? '';
    $publicKey = $_ENV['PAYSTACK_PUBLIC_KEY'] ?? '';
    if ($secretKey === '' || $publicKey === '') {
        return scriptloaded_demo_payment_response('paystack');
    }
    return [
        'status' => 'pending',
        'redirect_url' => 'https://dashboard.paystack.com/#/test/payments',
        'message' => 'Paystack integration placeholder: send initialize transaction request and redirect customer.',
    ];
}
