<?php
if (!defined('SCRIPTLOADED_CURRENCY_COOKIE')) {
	define('SCRIPTLOADED_CURRENCY_COOKIE', 'scriptloaded_currency');
}
function escape_html($v){return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');}
function format_currency($amount,$currency='USD'){
	$symbol=$currency==='NGN'?'₦':'$';
	$decimals=$currency==='NGN'?0:2;
	return $symbol.number_format((float)$amount,$decimals);
}
function convert_usd_to_ngn($amount,$rate=null){
	$fallback=isset($_ENV['CURRENCY_RATE_USD_NGN'])?(float)$_ENV['CURRENCY_RATE_USD_NGN']:1150;
	$rate=$rate!==null?(float)$rate:$fallback;
	return (float)$amount*(float)$rate;
}
function normalize_currency(?string $value): string {
	return in_array($value,['USD','NGN'],true)?$value:'USD';
}
function detect_card_brand(string $cardNumber): string {
	$first=substr(preg_replace('/\D+/','',$cardNumber),0,1);
	switch($first){
		case '4': return 'Visa';
		case '5': return 'Mastercard';
		case '3': return 'Amex';
		case '6': return 'Discover';
		default: return 'Card';
	}
}
/**
 * Generate absolute URLs while honoring BASE_URL or falling back to the detected subdirectory.
 */
function site_url(string $path = ''): string {
	$trimmed = trim($path);
	$base = rtrim($_ENV['BASE_URL'] ?? '', '/');
	if ($trimmed === '' || $trimmed === '/') {
		if ($base !== '') {
			return $base . '/';
		}
		$prefix = detect_base_path();
		return $prefix === '' ? '/' : rtrim($prefix, '/') . '/';
	}
	if (preg_match('/^(https?:|mailto:|tel:|#)/i', $trimmed)) {
		return $trimmed;
	}
	$normalizedPath = ltrim($trimmed, '/');
	if ($base !== '') {
		return $base . '/' . $normalizedPath;
	}
	$prefix = detect_base_path();
	$prefix = $prefix === '' ? '' : rtrim($prefix, '/');
	return ($prefix === '' ? '' : $prefix) . '/' . $normalizedPath;
}

/**
 * Detect the project subdirectory when BASE_URL is not set (e.g. http://localhost/scriptloaded).
 */
function detect_base_path(): string {
	static $cached = null;
	if ($cached !== null) {
		return $cached;
	}
	$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
	$projectRoot = realpath(__DIR__ . '/..') ?: '';
	if ($docRoot !== '' && $projectRoot !== '') {
		$docRootReal = rtrim(str_replace('\\', '/', realpath($docRoot) ?: $docRoot), '/');
		$projectRootReal = rtrim(str_replace('\\', '/', $projectRoot), '/');
		if ($docRootReal !== '' && str_starts_with($projectRootReal, $docRootReal)) {
			$relative = trim(substr($projectRootReal, strlen($docRootReal)), '/');
			$cached = $relative === '' ? '' : '/' . $relative;
			return $cached;
		}
	}
	$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
	if ($scriptName !== '') {
		$segments = explode('/', trim($scriptName, '/'));
		if (count($segments) > 1) {
			$cached = '/' . $segments[0];
			return $cached;
		}
	}
	$cached = '';
	return $cached;
}
function get_setting($key,$default=null){
	static $settingsLoaded=false;
	global $pdo, $SETTINGS_CACHE;
	if(!$settingsLoaded){
		if(!isset($pdo)){
			return $default;
		}
		try{
			$rows=$pdo->query('SELECT `key`,`value` FROM settings');
			$SETTINGS_CACHE=[];
			foreach($rows as $row){
				$SETTINGS_CACHE[$row['key']]=$row['value'];
			}
		}catch(Throwable $e){
			return $default;
		}
		$settingsLoaded=true;
	}
	if(!is_array($SETTINGS_CACHE)){
		$SETTINGS_CACHE=[];
	}
	return array_key_exists($key,$SETTINGS_CACHE)?$SETTINGS_CACHE[$key]:$default;
}
function set_setting($key,$value){
	global $pdo, $SETTINGS_CACHE;
	if(!isset($pdo)){
		return false;
	}
	$stmt=$pdo->prepare('INSERT INTO settings (`key`,`value`) VALUES (:key,:value) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)');
	$result=$stmt->execute([':key'=>$key,':value'=>$value]);
	if($result){
		if(!is_array($SETTINGS_CACHE)){
			$SETTINGS_CACHE=[];
		}
		$SETTINGS_CACHE[$key]=$value;
	}
	return $result;
}

function scriptloaded_store_currency_preference(string $currency): void {
	$normalized = normalize_currency($currency);
	if (!headers_sent()) {
		$secure = function_exists('scriptloaded_is_secure_request') ? scriptloaded_is_secure_request() : (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off');
		setcookie(SCRIPTLOADED_CURRENCY_COOKIE, $normalized, [
			'expires' => time() + (60 * 60 * 24 * 30),
			'path' => '/',
			'domain' => '',
			'secure' => $secure,
			'httponly' => false,
			'samesite' => 'Lax',
		]);
	}
	$_COOKIE[SCRIPTLOADED_CURRENCY_COOKIE] = $normalized;
}

function scriptloaded_client_ip(): ?string {
	$candidates = [
		$_SERVER['HTTP_CF_CONNECTING_IP'] ?? null,
		$_SERVER['HTTP_X_FORWARDED_FOR'] ?? null,
		$_SERVER['HTTP_X_REAL_IP'] ?? null,
		$_SERVER['REMOTE_ADDR'] ?? null,
	];
	foreach ($candidates as $candidate) {
		if (!$candidate) {
			continue;
		}
		$parts = str_contains($candidate, ',') ? explode(',', $candidate) : [$candidate];
		foreach ($parts as $ip) {
			$ip = trim($ip);
			if ($ip !== '' && filter_var($ip, FILTER_VALIDATE_IP)) {
				return $ip;
			}
		}
	}
	return null;
}

function scriptloaded_detect_country_code(): ?string {
	$override = $_ENV['SCRIPTLOADED_FORCE_COUNTRY'] ?? null;
	if ($override && preg_match('/^[A-Z]{2}$/i', $override)) {
		return strtoupper($override);
	}
	$headerKeys = ['HTTP_CF_IPCOUNTRY', 'HTTP_X_COUNTRY_CODE', 'HTTP_X_APPENGINE_COUNTRY', 'GEOIP_COUNTRY_CODE'];
	foreach ($headerKeys as $key) {
		$value = $_SERVER[$key] ?? '';
		if ($value && preg_match('/^[A-Z]{2}$/i', $value) && strtoupper($value) !== 'XX') {
			return strtoupper($value);
		}
	}
	$ip = scriptloaded_client_ip();
	if (!$ip) {
		return null;
	}
	$isPublicIp = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	if ($isPublicIp === false) {
		return null;
	}
	if (session_status() === PHP_SESSION_ACTIVE) {
		$cache = $_SESSION['scriptloaded_geo_cache'] ?? null;
		if (is_array($cache) && ($cache['ip'] ?? null) === $ip && ($cache['expires'] ?? 0) > time()) {
			return $cache['code'] ?? null;
		}
	}
	$country = null;
	$endpoint = 'https://ipapi.co/' . rawurlencode($ip) . '/country/';
	$context = stream_context_create(['http' => ['timeout' => 1.5]]);
	$response = @file_get_contents($endpoint, false, $context);
	if ($response !== false) {
		$candidate = strtoupper(trim($response));
		if (preg_match('/^[A-Z]{2}$/', $candidate) && $candidate !== 'ZZ') {
			$country = $candidate;
		}
	}
	if ($country && session_status() === PHP_SESSION_ACTIVE) {
		$_SESSION['scriptloaded_geo_cache'] = [
			'ip' => $ip,
			'code' => $country,
			'expires' => time() + 86400,
		];
	}
	return $country;
}

function scriptloaded_detect_currency_default(): string {
	$country = scriptloaded_detect_country_code();
	if ($country === 'NG') {
		return 'NGN';
	}
	$setting = get_setting('currency_default', 'USD');
	return normalize_currency($setting);
}

function scriptloaded_current_currency(): string {
	static $resolved = null;
	if ($resolved !== null) {
		return $resolved;
	}
	$userChoice = $_POST['currency'] ?? $_GET['currency'] ?? null;
	if ($userChoice !== null) {
		$resolved = normalize_currency($userChoice);
		scriptloaded_store_currency_preference($resolved);
		return $resolved;
	}
	$cookieChoice = $_COOKIE[SCRIPTLOADED_CURRENCY_COOKIE] ?? null;
	if ($cookieChoice) {
		$resolved = normalize_currency($cookieChoice);
		if ($resolved !== $cookieChoice) {
			scriptloaded_store_currency_preference($resolved);
		}
		return $resolved;
	}
	$resolved = normalize_currency(scriptloaded_detect_currency_default());
	scriptloaded_store_currency_preference($resolved);
	return $resolved;
}

/**
 * Ensure a directory exists before writing files.
 */
function ensure_directory(string $path): void {
	if (is_dir($path)) {
		return;
	}
	if (!mkdir($path, 0775, true) && !is_dir($path)) {
		throw new RuntimeException('Unable to create directory: ' . $path);
	}
}

/**
 * Persist an uploaded product image and return its absolute URL.
 */
function store_product_image_upload(array $file, string $prefix = 'product'): string {
	$errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
	if ($errorCode === UPLOAD_ERR_NO_FILE) {
		throw new RuntimeException('No file was uploaded.');
	}
	if ($errorCode !== UPLOAD_ERR_OK) {
		throw new RuntimeException('Upload failed (code ' . $errorCode . ').');
	}
	if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
		throw new RuntimeException('Invalid upload payload.');
	}
	$maxBytes = 5 * 1024 * 1024; // 5MB
	if (($file['size'] ?? 0) > $maxBytes) {
		throw new RuntimeException('Image must be 5MB or smaller.');
	}
	$mime = mime_content_type($file['tmp_name']) ?: '';
	$allowed = [
		'image/jpeg' => 'jpg',
		'image/png' => 'png',
		'image/webp' => 'webp',
		'image/gif' => 'gif',
	];
	if (!isset($allowed[$mime])) {
		throw new RuntimeException('Only JPG, PNG, GIF, or WEBP images are allowed.');
	}
	$uploadDir = __DIR__ . '/../assets/uploads/products';
	ensure_directory($uploadDir);
	$filename = $prefix . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
	$target = $uploadDir . DIRECTORY_SEPARATOR . $filename;
	if (!move_uploaded_file($file['tmp_name'], $target)) {
		throw new RuntimeException('Unable to store uploaded image.');
	}
	$relative = 'assets/uploads/products/' . $filename;
	return site_url($relative);
}

/**
 * Normalize common YouTube URLs or IDs into an embeddable src.
 */
function youtube_embed_url(?string $value): ?string {
	if ($value === null) {
		return null;
	}
	$trimmed = trim($value);
	if ($trimmed === '') {
		return null;
	}
	$lower = strtolower($trimmed);
	if (str_contains($lower, 'youtube.com/embed') || str_contains($lower, 'youtube-nocookie.com/embed')) {
		return $trimmed;
	}
	$patterns = [
		'~youtu\.be/([a-zA-Z0-9_-]{6,})~',
		'~youtube\.com/(?:watch\?v=|shorts/|embed/)([a-zA-Z0-9_-]{6,})~',
		'~v=([a-zA-Z0-9_-]{6,})~',
	];
	foreach ($patterns as $pattern) {
		if (preg_match($pattern, $trimmed, $matches)) {
			return 'https://www.youtube.com/embed/' . $matches[1];
		}
	}
	if (preg_match('~^[a-zA-Z0-9_-]{6,}$~', $trimmed)) {
		return 'https://www.youtube.com/embed/' . $trimmed;
	}
	parse_str(parse_url($trimmed, PHP_URL_QUERY) ?? '', $params);
	if (!empty($params['v']) && preg_match('~^[a-zA-Z0-9_-]{6,}$~', $params['v'])) {
		return 'https://www.youtube.com/embed/' . $params['v'];
	}
	return $trimmed;
}

/**
 * Convert arbitrary text into a URL-friendly slug.
 */
function slugify(string $value): string {
	$slug = strtolower(trim($value));
	$slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
	$slug = trim($slug, '-');
	if ($slug === '') {
		try {
			$slug = substr(bin2hex(random_bytes(8)), 0, 12);
		} catch (Throwable $e) {
			$slug = uniqid();
		}
	}
	return $slug;
}

/**
 * Default category presets aligned with Scriptloaded's product catalog.
 */
function get_default_product_categories(): array {
	return [
		[
			'slug' => 'web-app-scripts',
			'label' => 'Web App Scripts',
			'type' => 'Script',
			'description' => 'Deployable PHP and JavaScript utilities, SaaS boilerplates, and automation bundles.',
		],
		[
			'slug' => 'site-landing-templates',
			'label' => 'Site & Landing Templates',
			'type' => 'Template',
			'description' => 'Stitch-powered marketing pages, multi-section websites, and conversion-focused landers.',
		],
		[
			'slug' => 'wordpress-themes',
			'label' => 'WordPress Themes',
			'type' => 'Theme',
			'description' => 'Curated WP themes for agencies, portfolios, directories, and blogs.',
		],
		[
			'slug' => 'wordpress-plugins',
			'label' => 'WordPress Plugins',
			'type' => 'Plugin',
			'description' => 'Feature add-ons: forms, security, payments, and workflow helpers for WordPress.',
		],
		[
			'slug' => 'cms-dashboard-kits',
			'label' => 'CMS & Dashboard UI Kits',
			'type' => 'UI Kit',
			'description' => 'Admin dashboards, component libraries, and CMS-ready layouts built with Tailwind or Bootstrap.',
		],
		[
			'slug' => 'ecommerce-marketplace-kits',
			'label' => 'Ecommerce & Marketplace Kits',
			'type' => 'Commerce',
			'description' => 'Storefront starters, checkout flows, and marketplace scaffolds with payment hooks.',
		],
		[
			'slug' => 'mobile-app-source',
			'label' => 'Mobile App Source Codes',
			'type' => 'Source Code',
			'description' => 'React Native, Flutter, and native starter kits with core screens and API wiring.',
		],
		[
			'slug' => 'utilities-integrations',
			'label' => 'Utilities & Integrations',
			'type' => 'Utility',
			'description' => 'Payment gateway hooks, auth modules, and reusable PHP libraries ready for drop-in use.',
		],
		[
			'slug' => 'automation-no-code',
			'label' => 'Automation & No-Code Helpers',
			'type' => 'Automation',
			'description' => 'Workflow bridges, webhook handlers, and Zapier-style connectors packaged for no-code stacks.',
		],
	];
}

/**
 * Fetch saved categories or fall back to defaults.
 */
function get_product_categories(bool $refresh = false): array {
	global $PRODUCT_CATEGORIES_CACHE;
	if (!$refresh && is_array($PRODUCT_CATEGORIES_CACHE)) {
		return $PRODUCT_CATEGORIES_CACHE;
	}
	$value = get_setting('product_categories');
	if (is_string($value) && $value !== '') {
		$decoded = json_decode($value, true);
		if (is_array($decoded)) {
			$PRODUCT_CATEGORIES_CACHE = array_values(array_map(function ($item) {
				return [
					'slug' => slugify($item['slug'] ?? ($item['label'] ?? 'item')),
					'label' => trim((string)($item['label'] ?? 'Untitled')),
					'type' => isset($item['type']) && $item['type'] !== '' ? trim((string)$item['type']) : null,
					'description' => isset($item['description']) && $item['description'] !== '' ? trim((string)$item['description']) : null,
				];
			}, $decoded));
			return $PRODUCT_CATEGORIES_CACHE;
		}
	}
	$PRODUCT_CATEGORIES_CACHE = get_default_product_categories();
	return $PRODUCT_CATEGORIES_CACHE;
}

/**
 * Persist the category collection into settings for reuse across forms.
 */
function save_product_categories(array $categories): bool {
	global $PRODUCT_CATEGORIES_CACHE;
	$normalized = [];
	foreach ($categories as $category) {
		if (!isset($category['label'])) {
			continue;
		}
		$label = trim((string)$category['label']);
		if ($label === '') {
			continue;
		}
		$normalized[] = [
			'slug' => slugify($category['slug'] ?? $label),
			'label' => $label,
			'type' => isset($category['type']) && $category['type'] !== '' ? trim((string)$category['type']) : null,
			'description' => isset($category['description']) && $category['description'] !== '' ? trim((string)$category['description']) : null,
		];
	}
	$encoded = json_encode($normalized, JSON_UNESCAPED_SLASHES);
	if ($encoded === false) {
		return false;
	}
	$result = set_setting('product_categories', $encoded);
	if ($result) {
		$PRODUCT_CATEGORIES_CACHE = $normalized;
	}
	return $result;
}

/**
 * Build the trimmed footer navigation groups for public pages.
 */
function get_public_footer_link_groups(array $options = []): array {
	$isLoggedIn = (bool)($options['isLoggedIn'] ?? false);
	$dashboardHref = $options['dashboardHref'] ?? ($isLoggedIn ? 'user/dashboard' : 'user/login');
	$authNavLabel = $options['authNavLabel'] ?? ($isLoggedIn ? 'Dashboard' : 'Login');
	if ($isLoggedIn) {
		$accountTitle = 'Your account';
		$accountLinks = [
			['label' => $authNavLabel, 'href' => $dashboardHref],
			['label' => 'Support', 'href' => 'contact'],
		];
	} else {
		$accountTitle = 'Account';
		$accountLinks = [
			['label' => 'Login', 'href' => 'user/login'],
			['label' => 'Create Account', 'href' => 'user/register'],
			['label' => 'Forgot Password', 'href' => 'user/forgot_password'],
		];
	}

	return [
		[
			'title' => 'Marketplace',
			'links' => [
				['label' => 'Home', 'href' => 'index'],
				['label' => 'Browse Marketplace', 'href' => 'listing'],
				['label' => 'Featured Product', 'href' => 'product?slug=ecommerce-website-script'],
			],
		],
		[
			'title' => 'Company',
			'links' => [
				['label' => 'About', 'href' => 'about'],
				['label' => 'Contact', 'href' => 'contact'],
				['label' => 'Privacy Policy', 'href' => 'privacy'],
				['label' => 'Terms & Conditions', 'href' => 'terms'],
			],
		],
		[
			'title' => $accountTitle,
			'links' => $accountLinks,
		],
	];
}
