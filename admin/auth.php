<?php
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/helpers.php';
require __DIR__ . '/../inc/auth.php';

$action = $_GET['action'] ?? '';
if ($action === 'logout') {
    logout_user();
    header('Location: ' . site_url('admin/login.php'));
    exit;
}

if (!current_user() || !(current_user()['is_admin'] ?? false)) {
    header('Location: ' . site_url('admin/login.php'));
    exit;
}

header('Location: ' . site_url('admin/index.php'));
exit;
