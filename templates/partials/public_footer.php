<?php
if (!isset($footerLinkGroups) || !is_array($footerLinkGroups)) {
    $currentVisitor = function_exists('current_user') ? current_user() : null;
    $isLoggedIn = (bool) $currentVisitor;
    $isAdminVisitor = $isLoggedIn && !empty($currentVisitor['is_admin']);
    $dashboardHref = $isLoggedIn ? ($isAdminVisitor ? 'admin/index.php' : 'user/dashboard.php') : null;
    $authNavLabel = $isLoggedIn ? 'Dashboard' : 'Login';
    $footerLinkGroups = get_public_footer_link_groups([
        'isLoggedIn' => $isLoggedIn,
        'dashboardHref' => $dashboardHref,
        'authNavLabel' => $authNavLabel,
    ]);
}
?>
<footer class="mt-20 border-t border-white/10 bg-background-dark/60">
  <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-10">
    <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
      <?php foreach ($footerLinkGroups as $group): ?>
        <div>
          <h3 class="mb-4 font-bold text-white"><?= escape_html($group['title']); ?></h3>
          <ul class="space-y-2">
            <?php foreach ($group['links'] as $link): ?>
              <li>
                <a class="text-sm text-gray-400 transition-colors hover:text-white" href="<?= escape_html(site_url($link['href'])); ?>">
                  <?= escape_html($link['label']); ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
      <div>
        <h3 class="mb-4 font-bold text-white">Stay Connected</h3>
        <p class="mb-4 text-sm text-gray-400">Get the latest news and updates.</p>
        <label class="flex h-12 min-w-40 w-full max-w-[480px] flex-col">
          <div class="flex h-full w-full flex-1 items-stretch rounded-lg">
            <input class="form-input flex h-full w-full min-w-0 flex-1 resize-none overflow-hidden rounded-l-lg border border-white/20 bg-white/5 px-[15px] text-sm font-normal leading-normal text-white placeholder:text-gray-400 focus:border-primary focus:outline-0 focus:ring-0" placeholder="Your email..." value="" />
            <button class="flex h-full min-w-[50px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-r-lg bg-primary px-4 text-white transition-colors hover:bg-primary/80">
              <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </button>
          </div>
        </label>
      </div>
    </div>
    <div class="mt-10 border-t border-white/10 pt-8 text-center text-sm text-gray-500">
      <p>© <?= date('Y'); ?> Scriptloaded. All Rights Reserved.</p>
    </div>
  </div>
</footer>
