<?php
require __DIR__ . '/../inc/auth.php';

logout_user();

header('Location: ' . site_url('user/login.php'));
exit;
