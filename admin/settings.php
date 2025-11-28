<?php
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/helpers.php';
require __DIR__ . '/../inc/auth.php';
require __DIR__ . '/../inc/csrf.php';

require_admin();

$adminPageTitle = 'Website Settings';
$activeNav = 'settings';

$errors = [];
$notices = [];
$defaults = [
  'site_name' => get_setting('site_name', 'Scriptloaded'),
  'currency_default' => get_setting('currency_default', 'USD'),
  'currency_rate_usd_to_ngn' => get_setting('currency_rate_usd_to_ngn', $_ENV['CURRENCY_RATE_USD_NGN'] ?? '1150'),
  'payment_manual_enabled' => (int)get_setting('payment_manual_enabled', '1'),
  'payment_manual_account' => get_setting('payment_manual_account', 'Scriptloaded Ltd.\nGTBank - 0123456789'),
  'payment_manual_instructions' => get_setting('payment_manual_instructions', 'Transfer the invoice total, then upload proof of payment inside your dashboard so we can activate downloads.'),
  'payment_paystack_enabled' => (int)get_setting('payment_paystack_enabled', '0'),
  'payment_paystack_public_key' => get_setting('payment_paystack_public_key', ''),
  'payment_paystack_secret_key' => get_setting('payment_paystack_secret_key', ''),
  'payment_flutterwave_enabled' => (int)get_setting('payment_flutterwave_enabled', '0'),
  'payment_flutterwave_public_key' => get_setting('payment_flutterwave_public_key', ''),
  'payment_flutterwave_secret_key' => get_setting('payment_flutterwave_secret_key', ''),
];

$input = [
  'site_name' => $_POST ? trim($_POST['site_name'] ?? '') : $defaults['site_name'],
  'currency_default' => $_POST ? trim($_POST['currency_default'] ?? 'USD') : $defaults['currency_default'],
  'currency_rate_usd_to_ngn' => $_POST ? trim($_POST['currency_rate_usd_to_ngn'] ?? $defaults['currency_rate_usd_to_ngn']) : $defaults['currency_rate_usd_to_ngn'],
  'payment_manual_enabled' => $_POST ? (isset($_POST['payment_manual_enabled']) ? 1 : 0) : (int)$defaults['payment_manual_enabled'],
  'payment_manual_account' => $_POST ? trim($_POST['payment_manual_account'] ?? '') : $defaults['payment_manual_account'],
  'payment_manual_instructions' => $_POST ? trim($_POST['payment_manual_instructions'] ?? '') : $defaults['payment_manual_instructions'],
  'payment_paystack_enabled' => $_POST ? (isset($_POST['payment_paystack_enabled']) ? 1 : 0) : (int)$defaults['payment_paystack_enabled'],
  'payment_paystack_public_key' => $_POST ? trim($_POST['payment_paystack_public_key'] ?? '') : $defaults['payment_paystack_public_key'],
  'payment_paystack_secret_key' => $_POST ? trim($_POST['payment_paystack_secret_key'] ?? '') : $defaults['payment_paystack_secret_key'],
  'payment_flutterwave_enabled' => $_POST ? (isset($_POST['payment_flutterwave_enabled']) ? 1 : 0) : (int)$defaults['payment_flutterwave_enabled'],
  'payment_flutterwave_public_key' => $_POST ? trim($_POST['payment_flutterwave_public_key'] ?? '') : $defaults['payment_flutterwave_public_key'],
  'payment_flutterwave_secret_key' => $_POST ? trim($_POST['payment_flutterwave_secret_key'] ?? '') : $defaults['payment_flutterwave_secret_key'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid request token.';
    }
    if ($input['site_name'] === '') {
        $errors[] = 'Site name is required.';
    }
    if (!in_array($input['currency_default'], ['USD','NGN'], true)) {
        $errors[] = 'Invalid default currency.';
    }
    if (!is_numeric($input['currency_rate_usd_to_ngn']) || (float)$input['currency_rate_usd_to_ngn'] <= 0) {
        $errors[] = 'Currency rate must be a positive number.';
    }
    if ($input['payment_manual_enabled']) {
      if ($input['payment_manual_account'] === '') {
        $errors[] = 'Provide bank account details for manual transfers.';
      }
      if ($input['payment_manual_instructions'] === '') {
        $errors[] = 'Provide instructions for uploading transfer proof.';
      }
    }
    if ($input['payment_paystack_enabled']) {
      if ($input['payment_paystack_public_key'] === '' || $input['payment_paystack_secret_key'] === '') {
        $errors[] = 'Enter both Paystack public and secret keys to enable Paystack payments.';
      }
    }
    if ($input['payment_flutterwave_enabled']) {
      if ($input['payment_flutterwave_public_key'] === '' || $input['payment_flutterwave_secret_key'] === '') {
        $errors[] = 'Enter both Flutterwave public and secret keys to enable Flutterwave payments.';
      }
    }
    if (!$errors) {
        set_setting('site_name', $input['site_name']);
        set_setting('currency_default', $input['currency_default']);
        set_setting('currency_rate_usd_to_ngn', $input['currency_rate_usd_to_ngn']);
      set_setting('payment_manual_enabled', (string)$input['payment_manual_enabled']);
      set_setting('payment_manual_account', $input['payment_manual_account']);
      set_setting('payment_manual_instructions', $input['payment_manual_instructions']);
      set_setting('payment_paystack_enabled', (string)$input['payment_paystack_enabled']);
      set_setting('payment_paystack_public_key', $input['payment_paystack_public_key']);
      set_setting('payment_paystack_secret_key', $input['payment_paystack_secret_key']);
      set_setting('payment_flutterwave_enabled', (string)$input['payment_flutterwave_enabled']);
      set_setting('payment_flutterwave_public_key', $input['payment_flutterwave_public_key']);
      set_setting('payment_flutterwave_secret_key', $input['payment_flutterwave_secret_key']);
        $notices[] = 'Settings updated successfully.';
    }
}
?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/sidebar.php'; ?>
<main class="flex-1 flex flex-col bg-white/40 dark:bg-slate-950/30">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <section class="admin-content flex-1 space-y-8 overflow-y-auto px-4 py-6 sm:px-6 lg:px-10" data-admin-content>
    <div>
      <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Website Settings</h1>
      <p class="text-sm text-slate-500 dark:text-slate-400">Control branding and currency behavior.</p>
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
    <form method="post" class="space-y-6">
      <input type="hidden" name="csrf" value="<?= escape_html(generate_csrf()); ?>" />
      <section class="admin-panel rounded-2xl p-6 shadow-soft">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Branding</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Control global brand components used across the UI.</p>
        <label class="mt-4 block text-sm font-semibold text-slate-900 dark:text-white">
          Site Name
          <input type="text" name="site_name" value="<?= escape_html($input['site_name']); ?>" required class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
        </label>
      </section>
      <section class="admin-panel rounded-2xl p-6 shadow-soft">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Currency Settings</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Align storefront pricing with manual FX overrides.</p>
        <div class="mt-4 grid gap-4 md:grid-cols-2">
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Default Currency
            <select name="currency_default" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white">
              <option value="USD" <?= $input['currency_default'] === 'USD' ? 'selected' : ''; ?>>USD</option>
              <option value="NGN" <?= $input['currency_default'] === 'NGN' ? 'selected' : ''; ?>>NGN</option>
            </select>
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            USD → NGN Rate
            <input type="number" step="0.01" name="currency_rate_usd_to_ngn" value="<?= escape_html($input['currency_rate_usd_to_ngn']); ?>" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
            <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">Used to auto-calc NGN price fields.</span>
          </label>
        </div>
      </section>
      <section class="admin-panel rounded-2xl p-6 shadow-soft space-y-6">
        <div>
          <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Payment Methods</h2>
          <p class="text-sm text-slate-500 dark:text-slate-400">Toggle accepted payment channels and store any gateway API keys securely. Manual payments ask buyers to upload transfer proof for approval.</p>
        </div>
        <div class="space-y-6">
          <div class="rounded-2xl border border-white/15 bg-white/40 p-5 dark:border-slate-800/60 dark:bg-slate-900/50">
            <div class="flex flex-wrap items-start justify-between gap-4">
              <div>
                <p class="text-base font-semibold text-slate-900 dark:text-white">Manual Bank Transfer</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">Buyers pay via bank transfer, upload payment proof, and admins confirm manually.</p>
              </div>
              <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                <input type="checkbox" name="payment_manual_enabled" value="1" class="h-5 w-5 rounded border-slate-400 text-primary focus:ring-primary/50" <?= $input['payment_manual_enabled'] ? 'checked' : ''; ?> />
                Enable
              </label>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
              <label class="block text-sm font-semibold text-slate-900 dark:text-white">
                Bank Account Details
                <textarea name="payment_manual_account" rows="3" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" placeholder="e.g. Scriptloaded Ltd.&#10;GTBank - 0123456789" <?= $input['payment_manual_enabled'] ? 'required' : ''; ?>><?= escape_html($input['payment_manual_account']); ?></textarea>
              </label>
              <label class="block text-sm font-semibold text-slate-900 dark:text-white">
                Instructions / Proof Requirements
                <textarea name="payment_manual_instructions" rows="3" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" placeholder="Describe how to upload receipts or screenshots." <?= $input['payment_manual_enabled'] ? 'required' : ''; ?>><?= escape_html($input['payment_manual_instructions']); ?></textarea>
              </label>
            </div>
          </div>
          <div class="rounded-2xl border border-white/15 bg-white/40 p-5 dark:border-slate-800/60 dark:bg-slate-900/50">
            <div class="flex flex-wrap items-start justify-between gap-4">
              <div>
                <p class="text-base font-semibold text-slate-900 dark:text-white">Paystack</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">Collect card and bank payments across Africa with automated order confirmation.</p>
              </div>
              <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                <input type="checkbox" name="payment_paystack_enabled" value="1" class="h-5 w-5 rounded border-slate-400 text-primary focus:ring-primary/50" <?= $input['payment_paystack_enabled'] ? 'checked' : ''; ?> />
                Enable
              </label>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
              <label class="block text-sm font-semibold text-slate-900 dark:text-white">
                Public Key
                <input type="text" name="payment_paystack_public_key" value="<?= escape_html($input['payment_paystack_public_key']); ?>" <?= $input['payment_paystack_enabled'] ? 'required' : ''; ?> class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" placeholder="pk_live_xxx" />
              </label>
              <label class="block text-sm font-semibold text-slate-900 dark:text-white">
                Secret Key
                <input type="text" name="payment_paystack_secret_key" value="<?= escape_html($input['payment_paystack_secret_key']); ?>" <?= $input['payment_paystack_enabled'] ? 'required' : ''; ?> class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" placeholder="sk_live_xxx" />
              </label>
            </div>
          </div>
          <div class="rounded-2xl border border-white/15 bg-white/40 p-5 dark:border-slate-800/60 dark:bg-slate-900/50">
            <div class="flex flex-wrap items-start justify-between gap-4">
              <div>
                <p class="text-base font-semibold text-slate-900 dark:text-white">Flutterwave</p>
                <p class="text-sm text-slate-500 dark:text-slate-400">Accept cards, mobile money, and bank transfers with instant callbacks.</p>
              </div>
              <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-white">
                <input type="checkbox" name="payment_flutterwave_enabled" value="1" class="h-5 w-5 rounded border-slate-400 text-primary focus:ring-primary/50" <?= $input['payment_flutterwave_enabled'] ? 'checked' : ''; ?> />
                Enable
              </label>
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
              <label class="block text-sm font-semibold text-slate-900 dark:text-white">
                Public Key
                <input type="text" name="payment_flutterwave_public_key" value="<?= escape_html($input['payment_flutterwave_public_key']); ?>" <?= $input['payment_flutterwave_enabled'] ? 'required' : ''; ?> class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" placeholder="FLWPUBK-xxxxxxxxx" />
              </label>
              <label class="block text-sm font-semibold text-slate-900 dark:text-white">
                Secret Key
                <input type="text" name="payment_flutterwave_secret_key" value="<?= escape_html($input['payment_flutterwave_secret_key']); ?>" <?= $input['payment_flutterwave_enabled'] ? 'required' : ''; ?> class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" placeholder="FLWSECK-xxxxxxxxx" />
              </label>
            </div>
          </div>
        </div>
      </section>
      <div class="flex justify-end">
        <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-soft hover:bg-primary/90">
          <span class="material-symbols-outlined text-base">save</span>
          Save Changes
        </button>
      </div>
    </form>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
