<?php
/** @var array $featuredProducts */
/** @var array $latestProducts */
/** @var array $featuredCategories */
/** @var string $currency */

$featuredProducts = $featuredProducts ?? [];
$latestProducts = $latestProducts ?? [];
$featuredCategories = $featuredCategories ?? get_product_categories();
$homeCategories = array_slice($featuredCategories, 0, 5);

$currentVisitor = function_exists('current_user') ? current_user() : null;
$isLoggedIn = (bool)$currentVisitor;
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
  ['label' => 'Featured Product', 'href' => 'product.php?slug=ecommerce-website-script'],
  ['label' => 'About', 'href' => 'about.php'],
  ['label' => 'Contact', 'href' => 'contact.php'],
];

$mobileNavLinks = [
  ['label' => 'Home', 'href' => 'index.php', 'icon' => 'home'],
  ['label' => 'Marketplace', 'href' => 'listing.php', 'icon' => 'storefront'],
  ['label' => 'Featured Product', 'href' => 'product.php?slug=ecommerce-website-script', 'icon' => 'rocket_launch'],
  ['label' => 'About', 'href' => 'about.php', 'icon' => 'info'],
  ['label' => 'Contact', 'href' => 'contact.php', 'icon' => 'call'],
];

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
<title>Scriptloaded - The Future of Digital Assets</title>
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
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#1A73E8",
              "background-light": "#f6f7f8",
              "background-dark": "#111821",
            },
            fontFamily: {
              display: ["Space Grotesk", "sans-serif"],
            },
            boxShadow: {
              "neo-light": "6px 6px 12px #d8dade, -6px -6px 12px #ffffff",
              "neo-dark": "6px 6px 12px #0c111b, -6px -6px 12px #161f2d",
            },
          },
        },
      }
    </script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-white">
<div class="relative flex h-auto min-h-screen w-full flex-col group/design-root overflow-x-hidden">
<div class="layout-container flex h-full grow flex-col">
<div class="px-4 sm:px-8 md:px-16 lg:px-24 xl:px-40 flex flex-1 justify-center py-5">
<div class="layout-content-container flex flex-col max-w-[960px] flex-1 w-full">
<?php include __DIR__ . '/partials/public_header.php'; ?>
<main class="flex flex-col gap-10 sm:gap-16">
<!-- HeroSection -->
<div class="@container mt-10">
<div class="@[480px]:p-4">
<div class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 @[480px]:rounded-xl items-center justify-center p-4" data-alt="Abstract futuristic gradient background with blue and purple hues" style='background-image: linear-gradient(rgba(17, 24, 33, 0.4) 0%, rgba(17, 24, 33, 0.8) 100%), url("https://lh3.googleusercontent.com/aida-public/AB6AXuDykr2a5eBzcCRxELFWwRcR-lNOhqslDeRZnBMmwbe7_Euu23K7A1GnaUHczM2zCwGHwZuol9aTs-Q_hIC4oGEIYH-a3yTqQLS2rQeWtdDFcuDfVmvWr7WSn6IAIZe2bYR5anqIvP2eQqmGwIQ3LAApayBEa1dDpq1nhEyr6wPYG5fhAgy3pIVkfVmOaN6Td_CvVQrLMJM45jaLSiGmbaXFrsHOCj_uqqLsTWPMxVh31gyzF54hYIAhOtD_c32nyyUA49DqJBIyAy4R");'>
<div class="flex flex-col gap-2 text-center">
<h1 class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]">The Future of Digital Assets is Here.</h1>
<h2 class="text-gray-300 text-sm font-normal leading-normal @[480px]:text-base @[480px]:font-normal @[480px]:leading-normal">Discover thousands of scripts, themes, and plugins to build your next project.</h2>
</div>
<label class="flex flex-col min-w-40 h-14 w-full max-w-[480px] @[480px]:h-16">
<div class="flex w-full flex-1 items-stretch rounded-xl h-full shadow-lg">
<div class="text-gray-400 flex border border-white/20 bg-background-dark/50 backdrop-blur-sm items-center justify-center pl-[15px] rounded-l-xl border-r-0">
<span class="material-symbols-outlined">search</span>
</div>
<input class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-none text-white focus:outline-0 focus:ring-2 focus:ring-primary h-full placeholder:text-gray-400 px-[15px] border-y border-white/20 bg-background-dark/50 backdrop-blur-sm text-sm font-normal leading-normal @[480px]:text-base @[480px]:font-normal @[480px]:leading-normal" placeholder="Search for scripts, themes, plugins..." value=""/>
<div class="flex items-center justify-center rounded-r-xl border-l-0 border border-white/20 bg-background-dark/50 backdrop-blur-sm pr-[7px]">
<button class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 @[480px]:h-12 @[480px]:px-5 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] @[480px]:text-base @[480px]:font-bold @[480px]:leading-normal @[480px]:tracking-[0.015em] hover:bg-primary/80 transition-colors">
<span class="truncate">Search</span>
</button>
</div>
</div>
</label>
</div>
</div>
</div>
<!-- Featured Products Section -->
<section>
<h2 class="text-white text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Featured Products</h2>
<div class="grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-4 p-4">
<?php foreach ($featuredProducts as $product): ?>
<?php
  $featuredSlug = (string)($product['slug'] ?? '');
  $productUrl = $featuredSlug !== '' ? site_url('product.php?slug=' . urlencode($featuredSlug)) : site_url('listing.php');
  $previewUrl = !empty($product['live_preview_url']) ? $product['live_preview_url'] : $productUrl;
  $previewTarget = $previewUrl !== $productUrl ? '_blank' : '_self';
  $checkoutPath = $featuredSlug !== '' ? 'checkout.php?slug=' . urlencode($featuredSlug) . '&currency=' . urlencode($currency) : 'listing.php';
  $buyUrl = $isLoggedIn
    ? site_url($checkoutPath)
    : site_url('user/login.php?redirect=' . rawurlencode($checkoutPath));
?>
<div class="flex flex-col gap-3 pb-3 border border-transparent hover:border-primary/50 bg-white/5 p-4 rounded-xl transition-all hover:-translate-y-1">
<div class="relative w-full overflow-hidden rounded-lg">
  <div class="bg-center bg-no-repeat aspect-video bg-cover" style='background-image: url("<?= escape_html($product['image']); ?>");'></div>
  <a href="<?= escape_html($productUrl); ?>" class="absolute bottom-3 left-3 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/15 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-white backdrop-blur-md transition hover:border-primary/60 hover:bg-primary/30">
    <span class="material-symbols-outlined text-base">open_in_new</span>
    View
  </a>
</div>
<div>
<p class="text-white text-base font-medium leading-normal"><?= escape_html($product['title']); ?></p>
<p class="text-gray-400 text-sm font-normal leading-normal">by <?= escape_html($product['author']); ?></p>
<p class="text-white text-sm font-bold leading-normal mt-1">
<?php if ($currency === 'NGN'): ?>
₦<?= number_format((float) $product['price_ngn'], 0); ?>
<?php else: ?>
$<?= number_format((float) $product['price_usd'], 0); ?>
<?php endif; ?>
</p>
</div>
<div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.2em]">
  <a href="<?= escape_html($buyUrl); ?>" class="inline-flex flex-1 items-center justify-center gap-1 rounded-xl bg-primary px-3 py-2 text-white shadow shadow-primary/30">
    <span class="material-symbols-outlined text-base">shopping_bag</span>
    Buy
  </a>
  <a href="<?= escape_html($previewUrl); ?>" class="inline-flex flex-[0.5] items-center justify-center gap-1 rounded-xl border border-white/20 px-3 py-2 text-white hover:border-primary/50" target="<?= escape_html($previewTarget); ?>" rel="noopener">
    <span class="material-symbols-outlined text-base">visibility</span>
    Preview
  </a>
</div>
</div>
<?php endforeach; ?>
</div>
</section>
<!-- Latest Products Section -->
<section>
<div class="flex items-center justify-between px-4 pt-2">
<div>
<p class="text-primary text-xs uppercase tracking-[0.3em]">Latest drops</p>
<h2 class="text-white text-[22px] font-bold leading-tight tracking-[-0.015em]">New from the admin desk</h2>
</div>
<a class="inline-flex items-center gap-2 rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-white hover:border-primary/50 hover:text-primary" href="listing.php">
Browse all
<span class="material-symbols-outlined text-base">arrow_outward</span>
</a>
</div>
<div class="grid gap-5 p-4 md:grid-cols-2 xl:grid-cols-3">
<?php if (!$latestProducts): ?>
  <article class="rounded-2xl border border-dashed border-white/20 bg-white/5 p-6 text-center text-gray-400">
    No recent products yet. Check back soon!
  </article>
<?php else: ?>
  <?php foreach ($latestProducts as $product): ?>
    <?php $latestSlug = (string)($product['slug'] ?? '');
    $latestHref = $latestSlug !== '' ? site_url('product.php?slug=' . urlencode($latestSlug)) : site_url('listing.php'); ?>
    <article class="flex flex-col gap-4 rounded-2xl border border-white/10 bg-white/5 p-5 transition hover:border-primary/40 hover:-translate-y-1">
      <div class="flex items-center gap-4">
        <img src="<?= escape_html($product['image'] ?? $product['preview_image'] ?? ''); ?>" alt="<?= escape_html($product['title']); ?> preview" class="h-20 w-20 rounded-xl object-cover"/>
        <div>
          <p class="text-xs uppercase tracking-[0.2em] text-gray-400">New arrival</p>
          <h3 class="text-lg font-semibold text-white"><?= escape_html($product['title']); ?></h3>
          <p class="text-sm text-gray-400">by <?= escape_html($product['author'] ?? $product['author_name'] ?? 'Admin'); ?></p>
        </div>
      </div>
      <div class="flex items-center justify-between text-sm text-white">
        <div>
          <?php if ($currency === 'NGN'): ?>
            <p class="text-base font-semibold">₦<?= number_format((float)($product['price_ngn'] ?? 0), 0); ?></p>
            <p class="text-xs text-gray-400">$<?= number_format((float)($product['price_usd'] ?? 0), 2); ?></p>
          <?php else: ?>
            <p class="text-base font-semibold">$<?= number_format((float)($product['price_usd'] ?? 0), 2); ?></p>
            <p class="text-xs text-gray-400">₦<?= number_format((float)($product['price_ngn'] ?? 0), 0); ?></p>
          <?php endif; ?>
        </div>
        <a href="<?= escape_html($latestHref); ?>" class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white hover:border-primary/60">
          View
          <span class="material-symbols-outlined text-base">arrow_forward</span>
        </a>
      </div>
    </article>
  <?php endforeach; ?>
<?php endif; ?>
</div>
</section>
<!-- Browse by Category Section -->
<section>
<div class="flex flex-wrap items-center justify-between gap-3 px-4 pt-5">
  <div>
    <p class="text-primary text-xs uppercase tracking-[0.3em]">Curated Tracks</p>
    <h2 class="text-white text-[22px] font-bold leading-tight tracking-[-0.015em]">Browse by Category</h2>
    <p class="text-gray-400 text-sm">Synced with the admin catalogue so the homepage always stays current.</p>
  </div>
  <a href="<?= escape_html(site_url('listing.php')); ?>" class="inline-flex items-center gap-2 rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-white hover:border-primary/60">
    View marketplace
    <span class="material-symbols-outlined text-base">arrow_outward</span>
  </a>
</div>
<div class="p-4">
  <?php if (!$homeCategories): ?>
    <div class="rounded-2xl border border-dashed border-white/20 bg-white/5 p-6 text-center text-sm text-gray-400">
      No categories have been configured yet.
    </div>
  <?php else: ?>
    <?php
      $categoryIconMap = [
        'ui kit' => 'dashboard_customize',
        'commerce' => 'shopping_bag',
        'source code' => 'code',
        'plugin' => 'extension',
        'utility' => 'developer_mode',
        'automation' => 'auto_awesome',
      ];
      $accentPalette = ['from-primary/90 to-primary/60', 'from-emerald-500/80 to-emerald-400/60', 'from-purple-500/80 to-pink-500/60'];
      $categoriesToRender = $homeCategories;
      $highlightCategory = array_shift($categoriesToRender);
      $highlightSlug = (string)($highlightCategory['slug'] ?? '');
      $highlightHref = $highlightSlug !== '' ? site_url('listing.php?category=' . urlencode($highlightSlug)) : site_url('listing.php');
      $highlightType = $highlightCategory['type'] ?? 'Featured';
      $highlightIconKey = strtolower($highlightType);
      $highlightIcon = $categoryIconMap[$highlightIconKey] ?? 'category';
    ?>
    <div class="grid gap-4 lg:grid-cols-12">
      <a href="<?= escape_html($highlightHref); ?>" class="relative flex flex-col justify-between gap-6 overflow-hidden rounded-2xl border border-white/15 bg-gradient-to-br <?= escape_html($accentPalette[0]); ?> p-6 text-white shadow-lg shadow-primary/20 lg:col-span-5">
        <span class="material-symbols-outlined text-4xl"><?= escape_html($highlightIcon); ?></span>
        <div>
          <p class="text-xs uppercase tracking-[0.3em] text-white/70">Featured Track</p>
          <h3 class="text-2xl font-semibold leading-snug"><?= escape_html($highlightCategory['label'] ?? 'Category'); ?></h3>
          <p class="mt-2 text-sm text-white/80"><?= escape_html($highlightCategory['description'] ?? 'Discover curated assets in this lane.'); ?></p>
        </div>
        <div class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-[0.3em]">
          Explore collection
          <span class="material-symbols-outlined text-base">trending_flat</span>
        </div>
        <div class="pointer-events-none absolute inset-0 rounded-2xl border border-white/20"></div>
      </a>
      <div class="grid gap-4 sm:grid-cols-2 lg:col-span-7">
        <?php foreach ($categoriesToRender as $index => $category): ?>
          <?php
            $categorySlug = (string)($category['slug'] ?? '');
            $categoryHref = $categorySlug !== '' ? site_url('listing.php?category=' . urlencode($categorySlug)) : site_url('listing.php');
            $categoryLabel = $category['label'] ?? 'Category';
            $categoryType = $category['type'] ?? 'Asset';
            $categoryDescription = $category['description'] ?? 'Discover curated assets in this lane.';
            $iconKey = strtolower($categoryType);
            $categoryIcon = $categoryIconMap[$iconKey] ?? 'category';
            $accentClass = $accentPalette[($index + 1) % count($accentPalette)];
          ?>
          <a href="<?= escape_html($categoryHref); ?>" class="group relative flex flex-col gap-4 overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 transition hover:border-primary/50 hover:bg-primary/5">
            <div class="flex items-center justify-between">
              <span class="material-symbols-outlined text-2xl text-primary group-hover:text-white transition"><?= escape_html($categoryIcon); ?></span>
              <span class="inline-flex items-center rounded-full border border-white/15 px-3 py-1 text-[11px] uppercase tracking-[0.3em] text-gray-300"><?= escape_html(strtoupper(substr($categoryType, 0, 10))); ?></span>
            </div>
            <div>
              <p class="text-lg font-semibold text-white"><?= escape_html($categoryLabel); ?></p>
              <p class="text-sm text-gray-400"><?= escape_html($categoryDescription); ?></p>
            </div>
            <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.3em] text-gray-300 group-hover:text-primary">
              Explore assets
              <span class="material-symbols-outlined text-base">arrow_outward</span>
            </span>
            <div class="pointer-events-none absolute inset-0 hidden rounded-2xl bg-gradient-to-br <?= escape_html($accentClass); ?> opacity-0 transition group-hover:opacity-10 sm:block"></div>
          </a>
        <?php endforeach; ?>
        <?php if (!$categoriesToRender): ?>
          <a href="<?= escape_html(site_url('admin/categories.php')); ?>" class="flex flex-col gap-3 rounded-2xl border border-dashed border-white/20 bg-white/5 p-5 text-center text-gray-400 hover:border-primary/40 hover:text-white">
            <span class="material-symbols-outlined text-3xl text-primary">add_circle</span>
            <p class="text-sm">Add more categories from the admin dashboard to fill this grid.</p>
          </a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>
</section>
<!-- Why Choose Us Section -->
<section id="why-choose">
<h2 class="text-white text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5 text-center">Why Choose Scriptloaded?</h2>
<div class="grid md:grid-cols-3 gap-8 p-4 mt-4">
<div class="flex flex-col items-center text-center p-6 bg-white/5 rounded-xl">
<span class="material-symbols-outlined text-primary text-5xl mb-4">verified_user</span>
<h3 class="text-lg font-bold mb-2">Quality Code</h3>
<p class="text-gray-400 text-sm">Every item is reviewed by our team to ensure it meets our high quality standards.</p>
</div>
<div class="flex flex-col items-center text-center p-6 bg-white/5 rounded-xl">
<span class="material-symbols-outlined text-primary text-5xl mb-4">credit_card</span>
<h3 class="text-lg font-bold mb-2">Secure Payments</h3>
<p class="text-gray-400 text-sm">We use industry-standard encryption to protect your payment information.</p>
</div>
<div class="flex flex-col items-center text-center p-6 bg-white/5 rounded-xl">
<span class="material-symbols-outlined text-primary text-5xl mb-4">support_agent</span>
<h3 class="text-lg font-bold mb-2">24/7 Support</h3>
<p class="text-gray-400 text-sm">Our dedicated support team is here to help you around the clock.</p>
</div>
</div>
</section>
</main>
<!-- Footer -->
<?php include __DIR__ . '/partials/public_footer.php'; ?>
</div>
</div>
</div>
</div>
<script src="assets/js/mobile-menu.js"></script>
</body></html>
