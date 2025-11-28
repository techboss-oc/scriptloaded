<?php
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/helpers.php';
require __DIR__ . '/../inc/auth.php';
require __DIR__ . '/../inc/csrf.php';

require_admin();

$adminPageTitle = 'Dashboard';
$activeNav = 'dashboard';

$totalUsers = (int)($pdo->query('SELECT COUNT(*) FROM users')->fetchColumn() ?: 0);
$totalProducts = (int)($pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() ?: 0);
$totalOrders = (int)($pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn() ?: 0);
$revenueByCurrency = ['USD' => 0.0, 'NGN' => 0.0];
$revenueStmt = $pdo->query("SELECT currency, COALESCE(SUM(amount),0) AS total FROM orders WHERE status = 'completed' GROUP BY currency");
foreach ($revenueStmt->fetchAll() as $row) {
  $currency = strtoupper((string)$row['currency']);
  $revenueByCurrency[$currency] = (float)$row['total'];
}
$totalRevenueUSD = $revenueByCurrency['USD'];
$totalRevenueNGN = $revenueByCurrency['NGN'];
$pendingOrders = (int)($pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn() ?: 0);

$recentProductsStmt = $pdo->prepare('SELECT title, category, created_at, is_active FROM products ORDER BY created_at DESC LIMIT 6');
$recentProductsStmt->execute();
$recentProducts = $recentProductsStmt->fetchAll();

$recentOrdersStmt = $pdo->prepare('SELECT o.id, o.amount, o.currency, o.status, o.created_at, u.full_name, u.email, p.title FROM orders o INNER JOIN users u ON u.id = o.user_id INNER JOIN products p ON p.id = o.product_id ORDER BY o.created_at DESC LIMIT 5');
$recentOrdersStmt->execute();
$recentOrders = $recentOrdersStmt->fetchAll();

$categoryStatsStmt = $pdo->prepare('SELECT category, COUNT(*) as total FROM products WHERE category IS NOT NULL AND category <> "" GROUP BY category ORDER BY total DESC LIMIT 3');
$categoryStatsStmt->execute();
$categoryStats = $categoryStatsStmt->fetchAll();
$categorySum = array_sum(array_column($categoryStats, 'total')) ?: 1;
?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/sidebar.php'; ?>
<main class="flex-1 flex flex-col bg-white/40 dark:bg-slate-950/30">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <section class="admin-content flex-1 space-y-10 overflow-y-auto px-4 py-6 sm:px-6 lg:px-10" data-admin-content>
    <div>
      <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">Admin Dashboard</h1>
      <p class="text-sm text-slate-500 dark:text-slate-400">Overview of marketplace performance and submissions.</p>
    </div>
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
      <article class="admin-panel rounded-2xl p-6 shadow-soft dark:shadow-soft-dark">
        <p class="text-sm text-slate-500 dark:text-slate-400">Total Users</p>
        <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white"><?= number_format($totalUsers); ?></p>
        <p class="text-xs text-emerald-500">Marketplace reach</p>
      </article>
      <article class="admin-panel rounded-2xl p-6 shadow-soft dark:shadow-soft-dark">
        <p class="text-sm text-slate-500 dark:text-slate-400">Active Products</p>
        <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white"><?= number_format($totalProducts); ?></p>
        <p class="text-xs text-sky-500">Published catalog</p>
      </article>
      <article class="admin-panel rounded-2xl p-6 shadow-soft dark:shadow-soft-dark">
        <p class="text-sm text-slate-500 dark:text-slate-400">Completed Revenue</p>
        <div class="mt-2 text-slate-900 dark:text-white">
          <p class="text-3xl font-bold"><?= escape_html(format_currency($totalRevenueUSD, 'USD')); ?></p>
          <p class="text-lg font-semibold text-slate-600 dark:text-slate-200"><?= escape_html(format_currency($totalRevenueNGN, 'NGN')); ?></p>
        </div>
        <p class="text-xs text-emerald-500">Captured via offline/demo flow</p>
      </article>
      <article class="admin-panel rounded-2xl p-6 shadow-soft dark:shadow-soft-dark">
        <p class="text-sm text-slate-500 dark:text-slate-400">Pending Orders</p>
        <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white"><?= number_format($pendingOrders); ?></p>
        <p class="text-xs text-amber-500">Awaiting payment confirmation</p>
      </article>
    </div>
    <div class="grid gap-6 lg:grid-cols-3">
      <section class="admin-panel lg:col-span-2 rounded-2xl p-6 shadow-soft dark:shadow-soft-dark">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-lg font-semibold text-slate-900 dark:text-white">Recent Orders</p>
            <p class="text-sm text-slate-500 dark:text-slate-400">Latest transactions across gateways.</p>
          </div>
        </div>
        <div class="mt-6 overflow-x-auto" data-admin-table-wrapper>
          <table class="admin-table min-w-full text-left text-sm" data-admin-table>
            <thead class="text-xs uppercase text-slate-500 dark:text-slate-400">
              <tr>
                <th class="px-3 py-2">Order</th>
                <th class="px-3 py-2">Buyer</th>
                <th class="px-3 py-2">Product</th>
                <th class="px-3 py-2">Amount</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/10 dark:divide-slate-800">
              <?php if (!$recentOrders): ?>
                <tr>
                  <td class="px-3 py-4 text-center text-slate-500 dark:text-slate-400" colspan="6">No orders yet.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($recentOrders as $order): ?>
                  <tr>
                    <td class="px-3 py-3 font-semibold text-slate-900 dark:text-white">#<?= (int)$order['id']; ?></td>
                    <td class="px-3 py-3">
                      <p class="font-medium text-slate-900 dark:text-white"><?= escape_html($order['full_name'] ?: $order['email']); ?></p>
                      <p class="text-xs text-slate-500 dark:text-slate-400"><?= escape_html($order['email']); ?></p>
                    </td>
                    <td class="px-3 py-3 text-slate-600 dark:text-slate-300"><?= escape_html($order['title']); ?></td>
                    <td class="px-3 py-3 font-semibold text-slate-900 dark:text-white"><?= escape_html(format_currency($order['amount'], $order['currency'])); ?></td>
                    <td class="px-3 py-3">
                      <?php $status = $order['status']; $statusColor = match($status){'completed'=>'bg-emerald-500/20 text-emerald-400','failed'=>'bg-rose-500/20 text-rose-400','refunded'=>'bg-amber-500/20 text-amber-400',default=>'bg-indigo-500/20 text-indigo-300'}; ?>
                      <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $statusColor; ?>"><?= ucfirst($status); ?></span>
                    </td>
                    <td class="px-3 py-3 text-slate-500 dark:text-slate-400"><?= escape_html(date('M j, Y', strtotime($order['created_at']))); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>
      <section class="admin-panel rounded-2xl p-6 shadow-soft dark:shadow-soft-dark">
        <p class="text-lg font-semibold text-slate-900 dark:text-white">Top Categories</p>
        <ul class="mt-6 space-y-4">
          <?php if (!$categoryStats): ?>
            <li class="text-sm text-slate-500 dark:text-slate-400">No category data yet.</li>
          <?php else: ?>
            <?php foreach ($categoryStats as $row): ?>
              <?php $pct = round(($row['total'] / $categorySum) * 100); ?>
              <li>
                <div class="flex items-center justify-between text-sm font-semibold text-slate-900 dark:text-white">
                  <span><?= escape_html($row['category']); ?></span>
                  <span><?= $pct; ?>%</span>
                </div>
                <div class="mt-2 h-2 rounded-full bg-slate-200/60 dark:bg-slate-800">
                  <div class="h-full rounded-full bg-primary" style="width: <?= $pct; ?>%"></div>
                </div>
              </li>
            <?php endforeach; ?>
          <?php endif; ?>
        </ul>
      </section>
    </div>
    <section class="admin-panel rounded-2xl p-6 shadow-soft dark:bg-slate-900/70 dark:shadow-soft-dark">
      <div class="flex items-center justify-between gap-4">
        <div>
          <p class="text-lg font-semibold text-slate-900 dark:text-white">Latest Product Submissions</p>
          <p class="text-sm text-slate-500 dark:text-slate-400">Monitor newly published assets.</p>
        </div>
        <a href="<?= escape_html(site_url('admin/products.php')); ?>" class="inline-flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-primary hover:bg-primary/10">View All</a>
      </div>
      <div class="mt-6 overflow-x-auto" data-admin-table-wrapper>
        <table class="admin-table min-w-full text-left text-sm" data-admin-table>
          <thead class="text-xs uppercase text-slate-500 dark:text-slate-400">
            <tr>
              <th class="px-3 py-2">Product</th>
              <th class="px-3 py-2">Category</th>
              <th class="px-3 py-2">Status</th>
              <th class="px-3 py-2">Created</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10 dark:divide-slate-800">
            <?php if (!$recentProducts): ?>
              <tr>
                <td colspan="4" class="px-3 py-4 text-center text-slate-500 dark:text-slate-400">No products available.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($recentProducts as $product): ?>
                <tr>
                  <td class="px-3 py-3 font-semibold text-slate-900 dark:text-white"><?= escape_html($product['title']); ?></td>
                  <td class="px-3 py-3 text-slate-500 dark:text-slate-400"><?= escape_html($product['category'] ?: 'Uncategorized'); ?></td>
                  <td class="px-3 py-3">
                    <?php $flag = (int)$product['is_active'] === 1 ? 'Active' : 'Hidden'; $flagColor = $product['is_active'] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-500/20 text-slate-300'; ?>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?= $flagColor; ?>"><?= $flag; ?></span>
                  </td>
                  <td class="px-3 py-3 text-slate-500 dark:text-slate-400"><?= escape_html(date('M j, Y', strtotime($product['created_at']))); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
