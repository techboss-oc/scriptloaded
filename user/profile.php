<?php
require __DIR__ . '/_bootstrap.php';

$profileErrors = [];
$profileSuccess = null;
$notificationSuccess = null;
$submittedForm = $_POST['form'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $formType = $submittedForm ?? 'profile';
  $token = $_POST['csrf_token'] ?? '';
  if (!validate_csrf($token)) {
    $profileErrors[] = 'Invalid request. Please refresh and try again.';
  } else {
    if ($formType === 'profile') {
      $fullName = trim($_POST['full_name'] ?? '');
      $email = trim($_POST['email'] ?? '');
      $location = trim($_POST['location'] ?? '');
      $website = trim($_POST['website'] ?? '');
      $bio = trim($_POST['bio'] ?? '');
      if ($fullName === '') {
        $profileErrors[] = 'Full name is required.';
      }
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $profileErrors[] = 'Enter a valid email address.';
      }
      if ($website && !filter_var($website, FILTER_VALIDATE_URL)) {
        $profileErrors[] = 'Website must be a valid URL.';
      }
      if (!$profileErrors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
        $stmt->execute(['email' => $email, 'id' => $userProfile['id']]);
        if ($stmt->fetch()) {
          $profileErrors[] = 'Another account already uses that email.';
        }
      }
      if (!$profileErrors) {
        $pdo->prepare('UPDATE users SET full_name = :full_name, email = :email WHERE id = :id')->execute([
          'full_name' => $fullName,
          'email' => $email,
          'id' => $userProfile['id'],
        ]);
        upsert_user_profile($pdo, $userProfile['id'], [
          'location' => $location ?: null,
          'website' => $website ?: null,
          'bio' => $bio ?: null,
        ]);
        $profileSuccess = 'Profile updated successfully.';
        $userProfile = build_user_profile($pdo, $userProfile['id']);
      }
    } elseif ($formType === 'notifications') {
      $selected = $_POST['notifications'] ?? [];
      $catalog = get_notification_catalog();
      $enabledMap = [];
      foreach ($catalog as $key => $meta) {
        $enabledMap[$key] = in_array($key, (array)$selected, true);
      }
      save_notification_preferences($pdo, $userProfile['id'], $enabledMap);
      $notificationSuccess = 'Notification preferences saved.';
    }
  }
}

$notificationPreferences = get_notification_preferences_with_defaults($pdo, $userProfile['id']);
$csrfToken = generate_csrf();

$profileFormValues = [
  'full_name' => $userProfile['name'],
  'email' => $userProfile['email'],
  'location' => $userProfile['location'] ?? '',
  'website' => $userProfile['website'] ?? '',
  'bio' => $userProfile['bio'] ?? '',
];
if ($submittedForm === 'profile' && $profileErrors) {
  foreach ($profileFormValues as $key => $value) {
    if (isset($_POST[$key])) {
      $profileFormValues[$key] = trim((string)$_POST[$key]);
    }
  }
}

$pageTitle = 'Profile Settings';
$activeNav = 'profile';

ob_start();
?>
<section class="flex flex-wrap items-center justify-between gap-4">
  <div>
    <p class="text-xs uppercase tracking-[0.3em] text-primary">Account</p>
    <h2 class="text-4xl font-black text-white">Profile & Preferences</h2>
    <p class="mt-1 text-sm text-gray-400">Update your identity, contact details, and notification rules.</p>
  </div>
  <button type="button" class="inline-flex items-center gap-2 rounded-xl border border-white/15 px-4 py-2 text-sm font-semibold text-white">
    <span class="material-symbols-outlined text-base">download</span>
    Export profile
  </button>
</section>
<section class="mt-10 grid gap-8 lg:grid-cols-3">
  <article class="lg:col-span-2 rounded-2xl border border-white/10 bg-white/5 p-6">
    <h3 class="text-2xl font-semibold text-white">Personal information</h3>
    <form class="mt-6 space-y-4" method="post" action="#">
      <input type="hidden" name="csrf_token" value="<?= escape_html($csrfToken); ?>"/>
      <input type="hidden" name="form" value="profile"/>
      <?php if ($profileErrors): ?>
        <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-3 text-sm text-red-100">
          <?php foreach ($profileErrors as $error): ?>
            <p><?= escape_html($error); ?></p>
          <?php endforeach; ?>
        </div>
      <?php elseif ($profileSuccess): ?>
        <div class="rounded-xl border border-green-500/30 bg-green-500/10 p-3 text-sm text-green-100">
          <?= escape_html($profileSuccess); ?>
        </div>
      <?php endif; ?>
      <div class="grid gap-4 md:grid-cols-2">
        <label class="text-sm text-gray-400">Full name
          <input type="text" name="full_name" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white" value="<?= escape_html($profileFormValues['full_name']); ?>"/>
        </label>
        <label class="text-sm text-gray-400">Email address
          <input type="email" name="email" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white" value="<?= escape_html($profileFormValues['email']); ?>"/>
        </label>
      </div>
      <div class="grid gap-4 md:grid-cols-2">
        <label class="text-sm text-gray-400">Location
          <input type="text" name="location" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white" value="<?= escape_html($profileFormValues['location']); ?>"/>
        </label>
        <label class="text-sm text-gray-400">Portfolio / Website
          <input type="url" name="website" class="mt-1 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white" placeholder="https://" value="<?= escape_html($profileFormValues['website']); ?>"/>
        </label>
      </div>
      <label class="text-sm text-gray-400">Bio
        <textarea name="bio" rows="4" class="mt-1 w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-white" placeholder="Tell buyers about your workflow."><?= escape_html($profileFormValues['bio']); ?></textarea>
      </label>
      <button type="submit" class="w-full rounded-xl bg-primary py-3 text-sm font-semibold uppercase tracking-[0.2em] text-white shadow-lg shadow-primary/40">Save profile</button>
    </form>
  </article>
  <div class="space-y-6">
    <article class="rounded-2xl border border-white/10 bg-white/5 p-6">
      <form method="post" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?= escape_html($csrfToken); ?>"/>
        <input type="hidden" name="form" value="notifications"/>
        <h4 class="text-xl font-semibold text-white">Notification preferences</h4>
        <p class="text-sm text-gray-400">Choose which emails reach your inbox.</p>
        <?php if ($notificationSuccess): ?>
          <div class="rounded-xl border border-green-500/30 bg-green-500/10 p-3 text-sm text-green-100">
            <?= escape_html($notificationSuccess); ?>
          </div>
        <?php endif; ?>
        <div class="space-y-4">
          <?php foreach ($notificationPreferences as $preference): ?>
            <label class="flex items-start gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 text-white">
              <input type="checkbox" name="notifications[]" value="<?= escape_html($preference['key']); ?>" <?= $preference['enabled'] ? 'checked' : ''; ?> class="mt-1 h-4 w-4 rounded border-white/20 bg-transparent"/>
              <div>
                <p class="font-semibold"><?= escape_html($preference['label']); ?></p>
                <p class="text-sm text-gray-400"><?= escape_html($preference['description']); ?></p>
              </div>
            </label>
          <?php endforeach; ?>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 rounded-xl border border-white/10 px-4 py-2 text-sm font-semibold text-white">Save notifications</button>
      </form>
    </article>
  </div>
</section>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/../templates/user/dashboard_layout.php';
