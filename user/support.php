<?php
require __DIR__ . '/_bootstrap.php';

$supportErrors = [];
$supportSuccess = null;
$ticketForm = [
  'subject' => '',
  'priority' => 'medium',
  'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = $_POST['csrf_token'] ?? '';
  $ticketForm['subject'] = trim((string)($_POST['subject'] ?? ''));
  $ticketForm['priority'] = strtolower(trim((string)($_POST['priority'] ?? 'medium')));
  $ticketForm['message'] = trim((string)($_POST['message'] ?? ''));
  if (!validate_csrf($token)) {
    $supportErrors[] = 'Invalid request. Please refresh and try again.';
  }
  $allowedPriorities = ['low', 'medium', 'high'];
  if ($ticketForm['subject'] === '') {
    $supportErrors[] = 'Subject is required.';
  }
  if (!in_array($ticketForm['priority'], $allowedPriorities, true)) {
    $supportErrors[] = 'Choose a valid priority.';
  }
  if (strlen($ticketForm['message']) < 10) {
    $supportErrors[] = 'Message must be at least 10 characters.';
  }
  if (!$supportErrors) {
    $saved = insert_support_ticket($pdo, $userProfile['id'], $ticketForm['subject'], $ticketForm['message'], $ticketForm['priority']);
    if ($saved) {
      $supportSuccess = 'Your ticket was submitted. Our team will reply shortly.';
      $ticketForm = ['subject' => '', 'priority' => 'medium', 'message' => ''];
    } else {
      $supportErrors[] = 'Unable to create ticket. Please try again.';
    }
  }
}

$supportTickets = array_map(static function (array $ticket) {
  return [
    'id' => '#'.str_pad((string)$ticket['id'], 4, '0', STR_PAD_LEFT),
    'subject' => $ticket['subject'],
    'status' => ucwords(str_replace('_', ' ', $ticket['status'])),
    'updated_at' => date('M d, Y', strtotime($ticket['updated_at'] ?? $ticket['created_at'])),
    'priority' => ucfirst($ticket['priority']),
  ];
}, fetch_support_tickets($pdo, $userProfile['id']));
$csrfToken = generate_csrf();

$pageTitle = 'Support Desk';
$activeNav = 'support';

ob_start();
?>
<section class="flex flex-wrap items-center justify-between gap-4">
  <div>
    <p class="text-xs uppercase tracking-[0.3em] text-primary">Customer care</p>
    <h2 class="text-4xl font-black text-white">Support Tickets</h2>
    <p class="mt-1 text-sm text-gray-400">Our average first response time is under 1 hour.</p>
  </div>
  <div class="flex items-center gap-3 text-sm text-gray-300">
    <span class="material-symbols-outlined text-base text-green-400">schedule</span>
    Live chat available Mon–Fri • 08:00–18:00 UTC
  </div>
</section>
<section class="mt-10 grid gap-8 lg:grid-cols-3">
  <article class="lg:col-span-2 rounded-2xl border border-white/10 bg-white/5 p-6">
    <div class="flex items-center justify-between">
      <h3 class="text-2xl font-semibold text-white">Open conversations</h3>
      <div class="text-xs uppercase tracking-[0.3em] text-gray-400">Updated realtime</div>
    </div>
    <div class="mt-4 space-y-4">
      <?php foreach ($supportTickets as $ticket): ?>
        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
          <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
              <p class="text-sm text-gray-400"><?= escape_html($ticket['id']); ?></p>
              <p class="text-lg font-semibold text-white"><?= escape_html($ticket['subject']); ?></p>
            </div>
            <div class="flex items-center gap-2 text-xs">
              <span class="rounded-full border border-white/20 px-3 py-1 text-gray-200"><?= escape_html($ticket['status']); ?></span>
              <span class="rounded-full border border-white/20 px-3 py-1 text-gray-200">Priority: <?= escape_html($ticket['priority']); ?></span>
            </div>
          </div>
          <p class="mt-2 text-sm text-gray-400">Updated <?= escape_html($ticket['updated_at']); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </article>
  <article class="rounded-2xl border border-white/10 bg-white/5 p-6">
    <h3 class="text-2xl font-semibold text-white">Create a ticket</h3>
    <p class="mt-1 text-sm text-gray-400">Share as much context as possible so we can help faster.</p>
    <form class="mt-4 space-y-4" method="post" action="#">
      <input type="hidden" name="csrf_token" value="<?= escape_html($csrfToken); ?>"/>
      <?php if ($supportSuccess): ?>
        <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-3 text-sm text-emerald-100">
          <?= escape_html($supportSuccess); ?>
        </div>
      <?php endif; ?>
      <?php if ($supportErrors): ?>
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-3 text-sm text-red-100">
          <?php foreach ($supportErrors as $error): ?>
            <p><?= escape_html($error); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <label class="text-sm text-gray-400">Subject
        <input type="text" name="subject" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white" placeholder="e.g. Download link expired" value="<?= escape_html($ticketForm['subject']); ?>"/>
      </label>
      <label class="text-sm text-gray-400">Priority
        <select name="priority" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white">
          <?php foreach (['low','medium','high'] as $priorityOption): ?>
            <option value="<?= $priorityOption; ?>" <?= $priorityOption === $ticketForm['priority'] ? 'selected' : ''; ?>><?= ucfirst($priorityOption); ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label class="text-sm text-gray-400">Message
        <textarea name="message" rows="5" class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white" placeholder="Share URLs, order IDs, or reproduction steps."><?= escape_html($ticketForm['message']); ?></textarea>
      </label>
      <button type="submit" class="w-full rounded-xl bg-primary py-3 text-sm font-semibold uppercase tracking-[0.2em] text-white shadow-lg shadow-primary/40">
        Submit ticket
      </button>
    </form>
    <p class="mt-4 text-xs text-gray-500">Need immediate help? Email support@scriptloaded.test</p>
  </article>
</section>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/../templates/user/dashboard_layout.php';
