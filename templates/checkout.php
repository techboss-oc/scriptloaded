<?php
$currentVisitor = $currentUser ?? (function_exists('current_user') ? current_user() : null);
$isLoggedIn = (bool) $currentVisitor;
$isAdminVisitor = $isLoggedIn && !empty($currentVisitor['is_admin']);
$dashboardHref = $isAdminVisitor ? 'admin/index.php' : 'user/dashboard.php';
$authNavLabel = $isLoggedIn ? 'Dashboard' : 'Login';
$authNavIcon = $isLoggedIn ? 'space_dashboard' : 'login';
$authNavHref = $isLoggedIn ? $dashboardHref : 'user/login.php';
$primaryCtaLabel = $isLoggedIn ? 'View Dashboard' : 'Register';
$primaryCtaHref = $isLoggedIn ? $dashboardHref : 'user/register.php';

$primaryNavLinks = [
  ['label' => 'Home', 'href' => 'index.php'],
  ['label' => 'Marketplace', 'href' => 'listing.php'],
  ['label' => 'Featured Product', 'href' => 'product.php?slug=' . urlencode($product['slug'])],
  ['label' => 'About', 'href' => 'about.php'],
  ['label' => 'Contact', 'href' => 'contact.php'],
];

$mobileNavLinks = [
  ['label' => 'Home', 'href' => 'index.php', 'icon' => 'home'],
  ['label' => 'Marketplace', 'href' => 'listing.php', 'icon' => 'storefront'],
  ['label' => 'Featured Product', 'href' => 'product.php?slug=' . urlencode($product['slug']), 'icon' => 'rocket_launch'],
  ['label' => 'About', 'href' => 'about.php', 'icon' => 'info'],
  ['label' => 'Contact', 'href' => 'contact.php', 'icon' => 'call'],
];

$footerLinkGroups = get_public_footer_link_groups([
  'isLoggedIn' => $isLoggedIn,
  'authNavLabel' => $authNavLabel,
  'dashboardHref' => $isLoggedIn ? $dashboardHref : null,
]);

$priceDisplay = format_currency($orderAmount, $currency);
$alternateCurrency = $currency === 'USD' ? 'NGN' : 'USD';
$alternateAmount = scriptloaded_calculate_product_price($product, $alternateCurrency);
$savedCards = array_map(static function (array $method) {
  return [
    'brand' => $method['brand'],
    'last4' => $method['last4'],
    'exp' => str_pad((string)$method['exp_month'], 2, '0', STR_PAD_LEFT) . '/' . substr((string)$method['exp_year'], -2),
    'is_primary' => (bool)$method['is_primary'],
  ];
}, $billingMethods ?? []);
?>
<!DOCTYPE html>
<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Checkout • <?= escape_html($product['title']); ?></title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<style>
  .material-symbols-outlined {
    font-variation-settings:
    'FILL' 0,
    'wght' 400,
    'GRAD' 0,
    'opsz' 24;
  }
</style>
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
  <div class="pointer-events-none absolute inset-0 opacity-70">
    <div class="absolute -left-32 top-10 h-80 w-80 rounded-full bg-primary/20 blur-3xl"></div>
    <div class="absolute right-0 top-24 h-96 w-96 rounded-full bg-primary/10 blur-[140px]"></div>
    <div class="absolute inset-x-0 bottom-0 h-72 bg-gradient-to-r from-primary/5 via-transparent to-primary/5 blur-[120px]"></div>
  </div>
  <div class="relative flex min-h-screen flex-col">
    <div class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 lg:px-10">
      <?php include __DIR__ . '/partials/public_header.php'; ?>
    </div>
    <main class="mx-auto w-full max-w-6xl flex-1 px-4 pb-16 sm:px-6 lg:px-10">
      <div class="rounded-[32px] border border-white/10 bg-white/5 p-6 backdrop-blur">
        <p class="text-xs uppercase tracking-[0.3em] text-primary">Secure checkout</p>
        <h1 class="mt-3 text-4xl font-black tracking-tight">Complete your purchase</h1>
        <p class="mt-2 text-gray-300"><?= escape_html($product['title']); ?> · Instant digital delivery</p>
      </div>
      <div class="mt-8 grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
        <section class="space-y-6 rounded-[32px] border border-white/10 bg-white/5 p-6">
          <?php if ($success): ?>
            <div class="rounded-2xl border border-emerald-400/50 bg-emerald-500/10 px-5 py-4 text-sm text-emerald-100">
              <p class="font-semibold"><?= escape_html($success); ?></p>
              <?php if ($downloadLink): ?>
                <a href="<?= escape_html($downloadLink); ?>" class="mt-3 inline-flex items-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-semibold text-white">Download now<span class="material-symbols-outlined text-base">arrow_downward</span></a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
          <?php if ($errors): ?>
            <div class="rounded-2xl border border-red-400/40 bg-red-500/10 px-5 py-4 text-sm text-red-200">
              <ul class="list-disc pl-5">
                <?php foreach ($errors as $error): ?>
                  <li><?= escape_html($error); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
          <form method="post" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= escape_html($csrfToken); ?>"/>
            <input type="hidden" name="slug" value="<?= escape_html($product['slug']); ?>"/>
            <section class="rounded-2xl border border-white/10 bg-white/5 p-5">
              <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                  <p class="text-xs uppercase tracking-[0.3em] text-primary">Billing currency</p>
                  <h2 class="text-xl font-semibold text-white">Choose your pricing</h2>
                </div>
                <div class="inline-flex rounded-full border border-white/10 bg-background-dark/40 p-1">
                  <label class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold <?= $currency === 'USD' ? 'bg-primary text-white' : 'text-gray-300'; ?>">
                    <input class="sr-only" type="radio" name="currency" value="USD" <?= $currency === 'USD' ? 'checked' : ''; ?> onchange="this.form.submit()"/>
                    USD
                  </label>
                  <label class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold <?= $currency === 'NGN' ? 'bg-primary text-white' : 'text-gray-300'; ?>">
                    <input class="sr-only" type="radio" name="currency" value="NGN" <?= $currency === 'NGN' ? 'checked' : ''; ?> onchange="this.form.submit()"/>
                    NGN
                  </label>
                </div>
              </div>
              <p class="mt-4 text-3xl font-bold text-white"><?= escape_html($priceDisplay); ?></p>
              <p class="text-sm text-gray-400">≈ <?= escape_html(format_currency($alternateAmount, $alternateCurrency)); ?> at current rate.</p>
            </section>
            <section class="rounded-2xl border border-white/10 bg-white/5 p-5">
              <p class="text-xs uppercase tracking-[0.3em] text-primary">Payment method</p>
              <div class="mt-4 grid gap-4 md:grid-cols-2">
                <?php foreach ($gateways as $gateway => $meta): ?>
                  <label class="flex cursor-pointer flex-col gap-3 rounded-2xl border <?= $selectedGateway === $gateway ? 'border-primary/60 bg-primary/10 text-white' : 'border-white/15 bg-background-dark/40 text-gray-300'; ?> p-4">
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-base font-semibold text-white"><?= escape_html($meta['label']); ?></p>
                        <p class="text-sm text-gray-400"><?= escape_html($meta['description']); ?></p>
                      </div>
                      <?php if (!empty($meta['badge'])): ?>
                        <span class="rounded-full border border-white/20 px-3 py-1 text-xs uppercase tracking-[0.2em] text-gray-200"><?= escape_html($meta['badge']); ?></span>
                      <?php endif; ?>
                    </div>
                    <input type="radio" name="gateway" value="<?= escape_html($gateway); ?>" class="sr-only" <?= $selectedGateway === $gateway ? 'checked' : ''; ?> />
                  </label>
                <?php endforeach; ?>
              </div>
            </section>
            <section class="rounded-2xl border border-white/10 bg-white/5 p-5">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-xs uppercase tracking-[0.3em] text-primary">Saved cards</p>
                  <h2 class="text-xl font-semibold text-white">Use a stored method</h2>
                </div>
                <a href="<?= escape_html(site_url('user/billing.php')); ?>" class="text-sm font-semibold text-primary">Manage billing</a>
              </div>
              <div class="mt-4 space-y-3">
                <?php if ($savedCards): ?>
                  <?php foreach ($savedCards as $card): ?>
                    <div class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-gray-200">
                      <span><?= escape_html($card['brand']); ?> ending • <?= escape_html($card['last4']); ?></span>
                      <span><?= escape_html($card['exp']); ?><?= $card['is_primary'] ? ' · primary' : ''; ?></span>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p class="rounded-xl border border-dashed border-white/20 px-4 py-3 text-sm text-gray-400">No cards saved yet. You can still pay via your selected gateway.</p>
                <?php endif; ?>
              </div>
            </section>
            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-primary px-4 py-3 text-sm font-semibold uppercase tracking-[0.3em] text-white shadow-lg shadow-primary/40">
              <span class="material-symbols-outlined text-base">lock</span>
              Pay <?= escape_html($priceDisplay); ?> securely
            </button>
          </form>
        </section>
        <aside class="space-y-6 rounded-[32px] border border-white/10 bg-white/5 p-6">
          <div class="rounded-2xl border border-white/10 bg-background-dark/40 p-5">
            <p class="text-xs uppercase tracking-[0.3em] text-primary">Order summary</p>
            <div class="mt-4 flex gap-4">
              <img src="<?= escape_html($product['preview_image'] ?? $product['image'] ?? ''); ?>" alt="<?= escape_html($product['title']); ?>" class="h-20 w-20 rounded-xl object-cover"/>
              <div>
                <p class="text-lg font-semibold text-white"><?= escape_html($product['title']); ?></p>
                <p class="text-sm text-gray-400">Category · <?= escape_html($product['category'] ?? 'Digital Asset'); ?></p>
                <p class="mt-2 text-sm text-gray-400">Includes lifetime updates + support</p>
              </div>
            </div>
            <div class="mt-4 border-t border-white/10 pt-4 text-sm text-gray-300">
              <div class="flex items-center justify-between">
                <span>Subtotal</span>
                <span><?= escape_html($priceDisplay); ?></span>
              </div>
              <div class="flex items-center justify-between">
                <span>Platform fee</span>
                <span>Included</span>
              </div>
              <div class="mt-3 flex items-center justify-between text-base font-semibold text-white">
                <span>Total</span>
                <span><?= escape_html($priceDisplay); ?></span>
              </div>
            </div>
          </div>
          <div class="rounded-2xl border border-white/10 bg-background-dark/40 p-5 text-sm text-gray-300">
            <p class="text-sm font-semibold text-white">Need an invoice?</p>
            <p class="mt-2 text-gray-400">Invoices generate automatically once payment succeeds. Find them under Billing → Invoices.</p>
            <?php if ($orderReference): ?>
              <p class="mt-4 text-xs text-gray-400">Reference: <?= escape_html($orderReference); ?></p>
            <?php endif; ?>
          </div>
        </aside>
      </div>
    </main>
    <?php include __DIR__ . '/partials/public_footer.php'; ?>
  </div>
</div>
<script src="assets/js/mobile-menu.js"></script>
</body></html>
