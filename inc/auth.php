<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/db.php';

if (!function_exists('scriptloaded_set_auth_cookie')) {
	function scriptloaded_set_auth_cookie(string $name, string $value, int $ttlSeconds): void
	{
		setcookie($name, $value, [
			'expires' => time() + $ttlSeconds,
			'path' => '/',
			'domain' => '',
			'secure' => scriptloaded_is_secure_request(),
			'httponly' => true,
			'samesite' => 'Lax',
		]);
	}
}

if (!function_exists('scriptloaded_clear_auth_cookie')) {
	function scriptloaded_clear_auth_cookie(string $name): void
	{
		setcookie($name, '', [
			'expires' => time() - 3600,
			'path' => '/',
			'domain' => '',
			'secure' => scriptloaded_is_secure_request(),
			'httponly' => true,
			'samesite' => 'Lax',
		]);
	}

	function scriptloaded_sanitize_redirect_target(?string $target, string $fallback = 'user/dashboard.php'): string
	{
		if ($target === null) {
			return $fallback;
		}
		$trimmed = trim($target);
		if ($trimmed === '') {
			return $fallback;
		}
		if (preg_match('#^(?:[a-z][a-z0-9+.-]*:|//)#i', $trimmed)) {
			return $fallback;
		}
		$relative = ltrim($trimmed, '/');
		return $relative === '' ? $fallback : $relative;
	}
}

function login_user(int $userId, bool $isAdmin = false): void
{
	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	session_regenerate_id(true);
	$_SESSION[SCRIPTLOADED_USER_SESSION_KEY] = $userId;
	scriptloaded_set_auth_cookie(SCRIPTLOADED_USER_COOKIE, '1', SCRIPTLOADED_AUTH_COOKIE_TTL);
	if ($isAdmin) {
		$_SESSION[SCRIPTLOADED_ADMIN_SESSION_KEY] = $userId;
		scriptloaded_set_auth_cookie(SCRIPTLOADED_ADMIN_COOKIE, '1', SCRIPTLOADED_AUTH_COOKIE_TTL);
	} else {
		unset($_SESSION[SCRIPTLOADED_ADMIN_SESSION_KEY]);
		scriptloaded_clear_auth_cookie(SCRIPTLOADED_ADMIN_COOKIE);
	}
}

function logout_user(): void
{
	if (session_status() !== PHP_SESSION_ACTIVE) {
		return;
	}
	unset($_SESSION[SCRIPTLOADED_USER_SESSION_KEY], $_SESSION[SCRIPTLOADED_ADMIN_SESSION_KEY]);
	scriptloaded_clear_auth_cookie(SCRIPTLOADED_USER_COOKIE);
	scriptloaded_clear_auth_cookie(SCRIPTLOADED_ADMIN_COOKIE);
	$_SESSION = [];
	if (ini_get('session.use_cookies')) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
	}
	session_destroy();
}

function logout_admin_session(): void
{
	if (session_status() !== PHP_SESSION_ACTIVE) {
		return;
	}
	unset($_SESSION[SCRIPTLOADED_ADMIN_SESSION_KEY]);
	scriptloaded_clear_auth_cookie(SCRIPTLOADED_ADMIN_COOKIE);
	session_regenerate_id(true);
}

function current_user(): ?array
{
	global $pdo;
	$userId = $_SESSION[SCRIPTLOADED_USER_SESSION_KEY] ?? null;
	if (!$userId) {
		return null;
	}
	$stmt = $pdo->prepare('SELECT id,email,full_name,is_admin FROM users WHERE id = ? LIMIT 1');
	$stmt->execute([$userId]);
	$row = $stmt->fetch();
	return $row ?: null;
}

function require_admin(): array
{
	$user = current_user();
	if (!$user || empty($user['is_admin'])) {
		header('Location: ' . site_url('admin/login.php'));
		exit;
	}
	return $user;
}

function require_user(): array
{
	$user = current_user();
	if (!$user) {
		$requestUri = $_SERVER['REQUEST_URI'] ?? 'user/dashboard.php';
		$redirectTarget = scriptloaded_sanitize_redirect_target($requestUri, 'user/dashboard.php');
		$loginPath = 'user/login.php?redirect=' . rawurlencode($redirectTarget);
		header('Location: ' . site_url($loginPath));
		exit;
	}
	return $user;
}
