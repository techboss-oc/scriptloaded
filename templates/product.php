<?php
/** @var array $product */
/** @var array $relatedProducts */
/** @var string $currency */
/** @var string $slug */

$heroImage = $product['preview_image'] ?? ($product['gallery'][0] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuCKgvZ07FdFHdW4gZhqwE_5lTNZp6YRH7JQMqXGXZUbyloQoIf9O3wsC87r7fFzc2N9JrqqRVbVqYwVt669MY7FW8BiL68ZbU_kPEbW-6FWJLlWVrGFcdQlBiRSjAHhx53JW_va5cZZUWB1WMN2Hdqsr4-JGoSgSZd2edSgjmJzrw4topToZp8CgkT1bG_3ZSFoxaiEjnpdK_BgWQ2q2ymP5mimv9NNAXR_DxSes-oSq6opSbDRSejwnbBy5rvvX7x7F4C5xZL4afrW');
$category = $product['category'] ?? 'Marketplace';
$descriptionPoints = [];
if (!empty($product['description_points']) && is_array($product['description_points'])) {
  $descriptionPoints = array_values(array_filter($product['description_points'], static function ($point) {
    return is_string($point) && trim($point) !== '';
  }));
}
$rawFeatures = $product['features'] ?? $product['feature_highlights'] ?? [];
if (is_string($rawFeatures)) {
  $rawFeatures = preg_split('/\r?\n/', $rawFeatures) ?: [];
}
$features = [];
if (is_array($rawFeatures)) {
  $features = array_values(array_filter(array_map(static function ($feature) {
    return is_string($feature) ? trim($feature) : '';
  }, $rawFeatures), static function ($feature) {
    return $feature !== '';
  }));
}
$changelog = [];
if (!empty($product['changelog']) && is_array($product['changelog'])) {
  $changelog = $product['changelog'];
}
$authorAvatar = $product['author_avatar'] ?? 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80';
$authorRating = $product['rating'] ?? 5.0;
$authorReviews = $product['reviews_count'] ?? 0;
$livePreviewUrl = $product['live_preview_url'] ?? null;
$combinedVideoSrc = youtube_embed_url(
  $product['youtube_overview']
  ?? $product['youtube_install']
  ?? $product['overview_video']
  ?? $product['install_video']
  ?? null
);

if ($currency === 'USD') {
    $primaryPrice = '$' . number_format($product['price_usd'], 2);
    $secondaryPrice = '₦' . number_format($product['price_ngn'], 0);
} else {
    $primaryPrice = '₦' . number_format($product['price_ngn'], 0);
    $secondaryPrice = '$' . number_format($product['price_usd'], 2);
}

$currentProductLink = 'product?slug=' . urlencode($slug ?? 'ecommerce-website-script');
$currentVisitor = function_exists('current_user') ? current_user() : null;
$isLoggedIn = (bool)$currentVisitor;
$isAdminVisitor = $isLoggedIn && !empty($currentVisitor['is_admin']);
$dashboardHref = $isAdminVisitor ? 'admin/index' : 'user/dashboard';
$authNavLabel = $isLoggedIn ? 'Dashboard' : 'Login';
$authNavIcon = $isLoggedIn ? 'space_dashboard' : 'login';
$authNavHref = $isLoggedIn ? $dashboardHref : 'user/login';
$primaryCtaLabel = $isLoggedIn ? 'View Dashboard' : 'Register';
$primaryCtaHref = $isLoggedIn ? $dashboardHref : 'user/register';

$primaryNavLinks = [
  ['label' => 'Home', 'href' => 'index'],
  ['label' => 'Marketplace', 'href' => 'listing'],
  ['label' => 'Product Details', 'href' => $currentProductLink],
  ['label' => 'About', 'href' => 'about'],
  ['label' => 'Contact', 'href' => 'contact'],
];
if ($isLoggedIn) {
  $primaryNavLinks[] = ['label' => $authNavLabel, 'href' => $authNavHref];
}
$mobileNavLinks = [
  ['label' => 'Home', 'href' => 'index', 'icon' => 'home'],
  ['label' => 'Marketplace', 'href' => 'listing', 'icon' => 'storefront'],
  ['label' => 'Product Details', 'href' => $currentProductLink, 'icon' => 'inventory_2'],
  ['label' => 'About', 'href' => 'about', 'icon' => 'info'],
  ['label' => 'Contact', 'href' => 'contact', 'icon' => 'call'],
];
$footerLinkGroups = get_public_footer_link_groups([
  'isLoggedIn' => $isLoggedIn,
  'authNavLabel' => $authNavLabel,
  'dashboardHref' => $isLoggedIn ? $dashboardHref : null,
]);

$productSlugValue = (string)($product['slug'] ?? ($slug ?? ''));
$encodedProductSlug = urlencode($productSlugValue);
$buyIntentPath = 'checkout?slug=' . $encodedProductSlug . '&currency=' . urlencode($currency);
$favoritesTargetPath = 'user/favorites?add=' . $encodedProductSlug;
$adminProductHref = isset($product['id']) ? 'admin/product_edit?id=' . urlencode((string)$product['id']) : 'admin/index';
if ($isAdminVisitor) {
  $buyNowHref = $adminProductHref;
  $addToCartHref = $adminProductHref;
} else {
  $buyNowHref = $isLoggedIn
    ? $buyIntentPath
    : 'user/login?redirect=' . rawurlencode($buyIntentPath);
  $addToCartHref = $isLoggedIn
    ? $favoritesTargetPath
    : 'user/login?redirect=' . rawurlencode($favoritesTargetPath);
}
$viewProfileHref = 'user/profile';
$detailNavLinks = [
  ['label' => 'Description', 'href' => '#description'],
  ['label' => 'Reviews', 'href' => '#reviews'],
];
?>
<!DOCTYPE html>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script>
tailwind.config = {
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
      }
    }
  }
};
</script>
<style>
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
.glassmorphic-panel {
  background-color: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.12);
}
.dark .glassmorphic-panel {
  background-color: rgba(36, 51, 71, 0.45);
}
.neomorphic-button {
  background: #111821;
  box-shadow: 6px 6px 12px #0c1017, -6px -6px 12px #16202b;
}
.neomorphic-button:active {
  box-shadow: inset 6px 6px 12px #0c1017, inset -6px -6px 12px #16202b;
}
</style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-gray-800 dark:text-gray-200">
<div class="relative flex min-h-screen flex-col">
  <?php include __DIR__ . '/partials/public_header.php'; ?>

  <main class="flex-1 px-4 py-10 sm:px-8 lg:px-16">
    <div class="mx-auto max-w-7xl">
      <div class="flex flex-wrap gap-2 text-sm text-gray-500 dark:text-gray-400">
        <a class="hover:text-primary" href="<?= escape_html(site_url('index')); ?>">Home</a>
        <span>/</span>
        <a class="hover:text-primary" href="<?= escape_html(site_url('listing')); ?>"><?= escape_html($category); ?></a>
        <span>/</span>
        <span class="text-gray-900 dark:text-white"><?= escape_html($product['title']); ?></span>
      </div>

      <div class="mt-6 grid gap-8 lg:grid-cols-3">
        <section class="space-y-8 lg:col-span-2">
          <div class="relative min-h-[380px] overflow-hidden rounded-2xl bg-cover bg-center" style="background-image:linear-gradient(0deg, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0) 60%), url('<?= escape_html($heroImage); ?>');">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
            <div class="relative z-10 m-6 rounded-2xl bg-white/5 p-6 backdrop-blur-md">
              <h2 class="text-3xl font-bold text-white"><?= escape_html($product['title']); ?></h2>
              <p class="mt-2 text-sm text-gray-200"><?= escape_html($product['short_description']); ?></p>
              <div class="mt-4 flex flex-wrap gap-3">
                <?php if ($livePreviewUrl): ?>
                  <a class="inline-flex items-center justify-center rounded-xl border border-white/40 px-4 py-2 text-sm font-semibold text-white/90 hover:bg-white/10" href="<?= escape_html($livePreviewUrl); ?>" target="_blank" rel="noreferrer">
                    Live Preview
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="grid gap-6">
            <div class="glassmorphic-panel rounded-2xl p-4">
              <h3 class="mb-3 text-lg font-bold">Installation &amp; Overview Video</h3>
              <div class="aspect-video overflow-hidden rounded-xl bg-black">
                <?php if ($combinedVideoSrc): ?>
                  <iframe class="h-full w-full" src="<?= escape_html($combinedVideoSrc); ?>" allowfullscreen title="Installation &amp; overview video"></iframe>
                <?php else: ?>
                  <div class="flex h-full items-center justify-center text-center text-sm text-gray-400">Add a single walkthrough video to showcase the product.</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </section>

        <aside class="space-y-6">
          <div class="glassmorphic-panel rounded-2xl p-6">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?= $primaryPrice; ?> <span class="text-base font-medium text-gray-500 dark:text-gray-300">/ <?= $secondaryPrice; ?></span></h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">One-time payment · Lifetime updates</p>
            <div class="mt-6 flex flex-col gap-3">
		      <a class="flex h-12 items-center justify-center rounded-xl bg-primary text-base font-semibold text-white shadow-lg shadow-primary/30 transition hover:bg-primary/90" href="<?= escape_html(site_url($buyNowHref)); ?>">Buy Now</a>
		      <a class="flex h-12 items-center justify-center rounded-xl text-base font-semibold text-gray-900 dark:text-white neomorphic-button" href="<?= escape_html(site_url($addToCartHref)); ?>">Add to Cart</a>
              <?php if ($livePreviewUrl): ?>
                <a class="flex h-12 items-center justify-center rounded-xl border border-cyan-400/60 bg-cyan-500/10 text-base font-semibold text-cyan-300 hover:bg-cyan-500/20" href="<?= escape_html($livePreviewUrl); ?>" target="_blank" rel="noreferrer">Live Preview</a>
              <?php endif; ?>
            </div>
            <form method="get" class="mt-4 flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white">
              <input type="hidden" name="slug" value="<?= escape_html($slug); ?>" />
              <span>Display prices in</span>
              <select name="currency" class="rounded-lg border border-white/20 bg-white/10 px-3 py-1" onchange="this.form.submit()">
                <option value="USD" <?= $currency === 'USD' ? 'selected' : ''; ?>>USD</option>
                <option value="NGN" <?= $currency === 'NGN' ? 'selected' : ''; ?>>NGN</option>
              </select>
            </form>
          </div>
          <div class="glassmorphic-panel flex items-center gap-4 rounded-2xl p-6">
            <img class="size-14 rounded-full object-cover" src="<?= escape_html($authorAvatar); ?>" alt="<?= escape_html($product['author_name'] ?? 'Scriptloaded Author'); ?>" />
            <div>
              <p class="text-lg font-bold text-gray-900 dark:text-white"><?= escape_html($product['author_name'] ?? 'Scriptloaded Author'); ?></p>
              <div class="flex items-center gap-1 text-sm text-gray-500 dark:text-gray-300">
                <span><?= number_format((float) $authorRating, 1); ?></span>
                <span class="material-symbols-outlined !text-base text-yellow-400" style="font-variation-settings:'FILL' 1;">star</span>
                <span>(<?= number_format((float) $authorReviews); ?> reviews)</span>
              </div>
              <a class="text-sm font-medium text-primary hover:underline" href="<?= escape_html(site_url($viewProfileHref)); ?>">View Profile</a>
            </div>
          </div>
        </aside>
      </div>

      <section class="mt-12" id="details">
        <div class="border-b border-gray-200 dark:border-gray-700">
          <nav class="-mb-px flex gap-6 text-base font-semibold" aria-label="Product details">
            <?php foreach ($detailNavLinks as $index => $detailNav): ?>
              <a class="<?= $index === 0 ? 'border-b-2 border-primary pb-4 text-primary' : 'border-b-2 border-transparent pb-4 text-gray-500 hover:text-primary'; ?>" href="<?= escape_html($detailNav['href']); ?>">
                <?= escape_html($detailNav['label']); ?>
              </a>
            <?php endforeach; ?>
          </nav>
        </div>
        <div class="prose prose-lg max-w-none py-8 dark:prose-invert" id="description">
          <?php foreach ($descriptionPoints as $paragraph): ?>
            <p><?= escape_html($paragraph); ?></p>
          <?php endforeach; ?>
          <?php if ($features): ?>
            <h3 class="mt-6 text-2xl font-semibold text-gray-900 dark:text-white">Feature Highlights</h3>
            <ul class="mt-3 list-disc space-y-2 pl-6 text-base text-gray-700 dark:text-gray-200">
              <?php foreach ($features as $feature): ?>
                <li><?= escape_html($feature); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
        <div id="reviews" class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-6 text-sm dark:bg-white/5">
          <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
              <p class="text-xs uppercase tracking-[0.3em] text-primary">Ratings</p>
              <p class="text-4xl font-bold text-gray-900 dark:text-white"><?= number_format((float)$authorRating, 1); ?></p>
              <p class="text-gray-500 dark:text-gray-300"><?= number_format((float)$authorReviews); ?> reviews from verified buyers</p>
            </div>
            <div class="flex flex-wrap gap-3">
              <a class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-primary/30" href="<?= escape_html(site_url('user/support')); ?>">
                Contact support
              </a>
              <?php if (!$isLoggedIn): ?>
                <a class="inline-flex items-center justify-center rounded-xl border border-white/20 px-4 py-2 text-sm font-semibold text-gray-900 dark:text-white" href="<?= escape_html(site_url('user/login?redirect=' . rawurlencode('user/support'))); ?>">
                  Login to review
                </a>
              <?php endif; ?>
            </div>
          </div>
          <p class="mt-4 text-gray-500 dark:text-gray-300">Buyer feedback and patch notes sync automatically after each release. Reach out if you need clarification on the latest update before purchasing.</p>
        </div>
        <?php if ($changelog): ?>
          <div id="changelog" class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-6 dark:bg-white/5">
            <h3 class="mb-4 text-xl font-bold">Latest Updates</h3>
            <div class="space-y-4">
              <?php foreach ($changelog as $entry): ?>
                <div class="rounded-xl bg-white/5 p-4 dark:bg-black/20">
                  <div class="flex flex-wrap items-center justify-between gap-2 text-sm text-gray-500 dark:text-gray-300">
                    <span class="font-semibold text-gray-900 dark:text-white">Version <?= escape_html($entry['version']); ?></span>
                    <span><?= escape_html($entry['date']); ?></span>
                  </div>
                  <ul class="mt-2 list-disc pl-5 text-sm text-gray-700 dark:text-gray-200">
                    <?php foreach ($entry['items'] as $item): ?>
                      <li><?= escape_html($item); ?></li>
                    <?php endforeach; ?>
                  </ul>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </section>

      <section class="mt-12">
        <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Related Products</h2>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <?php foreach ($relatedProducts as $r): ?>
            <a href="product?slug=<?= urlencode($r['slug']); ?>" class="glassmorphic-panel group rounded-2xl overflow-hidden">
              <div class="aspect-video bg-cover bg-center" style="background-image:url('<?= escape_html($r['image']); ?>')"></div>
              <div class="p-4">
                <h3 class="font-semibold text-gray-900 transition-colors group-hover:text-primary dark:text-white"><?= escape_html($r['title']); ?></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">by <?= escape_html($r['author']); ?></p>
                <p class="mt-2 font-bold text-gray-900 dark:text-white">
                  <?php if ($currency === 'USD'): ?>
                    $<?= number_format($r['price_usd'], 2); ?>
                  <?php else: ?>
                    ₦<?= number_format($r['price_ngn'], 0); ?>
                  <?php endif; ?>
                </p>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    </div>
  </main>
  <?php include __DIR__ . '/partials/public_footer.php'; ?>
</div>
<script src="assets/js/mobile-menu.js"></script>
</body></html>
