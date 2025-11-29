<?php
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/helpers.php';
require __DIR__ . '/../inc/auth.php';
require __DIR__ . '/../inc/csrf.php';

require_admin();

$adminPageTitle = 'Community Links';
$activeNav = 'community';
$errors = [];
$notices = [];

$channelPresets = [
    'whatsapp_direct' => [
        'title' => 'WhatsApp Direct Chat',
        'description' => 'One-to-one chat with the Scriptloaded success team.',
        'placeholder' => 'https://wa.me/2348012345678?text=Hello%20Scriptloaded',
        'icon' => 'headset_mic',
    ],
    'whatsapp_group' => [
        'title' => 'WhatsApp Group',
        'description' => 'Community space for product drops, tips, and Q&A.',
        'placeholder' => 'https://chat.whatsapp.com/your-group-link',
        'icon' => 'group',
    ],
    'whatsapp_channel' => [
        'title' => 'WhatsApp Channel',
        'description' => 'Broadcast-only channel for launch alerts.',
        'placeholder' => 'https://whatsapp.com/channel/XXXXXXXXXXXXX',
        'icon' => 'campaign',
    ],
    'telegram_group' => [
        'title' => 'Telegram Group',
        'description' => 'Long-form discussions with our engineering team.',
        'placeholder' => 'https://t.me/your-telegram-group',
        'icon' => 'send',
    ],
    'youtube_channel' => [
        'title' => 'YouTube Channel',
        'description' => 'Deep dives, demos, and release walkthroughs.',
        'placeholder' => 'https://www.youtube.com/@scriptloaded',
        'icon' => 'play_circle',
    ],
];

$channels = [];
foreach ($channelPresets as $key => $meta) {
    $channels[$key] = [
        'enabled' => $_POST
            ? (isset($_POST['links'][$key]['enabled']) ? 1 : 0)
            : (int)get_setting('social_' . $key . '_enabled', $key === 'whatsapp_direct' ? '1' : '0'),
        'url' => $_POST
            ? trim($_POST['links'][$key]['url'] ?? '')
            : (string)get_setting('social_' . $key . '_url', ''),
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid request token. Please try again.';
    }
    foreach ($channels as $key => $channel) {
        if ($channel['enabled']) {
            $url = $channel['url'];
            if ($url === '') {
                $errors[] = $channelPresets[$key]['title'] . ' link is required when enabled.';
                continue;
            }
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $errors[] = $channelPresets[$key]['title'] . ' must be a valid URL.';
            }
        }
    }
    if (!$errors) {
        foreach ($channels as $key => $channel) {
            set_setting('social_' . $key . '_enabled', (string)$channel['enabled']);
            set_setting('social_' . $key . '_url', $channel['url']);
        }
        $notices[] = 'Community links updated successfully.';
    }
}
?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/sidebar.php'; ?>
<main class="flex-1 flex flex-col bg-transparent">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <section class="admin-content flex-1 space-y-8 overflow-y-auto px-4 py-6 sm:px-6 lg:px-10" data-admin-content>
    <div>
      <h1 class="text-3xl font-bold text-white">Community Links</h1>
      <p class="text-sm text-white/70">Control the floating WhatsApp, Telegram, and YouTube shortcuts that appear across the site.</p>
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
      <div class="grid gap-6 xl:grid-cols-2">
        <?php foreach ($channelPresets as $key => $meta): $channel = $channels[$key]; ?>
          <section class="admin-panel rounded-2xl p-6 shadow-soft dark:shadow-soft-dark">
            <div class="flex items-start justify-between gap-4">
              <div>
                <p class="text-base font-semibold text-white flex items-center gap-2">
                  <span class="material-symbols-outlined text-lg text-primary" style="font-variation-settings:'FILL' 1;"><?= escape_html($meta['icon']); ?></span>
                  <?= escape_html($meta['title']); ?>
                </p>
                <p class="mt-1 text-sm text-white/70"><?= escape_html($meta['description']); ?></p>
              </div>
              <label class="inline-flex items-center gap-2 text-sm font-semibold text-white">
                <input type="checkbox" name="links[<?= escape_html($key); ?>][enabled]" value="1" class="h-5 w-5 rounded border-white/30 bg-transparent text-primary focus:ring-primary/50" <?= $channel['enabled'] ? 'checked' : ''; ?> />
                Enable
              </label>
            </div>
            <label class="mt-4 block text-sm font-semibold text-white">
              Link URL
              <input type="url" name="links[<?= escape_html($key); ?>][url]" value="<?= escape_html($channel['url']); ?>" placeholder="<?= escape_html($meta['placeholder']); ?>" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" <?= $channel['enabled'] ? 'required' : ''; ?> />
            </label>
          </section>
        <?php endforeach; ?>
      </div>
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
