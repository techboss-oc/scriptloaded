<?php
declare(strict_types=1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/helpers.php';
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/csrf.php';
require_once __DIR__ . '/../inc/store.php';
require_once __DIR__ . '/../inc/dashboard.php';

$currentUser = require_user();
$userProfile = build_user_profile($pdo, (int)$currentUser['id']);
$navLinks = get_dashboard_nav_links();