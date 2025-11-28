<?php
declare(strict_types=1);

function scriptloaded_process_stripe_payment(array $order, array $context = []): array
{
    $secretKey = $_ENV['STRIPE_SECRET_KEY'] ?? '';
    $publishableKey = $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '';
    if ($secretKey === '' || $publishableKey === '') {
        return scriptloaded_demo_payment_response('stripe');
    }
    return [
        'status' => 'pending',
        'redirect_url' => $context['success_url'] ?? site_url('user/purchases.php'),
        'message' => 'Stripe integration placeholder: configure Checkout Session creation in inc/payments/stripe.php.',
    ];
}
