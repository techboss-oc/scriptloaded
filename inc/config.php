<?php
$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
  $envFile = __DIR__ . '/../.env.example';
}
$lines = @file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
foreach ($lines as $line) {
  if (str_starts_with(trim($line), '#')) continue;
  [$key,$value] = array_pad(explode('=', $line, 2),2,null);
  if ($key !== null && $value !== null) {
    $_ENV[$key] = $value;
  }
}
$DB_HOST = $_ENV['DB_HOST'] ?? 'localhost';
$DB_NAME = $_ENV['DB_NAME'] ?? 'scriptloaded';
$DB_USER = $_ENV['DB_USER'] ?? 'root';
$DB_PASS = $_ENV['DB_PASS'] ?? '';
$BASE_URL = $_ENV['BASE_URL'] ?? '';
