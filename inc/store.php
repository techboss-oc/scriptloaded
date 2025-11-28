<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function fetch_featured_products(PDO $pdo, int $limit = 4): array
{
    $stmt = $pdo->prepare('SELECT id,title,slug,short_description,preview_image AS image,author_name AS author,price_usd,price_ngn,downloads_count,rating,reviews_count,live_preview_url FROM products WHERE is_active = 1 ORDER BY downloads_count DESC, created_at DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function fetch_active_products(PDO $pdo, ?int $limit = null, ?int $offset = null): array
{
    $sql = 'SELECT id,title,slug,preview_image AS image,short_description,author_name AS author,price_usd,price_ngn,category,rating,reviews_count,live_preview_url FROM products WHERE is_active = 1 ORDER BY created_at DESC';
    if ($limit !== null) {
        $sql .= ' LIMIT :limit';
        if ($offset !== null) {
            $sql .= ' OFFSET :offset';
        }
    }
    $stmt = $pdo->prepare($sql);
    if ($limit !== null) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        if ($offset !== null) {
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
    }
    $stmt->execute();
    return $stmt->fetchAll();
}

function fetch_product_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM products WHERE slug = :slug AND is_active = 1 LIMIT 1');
    $stmt->execute(['slug' => $slug]);
    $row = $stmt->fetch();
    if (!$row) {
        return null;
    }
    $row['gallery'] = decode_json_field($row['gallery']);
    $row['tags'] = decode_json_field($row['tags']);
    $row['description_points'] = decode_json_field($row['description_points']);
    $rawFeatures = $row['features'];
    $row['features'] = decode_json_field($row['features']);
    if (!$row['features'] && is_string($rawFeatures)) {
        $trimmed = trim($rawFeatures);
        if ($trimmed !== '') {
            $fallbackLines = array_values(array_filter(array_map('trim', preg_split("/(\r?\n)+/", $trimmed))));
            if ($fallbackLines) {
                $row['features'] = $fallbackLines;
            } else {
                $fallbackComma = array_values(array_filter(array_map('trim', explode(',', $trimmed))));
                if ($fallbackComma) {
                    $row['features'] = $fallbackComma;
                } else {
                    $row['features'] = [$trimmed];
                }
            }
        }
    }
    $row['changelog'] = decode_json_field($row['changelog']);
    return $row;
}

function fetch_related_products(PDO $pdo, int $productId, ?string $category, int $limit = 4): array
{
    if ($category === null) {
        $stmt = $pdo->prepare('SELECT id,title,slug,preview_image AS image,author_name AS author,price_usd,price_ngn FROM products WHERE is_active = 1 AND id != :id ORDER BY downloads_count DESC, created_at DESC LIMIT :limit');
        $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    $stmt = $pdo->prepare('SELECT id,title,slug,preview_image AS image,author_name AS author,price_usd,price_ngn FROM products WHERE is_active = 1 AND id != :id AND category = :category ORDER BY downloads_count DESC, created_at DESC LIMIT :limit');
    $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
    $stmt->bindValue(':category', $category, PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function fetch_user_profile_row(PDO $pdo, int $userId): ?array
{
    $stmt = $pdo->prepare('SELECT u.id,u.email,u.full_name,u.is_admin,up.plan,up.avatar_url,up.location,up.website,up.bio FROM users u LEFT JOIN user_profiles up ON up.user_id = u.id WHERE u.id = :id LIMIT 1');
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function upsert_user_profile(PDO $pdo, int $userId, array $data): bool
{
    $stmt = $pdo->prepare('INSERT INTO user_profiles (user_id,plan,avatar_url,location,website,bio) VALUES (:user_id,:plan,:avatar,:location,:website,:bio) ON DUPLICATE KEY UPDATE plan = VALUES(plan), avatar_url = VALUES(avatar_url), location = VALUES(location), website = VALUES(website), bio = VALUES(bio)');
    return $stmt->execute([
        'user_id' => $userId,
        'plan' => $data['plan'] ?? 'Creator',
        'avatar' => $data['avatar_url'] ?? null,
        'location' => $data['location'] ?? null,
        'website' => $data['website'] ?? null,
        'bio' => $data['bio'] ?? null,
    ]);
}

function fetch_orders_for_user(PDO $pdo, int $userId, ?int $limit = null): array
{
    $sql = 'SELECT o.*,p.title,p.slug,p.preview_image AS image,p.category,p.price_usd,p.price_ngn,p.author_name AS seller,dt.token AS download_token FROM orders o INNER JOIN products p ON p.id = o.product_id LEFT JOIN download_tokens dt ON dt.order_id = o.id WHERE o.user_id = :user_id AND o.status = "completed" ORDER BY o.completed_at DESC';
    if ($limit !== null) {
        $sql .= ' LIMIT :limit';
    }
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    if ($limit !== null) {
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchAll();
}

function fetch_downloads_for_user(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare('SELECT dt.token,dt.expires_at,o.license_key,p.title,p.slug,p.preview_image AS image,p.category,"Regular License" AS license FROM download_tokens dt INNER JOIN orders o ON o.id = dt.order_id INNER JOIN products p ON p.id = o.product_id WHERE o.user_id = :user_id ORDER BY dt.created_at DESC');
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll();
}

function scriptloaded_calculate_product_price(array $product, string $currency): float
{
    $currency = normalize_currency($currency);
    if ($currency === 'NGN') {
        $price = (float)($product['price_ngn'] ?? 0);
        if ($price <= 0 && isset($product['price_usd'])) {
            $price = convert_usd_to_ngn((float)$product['price_usd']);
        }
    } else {
        $price = (float)($product['price_usd'] ?? 0);
        if ($price <= 0 && isset($product['price_ngn'])) {
            $rate = (float)($_ENV['CURRENCY_RATE_USD_NGN'] ?? 1500);
            $price = ((float)$product['price_ngn']) / max($rate, 1);
        }
    }
    return max($price, 0);
}

function scriptloaded_create_checkout_order(PDO $pdo, int $userId, array $product, string $currency, string $gateway): array
{
    $amount = scriptloaded_calculate_product_price($product, $currency);
    $stmt = $pdo->prepare('INSERT INTO orders (user_id,product_id,amount,currency,payment_gateway,status,created_at,updated_at) VALUES (:user_id,:product_id,:amount,:currency,:gateway,"pending",NOW(),NOW())');
    $stmt->execute([
        'user_id' => $userId,
        'product_id' => $product['id'],
        'amount' => $amount,
        'currency' => $currency,
        'gateway' => $gateway,
    ]);
    $orderId = (int)$pdo->lastInsertId();
    return scriptloaded_fetch_order($pdo, $orderId);
}

function scriptloaded_fetch_order(PDO $pdo, int $orderId): ?array
{
    $stmt = $pdo->prepare('SELECT o.*,p.title,p.slug,p.preview_image AS image,p.file_path,p.category,p.price_usd,p.price_ngn FROM orders o INNER JOIN products p ON p.id = o.product_id WHERE o.id = :id LIMIT 1');
    $stmt->execute(['id' => $orderId]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function scriptloaded_generate_license_key(): string
{
    try {
        $random = strtoupper(bin2hex(random_bytes(6)));
    } catch (Throwable $e) {
        $random = strtoupper(bin2hex(pack('d', microtime(true))));
    }
    return 'SL-' . date('Y') . '-' . substr($random, 0, 4) . '-' . substr($random, 4, 4);
}

function scriptloaded_issue_download_token(PDO $pdo, int $orderId, int $ttlSeconds = 7200): string
{
    $stmt = $pdo->prepare('SELECT token,expires_at FROM download_tokens WHERE order_id = :order_id LIMIT 1');
    $stmt->execute(['order_id' => $orderId]);
    $existing = $stmt->fetch();
    $now = time();
    if ($existing && strtotime((string)$existing['expires_at']) > ($now + 600)) {
        return $existing['token'];
    }
    try {
        $token = bin2hex(random_bytes(24));
    } catch (Throwable $e) {
        $token = bin2hex(pack('d', microtime(true)));
    }
    $expiresAt = date('Y-m-d H:i:s', $now + $ttlSeconds);
    if ($existing) {
        $stmt = $pdo->prepare('UPDATE download_tokens SET token = :token, expires_at = :expires_at WHERE order_id = :order_id');
    } else {
        $stmt = $pdo->prepare('INSERT INTO download_tokens (order_id, token, expires_at) VALUES (:order_id, :token, :expires_at)');
    }
    $stmt->execute([
        'order_id' => $orderId,
        'token' => $token,
        'expires_at' => $expiresAt,
    ]);
    return $token;
}

function scriptloaded_resolve_product_file_path(string $filePath): ?string
{
    $trimmed = trim($filePath);
    if ($trimmed === '') {
        return null;
    }
    $isAbsolute = (bool)preg_match('#^(?:[A-Za-z]:\\\\|/|\\\\)#', $trimmed);
    $baseRoot = null;
    if ($isAbsolute) {
        $candidate = $trimmed;
    } else {
        $configuredBase = trim((string)($_ENV['PRODUCT_STORAGE_PATH'] ?? $_ENV['DOWNLOAD_STORAGE_PATH'] ?? 'storage/products'));
        if ($configuredBase === '') {
            $configuredBase = 'storage/products';
        }
        if (!preg_match('#^(?:[A-Za-z]:\\\\|/|\\\\)#', $configuredBase)) {
            $configuredBase = realpath(__DIR__ . '/../' . ltrim($configuredBase, '/\\')) ?: (__DIR__ . '/../' . ltrim($configuredBase, '/\\'));
        }
        $baseRoot = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $configuredBase), DIRECTORY_SEPARATOR);
        $relative = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $trimmed), DIRECTORY_SEPARATOR);
        $candidate = $baseRoot . DIRECTORY_SEPARATOR . $relative;
    }
    $realPath = realpath($candidate);
    if ($realPath === false || !is_file($realPath)) {
        return null;
    }
    if ($baseRoot !== null) {
        $normalizedReal = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $realPath), DIRECTORY_SEPARATOR);
        $normalizedBase = rtrim($baseRoot, DIRECTORY_SEPARATOR);
        $realLower = strtolower($normalizedReal);
        $baseLower = strtolower($normalizedBase);
        $allowedPrefix = $baseLower . DIRECTORY_SEPARATOR;
        if ($realLower !== $baseLower && strpos($realLower, $allowedPrefix) !== 0) {
            return null;
        }
    }
    return $realPath;
}

function scriptloaded_complete_order(PDO $pdo, array $order, ?string $gatewayRef = null, string $status = 'completed'): array
{
    $licenseKey = $order['license_key'] ?: scriptloaded_generate_license_key();
    $stmt = $pdo->prepare('UPDATE orders SET status = :status, gateway_ref = COALESCE(:gateway_ref, gateway_ref), license_key = :license_key, completed_at = NOW(), updated_at = NOW() WHERE id = :id');
    $stmt->execute([
        'status' => $status,
        'gateway_ref' => $gatewayRef,
        'license_key' => $licenseKey,
        'id' => $order['id'],
    ]);
    $pdo->prepare('UPDATE products SET downloads_count = downloads_count + 1 WHERE id = :product_id')->execute(['product_id' => $order['product_id']]);
    $downloadToken = scriptloaded_issue_download_token($pdo, (int)$order['id']);
    $updated = scriptloaded_fetch_order($pdo, (int)$order['id']);
    $updated['license_key'] = $licenseKey;
    $updated['download_token'] = $downloadToken;
    return $updated;
}

function scriptloaded_fail_order(PDO $pdo, int $orderId, ?string $message = null): void
{
    $stmt = $pdo->prepare('UPDATE orders SET status = "failed", updated_at = NOW(), gateway_ref = COALESCE(:message, gateway_ref) WHERE id = :id');
    $stmt->execute([
        'id' => $orderId,
        'message' => $message,
    ]);
}

function fetch_favorites_for_user(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare('SELECT p.*,p.preview_image AS image,p.author_name AS author FROM favorites f INNER JOIN products p ON p.id = f.product_id WHERE f.user_id = :user_id ORDER BY f.created_at DESC');
    $stmt->execute(['user_id' => $userId]);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$row) {
        $row['tags'] = decode_json_field($row['tags']);
    }
    return $rows;
}

function fetch_billing_methods(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare('SELECT * FROM billing_methods WHERE user_id = :user_id ORDER BY is_primary DESC, created_at ASC');
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll();
}

function insert_billing_method(PDO $pdo, int $userId, array $card): bool
{
    if (!empty($card['is_primary'])) {
        $pdo->prepare('UPDATE billing_methods SET is_primary = 0 WHERE user_id = :user_id')->execute(['user_id' => $userId]);
    }
    $stmt = $pdo->prepare('INSERT INTO billing_methods (user_id,brand,last4,exp_month,exp_year,cardholder,is_primary) VALUES (:user_id,:brand,:last4,:exp_month,:exp_year,:cardholder,:is_primary)');
    return $stmt->execute([
        'user_id' => $userId,
        'brand' => $card['brand'],
        'last4' => $card['last4'],
        'exp_month' => $card['exp_month'],
        'exp_year' => $card['exp_year'],
        'cardholder' => $card['cardholder'] ?? null,
        'is_primary' => $card['is_primary'] ?? 0,
    ]);
}

function fetch_invoices(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare('SELECT i.*,p.title FROM invoices i INNER JOIN orders o ON o.id = i.order_id INNER JOIN products p ON p.id = o.product_id WHERE o.user_id = :user_id ORDER BY i.issued_at DESC');
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll();
}

function fetch_support_tickets(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare('SELECT * FROM support_tickets WHERE user_id = :user_id ORDER BY created_at DESC');
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll();
}

function insert_support_ticket(PDO $pdo, int $userId, string $subject, string $message, string $priority = 'medium'): bool
{
    $stmt = $pdo->prepare('INSERT INTO support_tickets (user_id,subject,message,priority) VALUES (:user_id,:subject,:message,:priority)');
    return $stmt->execute([
        'user_id' => $userId,
        'subject' => $subject,
        'message' => $message,
        'priority' => $priority,
    ]);
}

function fetch_all_support_tickets_with_users(PDO $pdo): array
{
    $stmt = $pdo->prepare('SELECT t.*, u.full_name, u.email FROM support_tickets t INNER JOIN users u ON u.id = t.user_id ORDER BY t.created_at DESC');
    $stmt->execute();
    return $stmt->fetchAll();
}

function update_support_ticket(PDO $pdo, int $ticketId, array $fields): bool
{
    $allowed = ['status', 'priority'];
    $setParts = [];
    $params = ['id' => $ticketId];
    foreach ($allowed as $column) {
        if (array_key_exists($column, $fields)) {
            $setParts[] = $column . ' = :' . $column;
            $params[$column] = $fields[$column];
        }
    }
    if (!$setParts) {
        return false;
    }
    $sql = 'UPDATE support_tickets SET ' . implode(', ', $setParts) . ' WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

function fetch_notification_preferences(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare('SELECT pref_key,is_enabled FROM user_notifications WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $userId]);
    $existing = [];
    foreach ($stmt->fetchAll() as $row) {
        $existing[$row['pref_key']] = (bool)$row['is_enabled'];
    }
    return $existing;
}

function save_notification_preferences(PDO $pdo, int $userId, array $enabledKeys): void
{
    $pdo->prepare('DELETE FROM user_notifications WHERE user_id = :user_id')->execute(['user_id' => $userId]);
    $stmt = $pdo->prepare('INSERT INTO user_notifications (user_id,pref_key,is_enabled) VALUES (:user_id,:pref_key,:is_enabled)');
    foreach ($enabledKeys as $key => $enabled) {
        $stmt->execute([
            'user_id' => $userId,
            'pref_key' => $key,
            'is_enabled' => $enabled ? 1 : 0,
        ]);
    }
}

function decode_json_field(?string $value): array
{
    if (!$value) {
        return [];
    }
    $decoded = json_decode($value, true);
    return is_array($decoded) ? $decoded : [];
}