<?php
$currentVisitor = function_exists('current_user') ? current_user() : null;
$isLoggedIn = (bool) $currentVisitor;
$isAdminVisitor = $isLoggedIn && !empty($currentVisitor['is_admin']);
$dashboardHref = $isAdminVisitor ? 'admin/index.php' : 'user/dashboard.php';
$authNavLabel = $isLoggedIn ? 'Dashboard' : 'Login';
$authNavIcon = $isLoggedIn ? 'space_dashboard' : 'login';
$authNavHref = $isLoggedIn ? $dashboardHref : 'user/login.php';

$primaryNavLinks = [
  ['label' => 'Home', 'href' => 'index.php'],
  ['label' => 'Marketplace', 'href' => 'listing.php'],
  ['label' => 'About', 'href' => 'about.php'],
  ['label' => 'Contact', 'href' => 'contact.php'],
];
if ($isLoggedIn) {
  $primaryNavLinks[] = ['label' => $authNavLabel, 'href' => $authNavHref];
}

$mobileNavLinks = [
  ['label' => 'Home', 'href' => 'index.php', 'icon' => 'home'],
  ['label' => 'Marketplace', 'href' => 'listing.php', 'icon' => 'storefront'],
  ['label' => 'About', 'href' => 'about.php', 'icon' => 'info'],
  ['label' => 'Contact', 'href' => 'contact.php', 'icon' => 'call'],
];
if ($isLoggedIn) {
  $mobileNavLinks[] = ['label' => $authNavLabel, 'href' => $authNavHref, 'icon' => $authNavIcon];
}

$footerLinkGroups = get_public_footer_link_groups([
  'isLoggedIn' => $isLoggedIn,
  'authNavLabel' => $authNavLabel,
  'dashboardHref' => $isLoggedIn ? $dashboardHref : null,
]);
?>
<!DOCTYPE html>
<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Product Not Found - Scriptloaded</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<style>.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}</style>
<script>
  tailwind.config = {
    darkMode: 'class',
    theme: {
      extend: {
        colors: {
          primary: '#1A73E8',
          'background-light': '#f6f7f8',
          'background-dark': '#111821',
        },
        fontFamily: {
          display: ['Space Grotesk', 'sans-serif'],
        },
        boxShadow: {
          'neo-light': '6px 6px 12px #d8dade, -6px -6px 12px #ffffff',
          'neo-dark': '6px 6px 12px #0c111b, -6px -6px 12px #161f2d',
        },
      },
    },
  };
</script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-white">
<div class="relative min-h-screen overflow-hidden">
  <div class="absolute inset-0 bg-gradient-to-br from-background-dark via-background-dark/90 to-background-dark/70"></div>
  <div class="relative z-10 flex min-h-screen flex-col">
    <div class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 lg:px-10">
      <?php include __DIR__ . '/partials/public_header.php'; ?>
    </div>
    <main class="flex flex-1 items-center justify-center px-4 py-16 sm:px-6">
      <div class="max-w-xl rounded-3xl border border-white/10 bg-white/5 p-10 text-center shadow-2xl">
        <span class="material-symbols-outlined text-5xl text-primary">inventory_2</span>
        <h1 class="mt-6 text-3xl font-bold">We couldn’t load the featured product.</h1>
        <p class="mt-4 text-sm text-gray-300">It looks like the highlighted script is offline or hasn’t been created yet. Browse the marketplace to see everything that’s available right now.</p>
        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
          <a class="inline-flex flex-1 items-center justify-center rounded-2xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow shadow-primary/40" href="<?= escape_html(site_url('listing.php')); ?>">Browse Marketplace</a>
          <a class="inline-flex flex-1 items-center justify-center rounded-2xl border border-white/20 px-5 py-3 text-sm font-semibold text-white" href="<?= escape_html(site_url('index.php')); ?>">Back Home</a>
        </div>
      </div>
    </main>
    <?php include __DIR__ . '/partials/public_footer.php'; ?>
  </div>
</div>
<script src="assets/js/mobile-menu.js"></script>
</body></html>
