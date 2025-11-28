<?php
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/helpers.php';
require __DIR__ . '/../inc/auth.php';
require __DIR__ . '/../inc/csrf.php';

require_admin();

$adminPageTitle = 'Product Management';
$activeNav = 'products';

$search = trim($_GET['q'] ?? '');
$params = [];
$sql = 'SELECT id, title, slug, category, price_usd, price_ngn, is_active, created_at, updated_at FROM products';
if ($search !== '') {
    $sql .= ' WHERE title LIKE :term OR slug LIKE :term';
    $params[':term'] = "%$search%";
}
$sql .= ' ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/sidebar.php'; ?>
<main class="flex-1 flex flex-col bg-white/40 dark:bg-slate-950/30">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <section class="admin-content flex-1 space-y-8 overflow-y-auto px-4 py-6 sm:px-6 lg:px-10" data-admin-content>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Product Management</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Approve, edit or retire catalog assets.</p>
      </div>
      <div class="flex flex-wrap gap-3">
        <a href="<?= escape_html(site_url('admin/product_new.php')); ?>" class="inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow-soft hover:bg-primary/90">
          <span class="material-symbols-outlined text-base">add</span>
          New Product
        </a>
      </div>
    </div>
    <form method="get" class="admin-panel flex flex-wrap items-center gap-3 rounded-2xl p-4 shadow-soft">
      <label class="relative flex-1 min-w-[200px] max-w-xl">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
        <input type="search" name="q" value="<?= escape_html($search); ?>" placeholder="Search by title or slug" class="w-full rounded-xl border border-white/20 bg-transparent py-2 pl-11 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
      </label>
      <?php if ($search !== ''): ?>
        <a href="<?= escape_html(site_url('admin/products.php')); ?>" class="text-sm font-semibold text-slate-500 hover:text-primary dark:text-slate-400">Clear</a>
      <?php endif; ?>
      <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-white/20 px-4 py-2 text-sm font-semibold text-slate-900 hover:bg-white/20 dark:text-white">
        Filter
      </button>
    </form>
    <div class="admin-panel overflow-hidden rounded-2xl shadow-soft">
      <div class="overflow-x-auto" data-admin-table-wrapper>
        <table class="admin-table min-w-full text-left text-sm" data-admin-table>
          <thead class="bg-white/50 text-xs uppercase text-slate-500 dark:bg-slate-900/50 dark:text-slate-400">
            <tr>
              <th class="px-4 py-3">Product</th>
              <th class="px-4 py-3">Category</th>
              <th class="px-4 py-3">Price (USD)</th>
              <th class="px-4 py-3">Price (NGN)</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Created</th>
              <th class="px-4 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10 dark:divide-slate-800">
            <?php if (!$products): ?>
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">No products found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($products as $product): ?>
                <tr class="hover:bg-white/40 dark:hover:bg-slate-800/30">
                  <td class="px-4 py-4 font-semibold text-slate-900 dark:text-white">
                    <?= escape_html($product['title']); ?>
                    <p class="text-xs font-normal text-slate-500 dark:text-slate-400">Slug: <?= escape_html($product['slug']); ?></p>
                  </td>
                  <td class="px-4 py-4 text-slate-600 dark:text-slate-300"><?= escape_html($product['category'] ?: 'Uncategorized'); ?></td>
                  <td class="px-4 py-4 font-semibold text-slate-900 dark:text-white"><?= escape_html(format_currency($product['price_usd'], 'USD')); ?></td>
                  <td class="px-4 py-4 font-semibold text-slate-900 dark:text-white"><?= escape_html(format_currency($product['price_ngn'], 'NGN')); ?></td>
                  <td class="px-4 py-4">
                    <?php $statusLabel = $product['is_active'] ? 'Active' : 'Hidden'; $statusColor = $product['is_active'] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-500/20 text-slate-300'; ?>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?= $statusColor; ?>"><?= $statusLabel; ?></span>
                  </td>
                  <td class="px-4 py-4 text-slate-500 dark:text-slate-400"><?= escape_html(date('M j, Y', strtotime($product['created_at']))); ?></td>
                  <td class="px-4 py-4 text-right text-sm font-semibold">
                    <a href="<?= escape_html(site_url('admin/product_edit.php?id=' . (int)$product['id'])); ?>" class="text-primary hover:underline">Edit</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
