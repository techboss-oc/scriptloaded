<?php
/** @var array $products */
/** @var string $currency */
if (!function_exists('render_product_rating_icons')) {
  function render_product_rating_icons(float $rating): string {
    $output = '';
    for ($i = 1; $i <= 5; $i++) {
      if ($rating >= $i) {
        $output .= '<span class="material-symbols-outlined text-yellow-500 text-base" style="font-variation-settings: \"FILL\" 1;">star</span>';
      } elseif ($rating >= $i - 0.5) {
        $output .= '<span class="material-symbols-outlined text-yellow-500 text-base">star_half</span>';
      } else {
        $output .= '<span class="material-symbols-outlined text-gray-400 text-base">star</span>';
      }
    }
    return $output;
  }
}

$currentVisitor = function_exists('current_user') ? current_user() : null;
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

$availableCategories = $availableCategories ?? [];
$filters = array_merge([
  'category' => '',
  'price_min' => 0,
  'price_max' => 0,
  'rating' => null,
  'rating_choice' => 'any',
  'sort' => 'newest',
], $filters ?? []);
$priceBounds = $priceBounds ?? ['min' => 0, 'max' => 0];
$activeFilters = $activeFilters ?? 0;
$priceSymbol = $priceSymbol ?? ($currency === 'NGN' ? '₦' : '$');
$filterBase = 'listing.php' . ($currency ? '?currency=' . urlencode($currency) : '');
$filterResetUrl = $filterResetUrl ?? site_url($filterBase);
$resultsCount = isset($products) ? count($products) : 0;
?>
<!DOCTYPE html>

<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Scriptloaded - Product Listing</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<style>
    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glassmorphism {
            background: rgba(255, 255, 255, 0.1);
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .dark .glassmorphism {
            background: rgba(41, 52, 71, 0.25);
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .neomorphism-light {
            background-color: #f0f2f5;
            box-shadow: 6px 6px 12px #d9dbde, -6px -6px 12px #ffffff;
        }
        .neomorphism-dark {
            background: #1a202c;
            box-shadow: 6px 6px 12px #141923, -6px -6px 12px #202735;
        }
    </style>
<script>
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
<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200">
<div class="relative min-h-screen w-full overflow-x-hidden">
  <div class="absolute inset-0">
    <div class="absolute top-0 left-0 w-1/2 h-1/2 bg-primary/10 blur-[160px]"></div>
    <div class="absolute bottom-0 right-0 w-1/2 h-1/2 bg-primary/5 blur-[200px]"></div>
  </div>
  <div class="relative flex min-h-screen flex-col">
    <?php include __DIR__ . '/partials/public_header.php'; ?>
    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 lg:px-10">
      <div class="grid gap-8 lg:grid-cols-4">
        <!-- Filters -->
        <aside class="h-fit rounded-2xl border border-white/10 bg-white/5 p-4 dark:bg-white/5 lg:sticky lg:top-28">
<button type="button" class="mb-4 flex w-full items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white shadow-sm shadow-black/20 transition hover:border-white/30 lg:hidden" data-filter-toggle aria-expanded="false">
<span class="flex items-center gap-2 text-sm font-semibold">
<span class="material-symbols-outlined text-base">tune</span>
Filters &amp; Sort
</span>
<span class="flex items-center gap-2 text-xs uppercase tracking-wide text-gray-300">
<?= $activeFilters > 0 ? escape_html($activeFilters . ' active') : 'Tap to open'; ?>
<span class="material-symbols-outlined text-base" data-filter-toggle-icon>expand_more</span>
</span>
</button>
<div class="hidden lg:block" data-filter-panel>
<form method="get" class="w-full rounded-xl glassmorphism space-y-6 p-6">
<h2 class="text-xl font-bold text-gray-900 dark:text-white">Filters</h2>
<div>
<label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2" for="filter-category">Category</label>
<select id="filter-category" name="category" class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-primary focus:outline-none">
<option value="">All categories</option>
<?php foreach ($availableCategories as $category): ?>
<option value="<?= escape_html($category); ?>" <?= $filters['category'] === $category ? 'selected' : ''; ?>><?= escape_html($category); ?></option>
<?php endforeach; ?>
</select>
</div>
<div>
<p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Price Range (<?= escape_html($priceSymbol); ?>)</p>
<div class="flex items-center gap-3">
<input id="price-min" name="price_min" type="number" min="<?= (int) $priceBounds['min']; ?>" max="<?= (int) $priceBounds['max']; ?>" value="<?= (int) $filters['price_min']; ?>" class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-primary focus:outline-none" placeholder="Min"/>
<span class="text-gray-400 text-sm">to</span>
<input id="price-max" name="price_max" type="number" min="<?= (int) $priceBounds['min']; ?>" max="<?= (int) $priceBounds['max']; ?>" value="<?= (int) $filters['price_max']; ?>" class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-primary focus:outline-none" placeholder="Max"/>
</div>
<p class="text-xs text-gray-400 mt-2">Available: <?= escape_html($priceSymbol); ?><?= number_format((float) $priceBounds['min']); ?> – <?= escape_html($priceSymbol); ?><?= number_format((float) $priceBounds['max']); ?></p>
</div>
<div>
<p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Rating</p>
<?php $ratingOptions = [
  ['value' => '4', 'label' => '4+ stars'],
  ['value' => '3', 'label' => '3+ stars'],
  ['value' => 'any', 'label' => 'Any rating'],
]; ?>
<div class="flex flex-col gap-2">
<?php foreach ($ratingOptions as $option): $isActive = $filters['rating_choice'] === $option['value']; ?>
<label class="flex cursor-pointer items-center justify-between rounded-lg border px-3 py-2 text-sm <?= $isActive ? 'border-primary/50 bg-primary/10 text-white' : 'border-white/10 text-gray-300 hover:bg-white/5'; ?>">
<span class="flex items-center gap-2">
<span class="material-symbols-outlined text-base">star</span>
<?= escape_html($option['label']); ?>
</span>
<input class="sr-only" type="radio" name="rating" value="<?= escape_html($option['value']); ?>" <?= $isActive ? 'checked' : ''; ?> />
</label>
<?php endforeach; ?>
</div>
</div>
<div>
<label class="block text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2" for="filter-sort">Sort By</label>
<select id="filter-sort" name="sort" class="w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-primary focus:outline-none">
<option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : ''; ?>>Newest</option>
<option value="price_low" <?= $filters['sort'] === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
<option value="price_high" <?= $filters['sort'] === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
<option value="rating_high" <?= $filters['sort'] === 'rating_high' ? 'selected' : ''; ?>>Top Rated</option>
</select>
</div>
<div>
<p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Currency</p>
<div class="flex h-10 items-center rounded-lg bg-white/5 p-1">
<label class="flex cursor-pointer h-full flex-1 items-center justify-center rounded-md px-2 text-sm font-medium <?= $currency === 'USD' ? 'bg-primary text-white' : 'text-gray-300'; ?>">
<span>USD</span>
<input <?= $currency === 'USD' ? 'checked' : ''; ?> class="sr-only" name="currency" type="radio" value="USD" onchange="this.form.submit()" />
</label>
<label class="flex cursor-pointer h-full flex-1 items-center justify-center rounded-md px-2 text-sm font-medium <?= $currency === 'NGN' ? 'bg-primary text-white' : 'text-gray-300'; ?>">
<span>NGN</span>
<input <?= $currency === 'NGN' ? 'checked' : ''; ?> class="sr-only" name="currency" type="radio" value="NGN" onchange="this.form.submit()" />
</label>
</div>
</div>
<div class="flex flex-col gap-3 pt-2">
<button type="submit" class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow shadow-primary/40">Apply Filters</button>
<a class="inline-flex items-center justify-center rounded-lg border border-white/15 px-4 py-2 text-sm font-semibold text-white hover:border-white/40" href="<?= escape_html($filterResetUrl); ?>">Clear Filters</a>
</div>
</form>
  </div>
  </aside>
  <!-- Main Content -->
  <section class="lg:col-span-3">
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
<h1 class="text-3xl font-bold text-gray-900 dark:text-white">Explore Scripts &amp; Themes</h1>
<p class="text-sm text-gray-500 dark:text-gray-400"><?= number_format($resultsCount); ?> results</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
<?php foreach ($products as $product): ?>
<?php
  $productSlugValue = (string)($product['slug'] ?? '');
  $encodedProductSlug = $productSlugValue !== '' ? urlencode($productSlugValue) : '';
  $productDetailHref = $encodedProductSlug ? 'product.php?slug=' . $encodedProductSlug : 'product.php';
  $buyIntentPath = $encodedProductSlug ? $productDetailHref . '&intent=buy' : 'listing.php';
  if ($isAdminVisitor) {
    $buyNowHref = isset($product['id']) ? 'admin/product_edit.php?id=' . urlencode((string)$product['id']) : 'admin/index.php';
  } else {
    $buyNowHref = $isLoggedIn
      ? 'user/purchases.php?highlight=' . $encodedProductSlug
      : 'user/login.php?redirect=' . rawurlencode($buyIntentPath);
  }
  $livePreviewUrl = $product['live_preview_url'] ?? '';
?>
<div class="group rounded-2xl border border-white/10 bg-white/10 p-4 transition-all duration-300 backdrop-blur-xl shadow-[0_10px_30px_rgba(0,0,0,0.35)] hover:border-primary/40 hover:bg-white/20 hover:shadow-[0_20px_45px_rgba(0,0,0,0.45)] hover:-translate-y-1 dark:bg-white/5">
<div class="relative mb-4">
<div class="aspect-[4/3] w-full rounded-lg bg-cover bg-center" data-alt="<?= escape_html($product['title']); ?> preview" style="background-image: url('<?= escape_html($product['image']); ?>')"></div>
<div class="absolute inset-0 bg-black/20 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
<a class="bg-primary text-white font-bold py-2 px-5 rounded-lg text-sm" href="<?= escape_html(site_url($productDetailHref)); ?>">View Details</a>
</div>
</div>
<div>
<h3 class="font-bold text-lg mb-1 truncate text-gray-900 dark:text-white"><?= escape_html($product['title']); ?></h3>
<p class="text-xs text-gray-500 dark:text-gray-400 mb-2">by <?= escape_html($product['author']); ?></p>
<div class="flex items-center gap-1 mb-3">
<?= render_product_rating_icons((float) $product['rating']); ?>
<span class="text-xs font-medium ml-1 text-gray-500">(<?= number_format((float) $product['rating'], 1); ?>)</span>
</div>
<p class="text-xl font-bold text-primary">
<?php if ($currency === 'NGN'): ?>
₦<?= number_format((float) $product['price_ngn'], 0); ?> <span class="text-sm font-normal text-gray-500 dark:text-gray-400">/ $<?= number_format((float) $product['price_usd'], 0); ?></span>
<?php else: ?>
$<?= number_format((float) $product['price_usd'], 0); ?> <span class="text-sm font-normal text-gray-500 dark:text-gray-400">/ ₦<?= number_format((float) $product['price_ngn'], 0); ?></span>
<?php endif; ?>
</p>
<div class="mt-4 flex flex-wrap gap-3">
<a class="inline-flex flex-1 min-w-[140px] items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white shadow shadow-primary/40 hover:bg-primary/80" href="<?= escape_html(site_url($buyNowHref)); ?>">Buy Now</a>
<?php if (!empty($livePreviewUrl)): ?>
<a class="inline-flex flex-1 min-w-[140px] items-center justify-center rounded-xl border border-white/20 px-4 py-2 text-sm font-semibold text-white hover:border-white/40" href="<?= escape_html($livePreviewUrl); ?>" target="_blank" rel="noreferrer">Live Preview</a>
<?php endif; ?>
</div>
</div>
</div>
<?php endforeach; ?>
<?php if (empty($products)): ?>
<div class="col-span-full rounded-2xl border border-dashed border-white/10 bg-white/5 dark:bg-black/20 p-8 text-center">
<p class="text-lg font-semibold text-white">No products match your filters yet.</p>
<p class="text-sm text-gray-400 mt-2">Try adjusting your price range, category, or rating to see more results.</p>
</div>
<?php else: ?>
<!-- Skeleton cards -->
<div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-xl shadow-[0_6px_20px_rgba(0,0,0,0.35)] animate-pulse dark:bg-white/5">
<div class="aspect-[4/3] w-full rounded-lg bg-gray-300 dark:bg-gray-700 mb-4"></div>
<div class="h-5 w-3/4 rounded bg-gray-300 dark:bg-gray-700 mb-2"></div>
<div class="h-3 w-1/2 rounded bg-gray-300 dark:bg-gray-700 mb-3"></div>
<div class="h-4 w-1/3 rounded bg-gray-300 dark:bg-gray-700 mb-4"></div>
<div class="h-6 w-1/4 rounded bg-gray-300 dark:bg-gray-700"></div>
</div>
<div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-xl shadow-[0_6px_20px_rgba(0,0,0,0.35)] animate-pulse dark:bg-white/5">
<div class="aspect-[4/3] w-full rounded-lg bg-gray-300 dark:bg-gray-700 mb-4"></div>
<div class="h-5 w-3/4 rounded bg-gray-300 dark:bg-gray-700 mb-2"></div>
<div class="h-3 w-1/2 rounded bg-gray-300 dark:bg-gray-700 mb-3"></div>
<div class="h-4 w-1/3 rounded bg-gray-300 dark:bg-gray-700 mb-4"></div>
<div class="h-6 w-1/4 rounded bg-gray-300 dark:bg-gray-700"></div>
</div>
<div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur-xl shadow-[0_6px_20px_rgba(0,0,0,0.35)] animate-pulse dark:bg-white/5">
<div class="aspect-[4/3] w-full rounded-lg bg-gray-300 dark:bg-gray-700 mb-4"></div>
<div class="h-5 w-3/4 rounded bg-gray-300 dark:bg-gray-700 mb-2"></div>
<div class="h-3 w-1/2 rounded bg-gray-300 dark:bg-gray-700 mb-3"></div>
<div class="h-4 w-1/3 rounded bg-gray-300 dark:bg-gray-700 mb-4"></div>
<div class="h-6 w-1/4 rounded bg-gray-300 dark:bg-gray-700"></div>
</div>
<?php endif; ?>
</div>
<!-- Pagination -->
<div class="flex items-center justify-center mt-12">
<nav class="flex items-center gap-2">
<a class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700" href="#">
<span class="material-symbols-outlined">chevron_left</span>
</a>
<a class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-white bg-primary font-medium" href="#">1</a>
<a class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700" href="#">2</a>
<a class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700" href="#">3</a>

<a class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700" href="#">10</a>
<a class="inline-flex items-center justify-center w-10 h-10 rounded-lg text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700" href="#">
<span class="material-symbols-outlined">chevron_right</span>
</a>
</nav>
</div>
  </section>
      </div>
    </main>
    <?php include __DIR__ . '/partials/public_footer.php'; ?>
  </div>
</div>
<script src="assets/js/mobile-menu.js"></script>
<script>
  (function () {
    const toggle = document.querySelector('[data-filter-toggle]');
    const panel = document.querySelector('[data-filter-panel]');
    if (!toggle || !panel) return;
    const icon = toggle.querySelector('[data-filter-toggle-icon]');
    toggle.addEventListener('click', function () {
      panel.classList.toggle('hidden');
      const isHidden = panel.classList.contains('hidden');
      toggle.setAttribute('aria-expanded', String(!isHidden));
      if (icon) {
        icon.textContent = isHidden ? 'expand_more' : 'expand_less';
      }
    });
  })();
</script>
</body></html>