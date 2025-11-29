<?php
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/helpers.php';
require __DIR__ . '/../inc/auth.php';
require __DIR__ . '/../inc/csrf.php';

require_admin();

$adminPageTitle = 'Orders & Transactions';
$activeNav = 'orders';

$errors = [];
$notices = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid request token.';
    }
    $orderId = (int)($_POST['order_id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $allowedStatuses = ['pending','completed','failed','refunded'];
    if (!in_array($status, $allowedStatuses, true)) {
        $errors[] = 'Invalid status selected.';
    }
    if (!$orderId) {
        $errors[] = 'Missing order id.';
    }
    if (!$errors) {
      $orderLookup = $pdo->prepare('SELECT status, product_id FROM orders WHERE id = :id');
      $orderLookup->execute([':id' => $orderId]);
      $current = $orderLookup->fetch();
      if (!$current) {
        $errors[] = 'Order not found.';
      } else {
        $stmt = $pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->execute([':status' => $status, ':id' => $orderId]);
        if ($status === 'completed' && $current['status'] !== 'completed' && $current['product_id']) {
          $increment = $pdo->prepare('UPDATE products SET downloads_count = downloads_count + 1 WHERE id = :pid');
          $increment->execute([':pid' => $current['product_id']]);
        }
        $notices[] = "Order #{$orderId} updated.";
      }
    }
}

$ordersStmt = $pdo->prepare('SELECT o.*, u.full_name, u.email, p.title as product_title FROM orders o INNER JOIN users u ON u.id = o.user_id INNER JOIN products p ON p.id = o.product_id ORDER BY o.created_at DESC LIMIT 50');
$ordersStmt->execute();
$orders = $ordersStmt->fetchAll();
?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/sidebar.php'; ?>
<main class="flex-1 flex flex-col bg-transparent">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <section class="admin-content flex-1 space-y-8 overflow-y-auto px-4 py-6 sm:px-6 lg:px-10" data-admin-content>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white">Orders &amp; Transactions</h1>
        <p class="text-sm text-white/70">Track purchases, statuses, and payment gateways.</p>
      </div>
    </div>
    <?php foreach ($notices as $notice): ?>
      <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100"><?= escape_html($notice); ?></div>
    <?php endforeach; ?>
    <?php if ($errors): ?>
      <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
        <?php foreach ($errors as $error): ?>
          <p><?= escape_html($error); ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <div class="admin-panel overflow-hidden rounded-2xl shadow-soft">
      <div class="overflow-x-auto" data-admin-table-wrapper>
          <table class="admin-table min-w-full text-left text-sm" data-admin-table>
          <thead class="bg-white/5 text-xs uppercase text-white/60">
            <tr>
              <th class="px-4 py-3">Order</th>
              <th class="px-4 py-3">Buyer</th>
              <th class="px-4 py-3">Product</th>
              <th class="px-4 py-3">Amount</th>
              <th class="px-4 py-3">Gateway</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Created</th>
              <th class="px-4 py-3">Update</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10 dark:divide-slate-800">
            <?php if (!$orders): ?>
              <tr>
                <td colspan="8" class="px-4 py-6 text-center text-white/60">No orders yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($orders as $order): ?>
                <tr class="hover:bg-white/5">
                  <td class="px-4 py-4 font-semibold text-white">#<?= (int)$order['id']; ?></td>
                  <td class="px-4 py-4">
                    <p class="font-medium text-white"><?= escape_html($order['full_name'] ?: $order['email']); ?></p>
                    <p class="text-xs text-white/60"><?= escape_html($order['email']); ?></p>
                  </td>
                  <td class="px-4 py-4 text-white/70"><?= escape_html($order['product_title']); ?></td>
                  <td class="px-4 py-4 font-semibold text-white"><?= escape_html(format_currency($order['amount'], $order['currency'])); ?></td>
                  <td class="px-4 py-4 text-white/60">
                    <?= escape_html($order['payment_gateway'] ?: 'Offline'); ?>
                    <?php if ($order['gateway_ref']): ?>
                      <p class="text-xs text-white/40">Ref: <?= escape_html($order['gateway_ref']); ?></p>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-4">
                    <?php $statusColor = match($order['status']){
                      'completed' => 'bg-emerald-500/20 text-emerald-300',
                      'failed' => 'bg-rose-500/20 text-rose-300',
                      'refunded' => 'bg-amber-500/20 text-amber-300',
                      default => 'bg-indigo-500/20 text-indigo-300',
                    }; ?>
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?= $statusColor; ?>"><?= ucfirst($order['status']); ?></span>
                  </td>
                  <td class="px-4 py-4 text-white/60"><?= escape_html(date('M j, Y g:ia', strtotime($order['created_at']))); ?></td>
                  <td class="px-4 py-4">
                    <form method="post" class="flex items-center gap-2">
                      <input type="hidden" name="csrf" value="<?= escape_html(generate_csrf()); ?>" />
                      <input type="hidden" name="order_id" value="<?= (int)$order['id']; ?>" />
                      <select name="status" class="rounded-xl border border-white/20 bg-transparent px-3 py-2 text-xs text-white focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:bg-slate-900/70">
                        <?php foreach (['pending','completed','failed','refunded'] as $statusOption): ?>
                          <option value="<?= $statusOption; ?>" <?= $statusOption === $order['status'] ? 'selected' : ''; ?>><?= ucfirst($statusOption); ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button type="submit" class="inline-flex items-center rounded-full border border-white/20 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/10">Save</button>
                    </form>
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
