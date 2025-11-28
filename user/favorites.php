<?php
require __DIR__ . '/_bootstrap.php';

$favoriteNotice = null;
$favoriteNoticeTone = 'info';
$productToPin = trim((string)($_GET['add'] ?? ''));
if ($productToPin !== '') {
  $productRecord = fetch_product_by_slug($pdo, $productToPin);
  if ($productRecord) {
    try {
      $stmt = $pdo->prepare('INSERT IGNORE INTO favorites (user_id, product_id) VALUES (:user_id, :product_id)');
      $stmt->execute([
        'user_id' => $userProfile['id'],
        'product_id' => $productRecord['id'],
      ]);
      if ($stmt->rowCount() > 0) {
        $favoriteNoticeTone = 'success';
        $favoriteNotice = sprintf('%s was added to your cart.', $productRecord['title']);
      } else {
        $favoriteNoticeTone = 'info';
        $favoriteNotice = sprintf('%s is already in your cart.', $productRecord['title']);
      }
    } catch (\PDOException $exception) {
      $favoriteNoticeTone = 'error';
      $favoriteNotice = 'We could not update your cart. Please try again.';
    }
  } else {
    $favoriteNoticeTone = 'error';
    $favoriteNotice = 'That product is no longer available.';
  }
}

$favoritesRows = fetch_favorites_for_user($pdo, $userProfile['id']);
$favorites = array_map(static function (array $row) {
  return [
    'title' => $row['title'],
    'category' => $row['category'],
    'rating' => $row['rating'],
    'price_usd' => $row['price_usd'],
    'image' => $row['image'],
    'tags' => $row['tags'] ?: [],
  ];
}, $favoritesRows);

$pageTitle = 'Favorite Items';
$activeNav = 'favorites';

ob_start();
?>
<section class="flex flex-wrap items-center justify-between gap-4">
  <div>
    <p class="text-xs uppercase tracking-[0.3em] text-primary">Collections</p>
    <h2 class="text-4xl font-black text-white">Favorite Items</h2>
    <p class="mt-1 text-sm text-gray-400">Pinned products you monitor for updates and price drops.</p>
  </div>
  <a href="listing.php" class="inline-flex items-center gap-2 rounded-xl border border-white/15 px-4 py-2 text-sm font-semibold text-white">
    Browse marketplace
    <span class="material-symbols-outlined text-base">arrow_outward</span>
  </a>
</section>
<?php if ($favoriteNotice): ?>
  <div class="mt-6 rounded-2xl border px-4 py-3 text-sm font-medium <?php if ($favoriteNoticeTone === 'success'): ?>border-emerald-500/40 bg-emerald-500/10 text-emerald-200<?php elseif ($favoriteNoticeTone === 'error'): ?>border-red-500/40 bg-red-500/10 text-red-200<?php else: ?>border-cyan-500/30 bg-cyan-500/10 text-cyan-100<?php endif; ?>">
    <?= escape_html($favoriteNotice); ?>
  </div>
<?php endif; ?>
<section class="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
  <?php foreach ($favorites as $favorite): ?>
    <article class="rounded-2xl border border-white/10 bg-white/5 p-5 shadow-lg shadow-primary/10">
      <img src="<?= escape_html($favorite['image']); ?>" alt="<?= escape_html($favorite['title']); ?> preview" class="h-44 w-full rounded-xl object-cover"/>
      <div class="mt-4 flex items-center justify-between text-xs uppercase tracking-[0.3em] text-gray-400">
        <span><?= escape_html($favorite['category']); ?></span>
        <span class="inline-flex items-center gap-1 text-primary">
          <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">star</span>
          <?= number_format((float) $favorite['rating'], 1); ?>
        </span>
      </div>
      <h3 class="mt-2 text-2xl font-semibold text-white"><?= escape_html($favorite['title']); ?></h3>
      <p class="mt-1 text-lg font-bold text-white">$<?= number_format((float) $favorite['price_usd'], 2); ?></p>
      <div class="mt-3 flex flex-wrap gap-2">
        <?php foreach ($favorite['tags'] as $tag): ?>
          <span class="rounded-full border border-white/15 px-3 py-1 text-xs text-gray-300"><?= escape_html($tag); ?></span>
        <?php endforeach; ?>
      </div>
      <div class="mt-5 flex items-center gap-3">
        <a href="product.php" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white">
          View details
        </a>
        <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-white">
          <span class="material-symbols-outlined">favorite</span>
        </button>
      </div>
    </article>
  <?php endforeach; ?>
</section>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/../templates/user/dashboard_layout.php';
