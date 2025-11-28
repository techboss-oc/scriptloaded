<?php
declare(strict_types=1);

require __DIR__ . '/_bootstrap.php';

$token = trim((string)($_GET['token'] ?? ''));
if ($token === '') {
    respond_with_error('Missing download token.', 400);
}

$stmt = $pdo->prepare('SELECT dt.token,dt.expires_at,o.id AS order_id,o.user_id,o.status,p.title,p.file_path FROM download_tokens dt INNER JOIN orders o ON o.id = dt.order_id INNER JOIN products p ON p.id = o.product_id WHERE dt.token = :token LIMIT 1');
$stmt->execute(['token' => $token]);
$download = $stmt->fetch();

if (!$download || (int)$download['user_id'] !== (int)$currentUser['id']) {
    respond_with_error('Download not found for your account.', 404);
}

if ($download['status'] !== 'completed') {
    respond_with_error('Order is still pending. Try again once payment clears.', 403);
}

$expiresAt = strtotime((string)$download['expires_at']);
if ($expiresAt !== false && $expiresAt < time()) {
    $newToken = scriptloaded_issue_download_token($pdo, (int)$download['order_id']);
    header('Location: ' . site_url('user/download.php?token=' . urlencode($newToken) . '&refreshed=1'));
    exit;
}

$filePath = (string)($download['file_path'] ?? '');
if ($filePath === '') {
    respond_with_error('This product does not have a downloadable asset configured yet.', 404);
}

$resolvedPath = scriptloaded_resolve_product_file_path($filePath);
if ($resolvedPath === null || !is_readable($resolvedPath)) {
    respond_with_error('File is unavailable. Please contact support.', 404);
}

$filename = basename($resolvedPath);
$mime = mime_content_type($resolvedPath) ?: 'application/octet-stream';
$size = @filesize($resolvedPath) ?: null;

if (function_exists('session_write_close')) {
    session_write_close();
}
ignore_user_abort(true);
set_time_limit(0);

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . str_replace('"', '', $filename) . '"');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
if ($size !== null) {
    header('Content-Length: ' . (string)$size);
}

$chunkSize = 1048576;
$handle = fopen($resolvedPath, 'rb');
if ($handle === false) {
    respond_with_error('Unable to read file stream.', 500);
}
while (!feof($handle)) {
    echo fread($handle, $chunkSize);
    flush();
}
fclose($handle);
exit;

function respond_with_error(string $message, int $status = 400): void
{
    http_response_code($status);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"/><title>Download</title>';
    echo '<link rel="preconnect" href="https://fonts.googleapis.com"/><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>';
    echo '<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600&display=swap" rel="stylesheet"/>';
    echo '<style>body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#050b13;color:#fff;font-family:\'Space Grotesk\',sans-serif;}';
    echo '.card{padding:2.5rem 3rem;border-radius:24px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);backdrop-filter:blur(16px);text-align:center;max-width:420px;}';
    echo '.card h1{font-size:1.5rem;margin-bottom:0.5rem;} .card p{color:#cbd5f5;font-size:0.95rem;margin-bottom:1.25rem;}';
    echo '.card a{display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.5rem;border-radius:999px;background:#1C74E9;color:#fff;text-decoration:none;font-weight:600;font-size:0.85rem;text-transform:uppercase;letter-spacing:0.05em;}';
    echo '</style></head><body><div class="card"><h1>Download unavailable</h1><p>' . escape_html($message) . '</p>';
    echo '<a href="' . escape_html(site_url('user/downloads.php')) . '">Back to downloads</a></div></body></html>';
    exit;
}
