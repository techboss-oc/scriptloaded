<?php
require __DIR__ . '/_bootstrap.php';

$overview = compile_dashboard_overview($pdo, $userProfile['id']);
$statCards = $overview['stats'];
$recentPurchases = array_map(static function (array $order) {
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
}, array_slice($overview['recent_purchases'], 0, 3));
$downloadsRaw = $overview['active_downloads'];
$activeDownloads = array_map(static function (array $download) {
  return [
    'title' => $download['title'],
    'category' => $download['category'],
    'download_url' => 'download.php?token=' . urlencode($download['token']),
  ];
}, $downloadsRaw);
$openTickets = array_map(static function (array $ticket) {
  return [
    'subject' => $ticket['subject'],
    'status' => ucwords(str_replace('_', ' ', $ticket['status'])),
    'updated_at' => date('M d, Y', strtotime($ticket['updated_at'] ?? $ticket['created_at'])),
    'priority' => ucfirst($ticket['priority']),
  ];
}, $overview['open_tickets']);

$pageTitle = 'Dashboard Overview';
$activeNav = 'dashboard';

ob_start();
?>
<section class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
  <?php foreach ($statCards as $card): ?>
    <article class="rounded-2xl border border-white/10 bg-white/5 p-5 text-white shadow-lg shadow-primary/5">
      <p class="text-xs uppercase tracking-[0.2em] text-gray-400"><?= escape_html($card['label']); ?></p>
      <p class="mt-3 text-3xl font-bold text-white"><?= escape_html($card['value']); ?></p>
      <?php if (!empty($card['value_ngn'])): ?>
        <p class="mt-1 text-base font-semibold text-primary"><?= escape_html($card['value_ngn']); ?></p>
      <?php endif; ?>
      <p class="mt-1 text-sm text-gray-400"><?= escape_html($card['subtext']); ?></p>
    </article>
  <?php endforeach; ?>
</section>
<section class="mt-10 grid gap-8 lg:grid-cols-3">
  <div class="lg:col-span-2 rounded-2xl border border-white/10 bg-white/5 p-6 shadow-lg shadow-primary/5">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-xs uppercase tracking-[0.2em] text-primary">Recent purchases</p>
        <h2 class="text-2xl font-bold text-white">Latest downloads</h2>
      </div>
      <a href="purchases.php" class="text-sm font-semibold text-primary hover:underline">View all</a>
    </div>
    <div class="mt-6 space-y-4">
      <?php foreach ($recentPurchases as $purchase): ?>
        <article class="flex flex-col gap-4 rounded-xl border border-white/10 bg-white/5 p-4 sm:flex-row sm:items-start">
          <img src="<?= escape_html($purchase['image']); ?>" alt="<?= escape_html($purchase['title']); ?> preview" class="h-16 w-16 rounded-xl object-cover"/>
          <div class="flex-1 min-w-0">
            <p class="text-xs text-gray-400"><?= escape_html($purchase['category']); ?> • <?= escape_html($purchase['seller']); ?></p>
            <p class="text-lg font-semibold text-white"><?= escape_html($purchase['title']); ?></p>
            <p class="text-sm text-gray-400">Purchased <?= escape_html($purchase['purchased_at']); ?></p>
          </div>
          <div class="flex w-full flex-col items-start text-left sm:w-auto sm:items-end sm:text-right">
            <p class="text-base font-semibold text-white">$<?= number_format((float) $purchase['price_usd'], 2); ?></p>
            <p class="text-xs text-gray-500">₦<?= number_format((float) $purchase['price_ngn'], 0); ?></p>
            <div class="mt-3 flex w-full flex-wrap gap-2 sm:w-auto sm:justify-end">
              <a href="<?= escape_html($purchase['download_url']); ?>" class="inline-flex w-full items-center justify-center gap-1 rounded-lg bg-primary/90 px-3 py-1.5 text-xs font-semibold text-white sm:w-auto">
                <span class="material-symbols-outlined text-base">download</span>
                Download
              </a>
              <a href="<?= escape_html($purchase['details_url']); ?>" class="inline-flex w-full items-center justify-center gap-1 rounded-lg border border-white/20 px-3 py-1.5 text-xs font-semibold text-gray-200 sm:w-auto">
                Details
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="space-y-6">
    <article class="rounded-2xl border border-white/10 bg-white/5 p-6 text-white">
      <p class="text-xs uppercase tracking-[0.3em] text-primary">Active downloads</p>
      <h2 class="text-xl font-bold">Quick access</h2>
      <div class="mt-4 space-y-3">
        <?php foreach ($activeDownloads as $download): ?>
          <div class="rounded-xl border border-white/10 bg-white/5 p-3">
            <p class="text-sm text-gray-400"><?= escape_html($download['category']); ?></p>
            <p class="text-base font-semibold text-white"><?= escape_html($download['title']); ?></p>
            <div class="mt-3 flex items-center justify-end text-xs text-gray-400">
              <a href="<?= escape_html($download['download_url']); ?>" class="inline-flex items-center gap-1 text-primary">
                <span class="material-symbols-outlined text-base">download</span>
                Download
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <a href="downloads.php" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-primary">
        Manage downloads
        <span class="material-symbols-outlined text-base">arrow_outward</span>
      </a>
    </article>
    <article class="rounded-2xl border border-white/10 bg-white/5 p-6 text-white">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-xs uppercase tracking-[0.3em] text-primary">Support</p>
          <h2 class="text-xl font-bold">Open tickets</h2>
        </div>
        <a href="support.php" class="text-sm font-semibold text-primary">View desk</a>
      </div>
      <div class="mt-4 space-y-3">
        <?php foreach ($openTickets as $ticket): ?>
          <div class="rounded-xl border border-white/10 bg-white/5 p-4">
            <div class="flex items-center justify-between">
              <p class="text-sm font-semibold text-white"><?= escape_html($ticket['subject']); ?></p>
              <span class="rounded-full border border-white/20 px-2 py-0.5 text-xs text-gray-300"><?= escape_html($ticket['status']); ?></span>
            </div>
            <p class="mt-2 text-xs text-gray-400">Updated <?= escape_html($ticket['updated_at']); ?> • Priority <?= escape_html($ticket['priority']); ?></p>
          </div>
        <?php endforeach; ?>
        <?php if (!$openTickets): ?>
          <p class="text-sm text-gray-400">No open tickets right now.</p>
        <?php endif; ?>
      </div>
      <a href="support.php" class="mt-4 inline-flex items-center gap-1 rounded-lg bg-primary/90 px-3 py-2 text-sm font-semibold text-white">
        <span class="material-symbols-outlined text-base">add</span>
        Create ticket
      </a>
    </article>
  </div>
</section>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/../templates/user/dashboard_layout.php';
