<?php
if (!function_exists('escape_html')) {
    throw new RuntimeException('escape_html must be available before including public_header.php');
}
$publicHeaderMenuId = $publicHeaderMenuId ?? 'primary';
$publicHeaderBrand = $publicHeaderBrand ?? 'Scriptloaded';
$publicHeaderAvatar = $publicHeaderAvatar ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuDEXSGlhY4gQ83osDqErr_529SO2BX7IptvUXdbT6wL195ZoC0DysKUrCewJNsnunv8ftWKuIWtaVtRKYumN7OmzF8qMQJbHs10WcgwBFt2upakAK0WMZJD0hUV8Cm4X2KRAW2YaS0aGUV80YgtLO_HSKIpI1jgf6F6uTF2hd6y-xzXbvSauwIVTALLOINrzbb6vgXTtpEaDNgioAOIOTr_FWRGCr0-InlOahOXchjoQ3vkOG2I_HhUSXrWF5I8VnTDhaRRKJmWnqgz';
$publicHeaderNavClass = $publicHeaderNavClass ?? '';
$publicHeaderOuterClass = $publicHeaderOuterClass ?? '';
$publicHeaderSpacerClass = $publicHeaderSpacerClass ?? 'h-[88px] sm:h-[96px]';
$publicHeaderOuterClass = trim('fixed inset-x-0 top-0 z-50 w-full bg-background-dark/80 backdrop-blur-md/70 px-3 sm:px-6 py-3 ' . $publicHeaderOuterClass);
$publicHeaderWrapperClass = trim('relative mx-auto flex items-center justify-between whitespace-nowrap border-b border-solid border-white/10 px-4 sm:px-6 lg:px-10 py-3 backdrop-blur-md bg-background-dark/30 rounded-xl max-w-6xl w-full ' . $publicHeaderNavClass);
$primaryNavLinks = $primaryNavLinks ?? [];
$mobileNavLinks = $mobileNavLinks ?? [];
$isLoggedIn = $isLoggedIn ?? false;
$isAdminVisitor = $isAdminVisitor ?? false;
$primaryCtaLabel = $primaryCtaLabel ?? 'Register';
$primaryCtaHref = $primaryCtaHref ?? 'user/register.php';
$dashboardHref = $dashboardHref ?? 'user/dashboard.php';
$logoutHref = $logoutHref ?? 'user/logout.php';
$userDashboardHref = $userDashboardHref ?? ($isLoggedIn ? 'user/dashboard.php' : 'user/login.php');
$userDashboardLabel = $isLoggedIn ? 'User Dashboard' : 'User Login';
$adminDashboardHref = $adminDashboardHref ?? 'admin/index.php';
$adminDashboardLabel = 'Admin Dashboard';

if ($isLoggedIn) {
  $primaryNavLinks = array_values(array_filter($primaryNavLinks, static function ($link) {
    $label = strtolower((string)($link['label'] ?? ''));
    return strpos($label, 'dashboard') === false;
  }));
  $mobileNavLinks = array_values(array_filter($mobileNavLinks, static function ($link) {
    $label = strtolower((string)($link['label'] ?? ''));
    return strpos($label, 'dashboard') === false;
  }));
}

?>
<div class="<?= escape_html($publicHeaderOuterClass); ?>" data-public-header>
<header class="<?= escape_html($publicHeaderWrapperClass); ?>">
  <div class="flex items-center gap-4 text-white">
    <div class="size-6 text-primary">
      <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
        <path clip-rule="evenodd" d="M39.475 21.6262C40.358 21.4363 40.6863 21.5589 40.7581 21.5934C40.7876 21.655 40.8547 21.857 40.8082 22.3336C40.7408 23.0255 40.4502 24.0046 39.8572 25.2301C38.6799 27.6631 36.5085 30.6631 33.5858 33.5858C30.6631 36.5085 27.6632 38.6799 25.2301 39.8572C24.0046 40.4502 23.0255 40.7407 22.3336 40.8082C21.8571 40.8547 21.6551 40.7875 21.5934 40.7581C21.5589 40.6863 21.4363 40.358 21.6262 39.475C21.8562 38.4054 22.4689 36.9657 23.5038 35.2817C24.7575 33.2417 26.5497 30.9744 28.7621 28.762C30.9744 26.5497 33.2417 24.7574 35.2817 23.5037C36.9657 22.4689 38.4054 21.8562 39.475 21.6262ZM4.41189 29.2403L18.7597 43.5881C19.8813 44.7097 21.4027 44.9179 22.7217 44.7893C24.0585 44.659 25.5148 44.1631 26.9723 43.4579C29.9052 42.0387 33.2618 39.5667 36.4142 36.4142C39.5667 33.2618 42.0387 29.9052 43.4579 26.9723C44.1631 25.5148 44.659 24.0585 44.7893 22.7217C44.9179 21.4027 44.7097 19.8813 43.5881 18.7597L29.2403 4.41187C27.8527 3.02428 25.8765 3.02573 24.2861 3.36776C22.6081 3.72863 20.7334 4.58419 18.8396 5.74801C16.4978 7.18716 13.9881 9.18353 11.5858 11.5858C9.18354 13.988 7.18717 16.4978 5.74802 18.8396C4.58421 20.7334 3.72865 22.6081 3.36778 24.2861C3.02574 25.8765 3.02429 27.8527 4.41189 29.2403Z" fill="currentColor" fill-rule="evenodd"></path>
      </svg>
    </div>
    <h2 class="text-white text-lg font-bold leading-tight tracking-[-0.015em]"><?= escape_html($publicHeaderBrand); ?></h2>
  </div>
  <div class="hidden md:flex flex-1 justify-end gap-8 items-center">
    <div class="flex items-center gap-9">
      <?php foreach ($primaryNavLinks as $link): ?>
        <a class="text-white text-sm font-medium leading-normal hover:text-primary transition-colors" href="<?= escape_html(site_url($link['href'])); ?>"><?= escape_html($link['label']); ?></a>
      <?php endforeach; ?>
    </div>
    <div class="flex items-center gap-2">
      <?php if ($isLoggedIn): ?>
        <a class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 px-3 py-2 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:border-primary/60 hover:bg-primary/10"
           href="<?= escape_html(site_url($userDashboardHref)); ?>">
          <span class="material-symbols-outlined text-base">dashboard</span>
          <?= escape_html($userDashboardLabel); ?>
        </a>
      <?php else: ?>
        <a class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 px-3 py-2 text-xs font-semibold uppercase tracking-[0.15em] text-white transition hover:border-primary/60 hover:bg-primary/10"
           href="<?= escape_html(site_url('user/login.php')); ?>">
          <span class="material-symbols-outlined text-base">login</span>
          Login
        </a>
      <?php endif; ?>
    </div>
    <?php if (!$isLoggedIn): ?>
      <a class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/80 transition-colors" href="<?= escape_html(site_url($primaryCtaHref)); ?>">
        <span class="truncate"><?= escape_html($primaryCtaLabel); ?></span>
      </a>
    <?php endif; ?>
    <div class="bg-center bg-no-repeat aspect-square bg-cover rounded-full size-10" data-alt="User avatar" style="background-image: url('<?= escape_html($publicHeaderAvatar); ?>');"></div>
  </div>
  <button class="md:hidden inline-flex items-center justify-center rounded-xl border border-white/15 bg-white/5 text-white size-11 transition hover:border-white/40" type="button" aria-expanded="false" aria-controls="mobile-menu-<?= escape_html($publicHeaderMenuId); ?>" data-mobile-menu-toggle="<?= escape_html($publicHeaderMenuId); ?>">
    <span class="material-symbols-outlined text-2xl" data-menu-icon="open">menu</span>
    <span class="material-symbols-outlined text-2xl hidden" data-menu-icon="close">close</span>
  </button>
</header>
</div>
<div aria-hidden="true" class="<?= escape_html($publicHeaderSpacerClass); ?>" data-public-header-spacer></div>
<div class="md:hidden" aria-hidden="true">
  <div class="fixed inset-0 z-40 bg-black/70 opacity-0 pointer-events-none transition-all duration-300" data-mobile-menu-overlay="<?= escape_html($publicHeaderMenuId); ?>"></div>
  <nav id="mobile-menu-<?= escape_html($publicHeaderMenuId); ?>" data-mobile-menu-panel="<?= escape_html($publicHeaderMenuId); ?>" class="fixed inset-x-4 top-6 z-50 origin-top rounded-3xl border border-white/10 bg-background-dark/95 px-6 py-6 shadow-2xl opacity-0 pointer-events-none -translate-y-4 scale-95 transition duration-300">
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="size-10 rounded-2xl bg-primary/20 inline-flex items-center justify-center text-primary">
          <span class="material-symbols-outlined">hub</span>
        </div>
        <div>
          <p class="text-white font-semibold text-base"><?= escape_html($publicHeaderBrand); ?></p>
          <p class="text-gray-400 text-xs">Menu</p>
        </div>
      </div>
      <button class="inline-flex items-center justify-center rounded-full border border-white/15 text-white size-10" type="button" data-mobile-menu-close>
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <div class="flex flex-col gap-4 text-sm font-medium">
      <?php foreach ($mobileNavLinks as $link): ?>
        <a class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3 text-white transition hover:bg-white/10" href="<?= escape_html(site_url($link['href'])); ?>">
          <?= escape_html($link['label']); ?>
          <?php if (!empty($link['icon'])): ?>
            <span class="material-symbols-outlined text-base"><?= escape_html($link['icon']); ?></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
    <div class="mt-6 space-y-4">
      <div class="space-y-2">
        <?php if ($isLoggedIn): ?>
          <a class="flex h-12 w-full items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/5 text-sm font-semibold text-white" href="<?= escape_html(site_url($userDashboardHref)); ?>">
            <span class="material-symbols-outlined text-base">dashboard</span>
            <?= escape_html($userDashboardLabel); ?>
          </a>
        <?php endif; ?>
      </div>
      <?php if ($isLoggedIn): ?>
        <a class="flex h-12 w-full items-center justify-center rounded-2xl border border-white/20 text-white font-semibold" href="<?= escape_html(site_url($logoutHref)); ?>">Logout</a>
      <?php else: ?>
        <a class="w-full h-12 rounded-2xl bg-primary text-white font-semibold tracking-wide shadow-lg shadow-primary/50 flex items-center justify-center" href="<?= escape_html(site_url('user/register.php')); ?>">Register</a>
        <a class="flex h-12 w-full items-center justify-center rounded-2xl border border-white/20 text-white font-semibold" href="<?= escape_html(site_url('user/login.php')); ?>">Login</a>
      <?php endif; ?>
    </div>
  </nav>
</div>
<?php include __DIR__ . '/floating_social.php'; ?>
