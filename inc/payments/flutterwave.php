<?php
declare(strict_types=1);

function scriptloaded_process_flutterwave_payment(array $order, array $context = []): array
{
    $secretKey = $_ENV['FLUTTERWAVE_SECRET_KEY'] ?? '';
    if ($secretKey === '') {
        return scriptloaded_demo_payment_response('flutterwave');
    }
    return [
        'status' => 'pending',
        'redirect_url' => 'https://dashboard.flutterwave.com/dashboard/payments',
        'message' => 'Flutterwave integration placeholder: implement hosted payment link generation.',
    ];
}
