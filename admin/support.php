<?php
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/helpers.php';
require __DIR__ . '/../inc/auth.php';
require __DIR__ . '/../inc/csrf.php';
require __DIR__ . '/../inc/store.php';

require_admin();

$adminPageTitle = 'Support Tickets';
$activeNav = 'support';

$errors = [];
$notices = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? '';
    $ticketId = (int)($_POST['ticket_id'] ?? 0);
    $status = strtolower(trim((string)($_POST['status'] ?? '')));
    $priority = strtolower(trim((string)($_POST['priority'] ?? '')));
    $allowedStatuses = ['open', 'in_progress', 'resolved', 'closed'];
    $allowedPriorities = ['low', 'medium', 'high'];

    if (!validate_csrf($token)) {
        $errors[] = 'Invalid request token.';
    }
    if (!$ticketId) {
        $errors[] = 'Missing ticket reference.';
    }
    if (!in_array($status, $allowedStatuses, true)) {
        $errors[] = 'Choose a valid status.';
    }
    if (!in_array($priority, $allowedPriorities, true)) {
        $errors[] = 'Choose a valid priority.';
    }

    if (!$errors) {
        $updated = update_support_ticket($pdo, $ticketId, [
            'status' => $status,
            'priority' => $priority,
        ]);
        if ($updated) {
            $notices[] = "Ticket #{$ticketId} updated.";
        } else {
            $errors[] = 'Unable to update ticket. Please try again.';
        }
    }
}

$tickets = fetch_all_support_tickets_with_users($pdo);
?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/sidebar.php'; ?>
<main class="flex-1 flex flex-col bg-transparent">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <section class="admin-content flex-1 space-y-8 overflow-y-auto px-4 py-6 sm:px-6 lg:px-10" data-admin-content>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white">Support Tickets</h1>
        <p class="text-sm text-white/70">Review requests from creators and keep statuses up-to-date.</p>
      </div>
      <div class="inline-flex items-center gap-2 rounded-full border border-white/30 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/70">
        <span class="material-symbols-outlined text-base text-primary">support_agent</span>
        Support Desk
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
              <th class="px-4 py-3">Ticket</th>
              <th class="px-4 py-3">Customer</th>
              <th class="px-4 py-3">Subject</th>
              <th class="px-4 py-3">Priority</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Created</th>
              <th class="px-4 py-3">Message</th>
              <th class="px-4 py-3">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10 dark:divide-slate-800">
            <?php if (!$tickets): ?>
              <tr>
                <td colspan="8" class="px-4 py-6 text-center text-white/60">No support requests yet.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($tickets as $ticket): ?>
                <?php
                  $statusColor = match ($ticket['status']) {
                    'resolved' => 'bg-emerald-500/20 text-emerald-400',
                    'closed' => 'bg-white/10 text-white/70',
                    'in_progress' => 'bg-amber-500/20 text-amber-400',
                    default => 'bg-indigo-500/20 text-indigo-300',
                  };
                  $priorityColor = match ($ticket['priority']) {
                    'high' => 'text-rose-400',
                    'low' => 'text-white/60',
                    default => 'text-amber-400',
                  };
                ?>
                <tr class="hover:bg-white/5">
                  <td class="px-4 py-4 font-semibold text-white">#<?= (int)$ticket['id']; ?></td>
                  <td class="px-4 py-4">
                    <p class="font-medium text-white"><?= escape_html($ticket['full_name'] ?: $ticket['email']); ?></p>
                    <p class="text-xs text-white/60"><?= escape_html($ticket['email']); ?></p>
                  </td>
                  <td class="px-4 py-4 text-white/80"><?= escape_html($ticket['subject']); ?></td>
                  <td class="px-4 py-4 font-semibold <?= $priorityColor; ?>"><?= ucfirst($ticket['priority']); ?></td>
                  <td class="px-4 py-4">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold <?= $statusColor; ?>"><?= ucwords(str_replace('_', ' ', $ticket['status'])); ?></span>
                  </td>
                  <td class="px-4 py-4 text-white/60"><?= escape_html(date('M j, Y g:ia', strtotime($ticket['created_at']))); ?></td>
                  <td class="px-4 py-4 text-white/70">
                    <p class="max-h-28 overflow-y-auto whitespace-pre-line text-sm"><?= nl2br(escape_html($ticket['message'])); ?></p>
                  </td>
                  <td class="px-4 py-4">
                    <form method="post" class="space-y-2">
                      <input type="hidden" name="csrf" value="<?= escape_html(generate_csrf()); ?>" />
                      <input type="hidden" name="ticket_id" value="<?= (int)$ticket['id']; ?>" />
                      <label class="block text-xs font-semibold text-white/70">Status
                        <select name="status" class="mt-1 w-full rounded-xl border border-white/30 bg-transparent px-3 py-2 text-xs text-white focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:bg-slate-900/70">
                          <?php foreach (['open','in_progress','resolved','closed'] as $statusOption): ?>
                            <option value="<?= $statusOption; ?>" <?= $statusOption === $ticket['status'] ? 'selected' : ''; ?>><?= ucwords(str_replace('_', ' ', $statusOption)); ?></option>
                          <?php endforeach; ?>
                        </select>
                      </label>
                      <label class="block text-xs font-semibold text-white/70">Priority
                        <select name="priority" class="mt-1 w-full rounded-xl border border-white/30 bg-transparent px-3 py-2 text-xs text-white focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:bg-slate-900/70">
                          <?php foreach (['low','medium','high'] as $priorityOption): ?>
                            <option value="<?= $priorityOption; ?>" <?= $priorityOption === $ticket['priority'] ? 'selected' : ''; ?>><?= ucfirst($priorityOption); ?></option>
                          <?php endforeach; ?>
                        </select>
                      </label>
                      <button type="submit" class="inline-flex w-full items-center justify-center rounded-full border border-white/30 px-3 py-2 text-xs font-semibold text-primary hover:bg-primary/10">Update</button>
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
