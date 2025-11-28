<?php
require __DIR__ . '/_bootstrap.php';

$downloadsRaw = fetch_downloads_for_user($pdo, $userProfile['id']);
$downloads = array_map(static function (array $download) {
  return [
    'title' => $download['title'],
    'category' => $download['category'],
    'image' => $download['image'],
    'download_url' => 'download.php?token=' . urlencode($download['token']),
  ];
}, $downloadsRaw);

$pageTitle = 'My Downloads';
$activeNav = 'downloads';

ob_start();
?>
<section class="flex flex-wrap items-center justify-between gap-4">
  <div>
    <p class="text-xs uppercase tracking-[0.3em] text-primary">Secure delivery</p>
    <h2 class="text-4xl font-black text-white">My Downloads</h2>
    <p class="mt-1 text-sm text-gray-400">Tokens refresh automatically every 2 hours.</p>
  </div>
  <div class="flex items-center gap-3 text-sm text-white">
    <span class="material-symbols-outlined text-base text-green-400">lock</span>
    End-to-end encrypted downloads
  </div>
</section>
<section class="mt-6 flex gap-3 overflow-x-auto pb-2 text-sm text-white">
  <button class="inline-flex items-center gap-2 rounded-full bg-primary px-4 py-2 text-white shadow-lg shadow-primary/40">All</button>
  <button class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-gray-300">Themes</button>
  <button class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-gray-300">Plugins</button>
  <button class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-gray-300">UI Kits</button>
  <button class="inline-flex items-center gap-2 rounded-full border border-white/10 px-4 py-2 text-gray-300">Scripts</button>
</section>
<section class="mt-8 rounded-2xl border border-white/10 bg-white/5">
  <div class="hidden overflow-x-auto md:block">
    <table class="w-full min-w-[640px] text-left text-sm">
      <thead class="text-xs uppercase tracking-[0.25em] text-gray-400">
        <tr>
          <th class="px-6 py-4">Product</th>
          <th class="px-6 py-4">Category</th>
          <th class="px-6 py-4 text-right">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-white/10 text-white">
        <?php foreach ($downloads as $download): ?>
          <tr>
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <img src="<?= escape_html($download['image']); ?>" alt="<?= escape_html($download['title']); ?> preview" class="h-12 w-12 rounded-lg object-cover"/>
                <p class="font-semibold text-white"><?= escape_html($download['title']); ?></p>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-300"><?= escape_html($download['category']); ?></td>
            <td class="px-6 py-4 text-right">
              <a href="<?= escape_html($download['download_url']); ?>" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white">
                <span class="material-symbols-outlined text-base">download</span>
                Download
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="space-y-4 p-4 md:hidden">
    <?php foreach ($downloads as $download): ?>
      <article class="rounded-xl border border-white/10 bg-white/5 p-4 text-white">
        <div class="flex items-center gap-3">
          <img src="<?= escape_html($download['image']); ?>" alt="<?= escape_html($download['title']); ?> preview" class="h-14 w-14 rounded-xl object-cover"/>
          <div class="min-w-0">
            <p class="text-xs uppercase tracking-[0.2em] text-primary">Download token</p>
            <p class="text-lg font-semibold text-white leading-tight"><?= escape_html($download['title']); ?></p>
            <p class="text-xs text-gray-400">Category • <?= escape_html($download['category']); ?></p>
          </div>
        </div>
        <div class="mt-4 flex flex-col gap-3">
          <a href="<?= escape_html($download['download_url']); ?>" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-white">
            <span class="material-symbols-outlined text-base">download</span>
            Download now
          </a>
          <p class="text-[11px] text-gray-400">Tokens refresh every 2 hours, so download before expiry.</p>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php
$pageContent = ob_get_clean();
include __DIR__ . '/../templates/user/dashboard_layout.php';
