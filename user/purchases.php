<?php
require __DIR__ . '/_bootstrap.php';

$currency = scriptloaded_current_currency();
$orders = fetch_orders_for_user($pdo, $userProfile['id'], null);
$purchases = array_map(static function (array $order) {
  return [
    'title' => $order['title'],
    'category' => $order['category'],
    'seller' => $order['seller'],
    'purchased_at' => date('M d, Y', strtotime($order['completed_at'] ?? $order['created_at'])),
    'price_usd' => $order['price_usd'],
    'price_ngn' => $order['price_ngn'],
    'image' => $order['image'],
    'download_url' => $order['download_token'] ? 'download.php?token=' . urlencode($order['download_token']) : 'downloads.php',
    'details_url' => 'purchases.php?order=' . urlencode((string)$order['id']),
  ];
}, $orders);

$pageTitle = 'Purchased Items';
$activeNav = 'purchases';

ob_start();
?>
<section class="flex flex-wrap items-center justify-between gap-4">
  <div>
    <p class="text-xs uppercase tracking-[0.3em] text-primary">Library</p>
    <h2 class="text-4xl font-black text-white">Purchased Items</h2>
    <p class="mt-1 text-sm text-gray-400">Lifetime access to everything you've unlocked.</p>
  </div>
  <form method="get" class="flex items-center gap-3 rounded-2xl border border-white/15 bg-white/5 px-4 py-2 text-sm text-white">
    <label for="currency" class="text-gray-400">Currency</label>
    <select id="currency" name="currency" class="bg-transparent" onchange="this.form.submit()">
      <option value="USD" <?= $currency === 'USD' ? 'selected' : ''; ?>>USD</option>
      <option value="NGN" <?= $currency === 'NGN' ? 'selected' : ''; ?>>NGN</option>
    </select>
  </form>
</section>
<section class="mt-6 flex gap-3 overflow-x-auto pb-2 text-sm text-white">
  <button class="inline-flex items-center gap-2 rounded-full border border-white/15 px-4 py-2 text-xs uppercase tracking-[0.2em]">All</button>
  <button class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-xs text-gray-400">Themes</button>
  <button class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-xs text-gray-400">Plugins</button>
  <button class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-xs text-gray-400">Apps</button>
</section>
<section class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
  <?php foreach ($purchases as $purchase): ?>
    <article class="group rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg shadow-primary/10 transition hover:border-primary/40">
      <div class="flex items-start justify-between gap-4">
        <div>
          <p class="text-xs text-gray-400"><?= escape_html($purchase['category']); ?></p>
          <h3 class="text-xl font-semibold text-white">
            <?= escape_html($purchase['title']); ?>
          </h3>
          <p class="text-xs text-gray-500">by <?= escape_html($purchase['seller']); ?> • <?= escape_html($purchase['purchased_at']); ?></p>
          <p class="mt-3 text-base font-semibold text-white">
            <?php if ($currency === 'USD'): ?>
              $<?= number_format((float) $purchase['price_usd'], 2); ?>
              <span class="text-xs text-gray-400"> / ₦<?= number_format((float) $purchase['price_ngn'], 0); ?></span>
            <?php else: ?>
              ₦<?= number_format((float) $purchase['price_ngn'], 0); ?>
              <span class="text-xs text-gray-400"> / $<?= number_format((float) $purchase['price_usd'], 2); ?></span>
            <?php endif; ?>
          </p>
          <div class="mt-4 flex flex-wrap gap-2">
            <a href="<?= escape_html($purchase['download_url']); ?>" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-[0_4px_20px_rgba(26,115,232,0.35)]">
              <span class="material-symbols-outlined text-base">download</span>
              Download
            </a>
            <a href="<?= escape_html($purchase['details_url']); ?>" class="inline-flex items-center gap-2 rounded-lg border border-white/15 px-4 py-2 text-sm font-semibold text-white/80">
              Details
            </a>
          </div>
        </div>
        <img src="<?= escape_html($purchase['image']); ?>" alt="<?= escape_html($purchase['title']); ?> preview" class="h-24 w-24 rounded-xl object-cover"/>
      </div>
    </article>
  <?php endforeach; ?>
</section>
<section class="mt-10 flex items-center justify-center gap-2 text-sm text-white">
  <button class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-gray-400">
    <span class="material-symbols-outlined">chevron_left</span>
  </button>
  <span class="rounded-xl bg-primary px-4 py-2 text-white">1</span>
  <button class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-gray-400">
    <span class="material-symbols-outlined">chevron_right</span>
  </button>
</section>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/../templates/user/dashboard_layout.php';
