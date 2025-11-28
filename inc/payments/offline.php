<?php
declare(strict_types=1);

function scriptloaded_process_offline_payment(array $order, array $context = []): array
{
    try {
        $ref = 'OFFLINE-' . strtoupper(bin2hex(random_bytes(4)));
    } catch (Throwable $e) {
        $ref = 'OFFLINE-' . strtoupper(bin2hex(pack('d', microtime(true))));
    }
    return [
        'status' => 'completed',
        'gateway_ref' => $ref,
        'message' => 'Offline demo payment completed instantly.',
    ];
}
