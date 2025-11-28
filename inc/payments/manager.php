<?php
declare(strict_types=1);

require_once __DIR__ . '/../helpers.php';
require_once __DIR__ . '/offline.php';
require_once __DIR__ . '/stripe.php';
require_once __DIR__ . '/paystack.php';
require_once __DIR__ . '/flutterwave.php';

function scriptloaded_get_payment_gateways(): array
{
    return [
        'offline' => [
            'label' => 'Offline Demo',
            'description' => 'Instant activation for testing and manual transfers.',
            'badge' => 'Demo Ready',
            'supports_demo' => true,
        ],
        'stripe' => [
            'label' => 'Stripe Checkout',
            'description' => 'Global cards, Apple Pay, Google Pay.',
            'badge' => 'Cards',
            'supports_demo' => true,
        ],
        'paystack' => [
            'label' => 'Paystack',
            'description' => 'NG cards, transfers, USSD.',
            'badge' => 'Nigeria',
            'supports_demo' => true,
        ],
        'flutterwave' => [
            'label' => 'Flutterwave',
            'description' => 'Pan-African payments with local methods.',
            'badge' => 'Africa',
            'supports_demo' => true,
        ],
    ];
}

function scriptloaded_demo_payment_response(string $gateway): array
{
    $gateway = strtolower($gateway);
    try {
        $ref = strtoupper($gateway) . '-DEMO-' . substr(bin2hex(random_bytes(4)), 0, 8);
    } catch (Throwable $e) {
        $ref = strtoupper($gateway) . '-DEMO-' . substr(bin2hex(pack('d', microtime(true))), 0, 8);
    }
    return [
        'status' => 'completed',
        'gateway_ref' => $ref,
        'message' => strtoupper($gateway) . ' demo mode: order marked paid automatically.',
    ];
}

function scriptloaded_process_payment(string $gateway, array $order, array $context = []): array
{
    switch ($gateway) {
        case 'stripe':
            return scriptloaded_process_stripe_payment($order, $context);
        case 'paystack':
            return scriptloaded_process_paystack_payment($order, $context);
        case 'flutterwave':
            return scriptloaded_process_flutterwave_payment($order, $context);
        case 'offline':
        default:
            return scriptloaded_process_offline_payment($order, $context);
    }
}
