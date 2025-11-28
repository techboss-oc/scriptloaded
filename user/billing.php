<?php
require __DIR__ . '/_bootstrap.php';

header('Location: dashboard.php');
exit;

$cardHolder = $userProfile['name'] ?? '';
$expiry = '';
$billingErrors = [];
$billingSuccess = null;
$submittedForm = $_POST['form'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $submittedForm === 'billing') {
  $token = $_POST['csrf_token'] ?? '';
  if (!validate_csrf($token)) {
    $billingErrors[] = 'Invalid request. Please refresh and try again.';
  } else {
    $cardNumberRaw = $_POST['card_number'] ?? '';
    $cardNumberDigits = preg_replace('/\D+/', '', $cardNumberRaw);
    $cardHolder = trim($_POST['card_holder'] ?? '');
    $expiry = trim($_POST['expiry'] ?? '');
    $cvc = preg_replace('/\D+/', '', $_POST['cvc'] ?? '');
    if (strlen($cardNumberDigits) < 12 || strlen($cardNumberDigits) > 19) {
      $billingErrors[] = 'Card number must be 12 to 19 digits.';
    }
    if ($cardHolder === '') {
      $billingErrors[] = 'Card holder name is required.';
    }
    if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2}|\d{4})$/', $expiry, $matches)) {
      $billingErrors[] = 'Expiry must be in MM/YY format.';
    }
    if ($cvc !== '' && (strlen($cvc) < 3 || strlen($cvc) > 4)) {
      $billingErrors[] = 'CVC must be 3 or 4 digits.';
    }
    if (!$billingErrors) {
      $expMonth = (int)$matches[1];
      $expYear = (int)$matches[2];
      if ($expYear < 100) {
        $expYear += 2000;
      }
      $expiryDate = DateTime::createFromFormat('Ym', sprintf('%04d%02d', $expYear, $expMonth));
      if (!$expiryDate) {
        $billingErrors[] = 'Could not parse expiry date.';
      } else {
        $expiryDate->modify('last day of this month');
        if ($expiryDate < new DateTime('first day of this month')) {
          $billingErrors[] = 'The card is already expired.';
        }
      }
    }
    if (!$billingErrors) {
      insert_billing_method($pdo, $userProfile['id'], [
        'brand' => detect_card_brand($cardNumberDigits),
        'last4' => substr($cardNumberDigits, -4),
        'exp_month' => $expMonth,
        'exp_year' => $expYear,
        'cardholder' => $cardHolder,
        'is_primary' => empty(fetch_billing_methods($pdo, $userProfile['id'])) ? 1 : 0,
      ]);
      $billingSuccess = 'Payment method saved.';
      $cardHolder = $userProfile['name'] ?? '';
      $expiry = '';
    }
  }
}

$billingRows = fetch_billing_methods($pdo, $userProfile['id']);
$billingMethods = array_map(static function (array $method) {
  return [
    'brand' => $method['brand'],
    'last4' => $method['last4'],
    'exp' => str_pad((string)$method['exp_month'], 2, '0', STR_PAD_LEFT) . '/' . substr((string)$method['exp_year'], -2),
    'is_primary' => (bool)$method['is_primary'],
  ];
}, $billingRows);
$invoiceRows = fetch_invoices($pdo, $userProfile['id']);
$invoices = array_map(static function (array $invoice) {
  return [
    'id' => $invoice['invoice_number'],
    'date' => date('M d, Y', strtotime($invoice['issued_at'])),
    'amount' => format_currency($invoice['amount'], $invoice['currency']),
    'status' => ucfirst($invoice['status']),
    'download_url' => $invoice['download_url'] ?? '#',
  ];
}, $invoiceRows);
$csrfToken = generate_csrf();

$pageTitle = 'Billing & Payment';
$activeNav = 'billing';

ob_start();
?>
<section class="flex flex-wrap items-center justify-between gap-4">
  <div>
    <p class="text-xs uppercase tracking-[0.3em] text-primary">Secure billing</p>
    <h2 class="text-4xl font-black text-white">Billing & Payment Settings</h2>
    <p class="mt-1 text-sm text-gray-400">Manage saved cards, invoices, and compliance documents.</p>
  </div>
  <div class="inline-flex items-center gap-2 text-sm text-gray-300">
    <span class="material-symbols-outlined text-green-400">lock</span>
    PCI DSS compliant
  </div>
</section>
<section class="mt-10 grid gap-8 lg:grid-cols-3">
  <div class="lg:col-span-2 space-y-6">
    <article class="rounded-2xl border border-white/10 bg-white/5 p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-2xl font-semibold text-white">Saved payment methods</h3>
        <span class="text-xs uppercase tracking-[0.3em] text-gray-400">Auto-renew enabled</span>
      </div>
      <div class="mt-6 space-y-4">
        <?php foreach ($billingMethods as $method): ?>
          <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 p-4 text-white">
            <div>
              <p class="text-sm text-gray-400"><?= escape_html($method['brand']); ?> ending in <?= escape_html($method['last4']); ?></p>
              <p class="text-base font-semibold">Expires <?= escape_html($method['exp']); ?></p>
            </div>
            <div class="flex items-center gap-3 text-sm">
              <?php if ($method['is_primary']): ?>
                <span class="rounded-full bg-primary/30 px-3 py-1 text-xs uppercase tracking-[0.2em] text-primary">Primary</span>
              <?php else: ?>
                <button class="text-gray-400 hover:text-white" type="button">Set primary</button>
              <?php endif; ?>
              <button class="text-gray-400 hover:text-white" type="button">Edit</button>
              <button class="text-gray-400 hover:text-red-400" type="button">Delete</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </article>
    <article class="rounded-2xl border border-white/10 bg-white/5 p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-2xl font-semibold text-white">Invoices</h3>
        <a href="#" class="text-sm font-semibold text-primary">Download all</a>
      </div>
      <div class="mt-4 overflow-hidden rounded-xl border border-white/10 bg-white/5">
        <table class="w-full text-left text-sm">
          <thead class="text-xs uppercase tracking-[0.3em] text-gray-400">
            <tr>
              <th class="px-5 py-3">Invoice</th>
              <th class="px-5 py-3">Date</th>
              <th class="px-5 py-3">Amount</th>
              <th class="px-5 py-3 text-right">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-white/10 text-white">
            <?php foreach ($invoices as $invoice): ?>
              <tr>
                <td class="px-5 py-3 font-semibold"><?= escape_html($invoice['id']); ?></td>
                <td class="px-5 py-3 text-gray-300"><?= escape_html($invoice['date']); ?></td>
                <td class="px-5 py-3 text-white"><?= escape_html($invoice['amount']); ?></td>
                <td class="px-5 py-3 text-right">
                  <a href="<?= escape_html($invoice['download_url']); ?>" class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-1 text-xs uppercase tracking-[0.2em] text-green-300">
                    <?= escape_html($invoice['status']); ?>
                    <span class="material-symbols-outlined text-base">download</span>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </article>
  </div>
  <div class="space-y-6">
    <article class="rounded-2xl border border-white/10 bg-white/5 p-6">
      <h3 class="text-2xl font-semibold text-white">Add payment method</h3>
      <?php if ($billingSuccess): ?>
        <p class="mt-4 rounded-xl border border-emerald-400/40 bg-emerald-400/10 px-4 py-2 text-sm text-emerald-200"><?= escape_html($billingSuccess); ?></p>
      <?php endif; ?>
      <?php if ($billingErrors): ?>
        <ul class="mt-4 space-y-2 rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-3 text-sm text-red-200">
          <?php foreach ($billingErrors as $error): ?>
            <li><?= escape_html($error); ?></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <form class="mt-4 space-y-4" method="post" action="#" novalidate>
        <input type="hidden" name="csrf_token" value="<?= escape_html($csrfToken); ?>"/>
        <input type="hidden" name="form" value="billing"/>
        <label class="block text-sm text-gray-400">Card number
          <input type="text" name="card_number" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white" placeholder="**** **** **** ****"/>
        </label>
        <label class="block text-sm text-gray-400">Card holder
          <input type="text" name="card_holder" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white" placeholder="<?= escape_html($userProfile['name']); ?>" value="<?= escape_html($cardHolder); ?>"/>
        </label>
        <div class="grid grid-cols-2 gap-4">
          <label class="block text-sm text-gray-400">Expiry
            <input type="text" name="expiry" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white" placeholder="MM/YY" value="<?= escape_html($expiry); ?>"/>
          </label>
          <label class="block text-sm text-gray-400">CVC
            <input type="text" name="cvc" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white" placeholder="***"/>
          </label>
        </div>
        <button type="submit" class="w-full rounded-xl bg-primary py-3 text-sm font-semibold uppercase tracking-[0.2em] text-white shadow-lg shadow-primary/40">Add card</button>
      </form>
    </article>
    <article class="rounded-2xl border border-white/10 bg-white/5 p-6 text-sm text-gray-300">
      <h4 class="text-base font-semibold text-white">Tax information</h4>
      <p class="mt-2 text-gray-400">Update billing entity, VAT numbers, or download W-9 statements.</p>
      <a href="#" class="mt-4 inline-flex items-center gap-2 text-primary">Manage tax profile<span class="material-symbols-outlined text-base">arrow_outward</span></a>
    </article>
  </div>
</section>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/../templates/user/dashboard_layout.php';
