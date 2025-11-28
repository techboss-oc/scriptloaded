<?php
if (!defined('SCRIPTLOADED_SESSION_NAME')) {
    define('SCRIPTLOADED_SESSION_NAME', 'SCRIPTLOADEDSESSID');
}
if (!defined('SCRIPTLOADED_USER_SESSION_KEY')) {
    define('SCRIPTLOADED_USER_SESSION_KEY', 'auth_user_id');
}
if (!defined('SCRIPTLOADED_ADMIN_SESSION_KEY')) {
    define('SCRIPTLOADED_ADMIN_SESSION_KEY', 'auth_admin_id');
}
if (!defined('SCRIPTLOADED_USER_COOKIE')) {
    define('SCRIPTLOADED_USER_COOKIE', 'scriptloaded_user_auth');
}
if (!defined('SCRIPTLOADED_ADMIN_COOKIE')) {
    define('SCRIPTLOADED_ADMIN_COOKIE', 'scriptloaded_admin_auth');
}
if (!defined('SCRIPTLOADED_AUTH_COOKIE_TTL')) {
    define('SCRIPTLOADED_AUTH_COOKIE_TTL', 60 * 60 * 24 * 14);
}

if (!function_exists('scriptloaded_is_secure_request')) {
    function scriptloaded_is_secure_request(): bool
    {
        if (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') {
            return true;
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
            return true;
        }
        return ($_ENV['FORCE_SECURE_COOKIES'] ?? '') === '1';
    }
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(SCRIPTLOADED_SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => scriptloaded_is_secure_request(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
