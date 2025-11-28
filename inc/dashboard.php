<?php
declare(strict_types=1);

require_once __DIR__ . '/store.php';

function get_dashboard_nav_links(): array
{
    return [
        ['slug' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'dashboard', 'href' => 'dashboard.php'],
        ['slug' => 'purchases', 'label' => 'Purchased Items', 'icon' => 'shopping_cart', 'href' => 'purchases.php'],
        ['slug' => 'downloads', 'label' => 'Downloads', 'icon' => 'download', 'href' => 'downloads.php'],
        ['slug' => 'favorites', 'label' => 'Favorites', 'icon' => 'favorite', 'href' => 'favorites.php'],
        ['slug' => 'profile', 'label' => 'Profile', 'icon' => 'person', 'href' => 'profile.php'],
        ['slug' => 'support', 'label' => 'Support', 'icon' => 'support_agent', 'href' => 'support.php'],
    ];
}

function build_user_profile(PDO $pdo, int $userId): array
{
    $row = fetch_user_profile_row($pdo, $userId);
    if (!$row) {
        throw new RuntimeException('Unable to load user profile');
    }
    $name = $row['full_name'] ?: $row['email'];
    $avatar = $row['avatar_url'] ?: default_avatar_for_email($row['email']);
    return [
        'id' => (int)$row['id'],
        'name' => $name,
        'email' => $row['email'],
        'plan' => $row['plan'] ?: 'Creator',
        'avatar' => $avatar,
        'location' => $row['location'] ?? '',
        'website' => $row['website'] ?? '',
        'bio' => $row['bio'] ?? '',
    ];
}

function default_avatar_for_email(string $email): string
{
    return 'https://i.pravatar.cc/150?u=' . urlencode($email);
}

function get_notification_catalog(): array
{
    return [
        'product_updates' => ['label' => 'Product updates & patches', 'description' => 'Alerts when your purchases get a new release.'],
        'promotions' => ['label' => 'Marketplace promotions', 'description' => 'Occasional curated drops and launch deals.'],
        'security' => ['label' => 'Security alerts', 'description' => 'Login attempts, password changes, token events.'],
    ];
}

function get_notification_preferences_with_defaults(PDO $pdo, int $userId): array
{
    $catalog = get_notification_catalog();
    $stored = fetch_notification_preferences($pdo, $userId);
    $result = [];
    foreach ($catalog as $key => $meta) {
        $result[] = [
            'key' => $key,
            'label' => $meta['label'],
            'description' => $meta['description'],
            'enabled' => $stored[$key] ?? true,
        ];
    }
    return $result;
}

function compile_dashboard_overview(PDO $pdo, int $userId): array
{
    $orders = fetch_orders_for_user($pdo, $userId, null);
    $downloads = fetch_downloads_for_user($pdo, $userId);
    $tickets = fetch_support_tickets($pdo, $userId);

    $lifetimeUsd = 0.0;
    foreach ($orders as $order) {
        $amount = (float)$order['amount'];
        if ($order['currency'] === 'NGN') {
            $amount = $amount / max(1, (float)($_ENV['CURRENCY_RATE_USD_NGN'] ?? 1500));
        }
        $lifetimeUsd += $amount;
    }
    $usdToNgnRate = (float)get_setting('currency_rate_usd_to_ngn', $_ENV['CURRENCY_RATE_USD_NGN'] ?? 1500);
    $usdToNgnRate = $usdToNgnRate > 0 ? $usdToNgnRate : 1;
    $lifetimeNgn = $lifetimeUsd * $usdToNgnRate;

    $activeDownloads = array_filter($downloads, static function (array $download): bool {
        return strtotime($download['expires_at']) > time();
    });
    $openTickets = array_filter($tickets, static function (array $ticket): bool {
        return in_array($ticket['status'], ['open', 'in_progress'], true);
    });

    $statCards = [
        [
            'label' => 'Lifetime Spend',
            'value' => format_currency($lifetimeUsd, 'USD'),
            'value_ngn' => format_currency($lifetimeNgn, 'NGN'),
            'subtext' => count($orders) . ' completed orders',
        ],
        ['label' => 'Total Purchases', 'value' => (string)count($orders), 'subtext' => 'Across ' . max(1, count(array_unique(array_column($orders, 'category')))) . ' categories'],
        ['label' => 'Active Downloads', 'value' => (string)count($activeDownloads), 'subtext' => 'Tokens refresh every 2h'],
        ['label' => 'Support Tickets', 'value' => count($openTickets) . ' open', 'subtext' => 'Avg response < 1h'],
    ];

    return [
        'stats' => $statCards,
        'recent_purchases' => array_slice($orders, 0, 3),
        'active_downloads' => array_slice($downloads, 0, 2),
        'open_tickets' => $openTickets,
    ];
}