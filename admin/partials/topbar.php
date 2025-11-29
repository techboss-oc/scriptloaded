<?php
$user = current_user();
$adminNotifications = $adminNotifications ?? [
  ['title' => 'New order received', 'description' => 'Order #1042 was just completed.', 'time' => '2m ago'],
  ['title' => 'Ticket waiting', 'description' => 'Creator needs help with deployment.', 'time' => '18m ago'],
  ['title' => 'Product pending review', 'description' => 'New SaaS dashboard template uploaded.', 'time' => '1h ago'],
];
?>
<header class="sticky top-0 z-10 flex flex-wrap items-center gap-4 border-b border-white/10 bg-gradient-to-r from-slate-900/60 via-slate-900/40 to-transparent px-4 py-4 backdrop-blur sm:px-6">
  <div class="flex w-full items-center gap-3">
    <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-white/20 bg-white/5 text-white transition hover:border-[#4f9bff]/60 hover:text-[#4f9bff] lg:hidden" data-admin-mobile-menu-open aria-label="Open navigation" aria-controls="admin-mobile-menu">
      <span class="material-symbols-outlined">menu</span>
    </button>
    <div class="flex-1 flex items-center gap-3 min-w-[200px] max-w-xl">
      <label class="relative flex-1">
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-white/60">search</span>
        <input type="search" name="q" placeholder="Search products, orders, users..." class="w-full rounded-2xl border border-white/15 bg-white/5 py-3 pl-12 pr-4 text-sm text-white placeholder:text-white/40 focus:border-[#4f9bff] focus:outline-none focus:ring-2 focus:ring-[#4f9bff]/40" />
      </label>
      <div class="relative" data-admin-popover="notifications">
        <button class="relative flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-white/5 text-white transition hover:border-[#4f9bff]/60 hover:text-[#4f9bff]" type="button" data-popover-trigger aria-haspopup="true" aria-expanded="false">
          <span class="material-symbols-outlined">notifications</span>
          <span class="absolute top-2 right-2 inline-flex h-2 w-2 rounded-full bg-cyan-400"></span>
        </button>
        <div class="admin-popover-panel absolute right-0 top-14 w-80 rounded-2xl border border-white/10 bg-[#060d21]/95 p-4 shadow-2xl" data-popover-panel>
          <div class="flex items-center justify-between">
            <p class="text-sm font-semibold text-white">Notifications</p>
            <a href="<?= escape_html(site_url('admin/support')); ?>" class="text-xs font-semibold text-primary hover:underline">View all</a>
          </div>
          <div class="mt-3 space-y-3 max-h-64 overflow-y-auto pr-1">
            <?php foreach ($adminNotifications as $note): ?>
              <article class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-gray-200">
                <p class="font-semibold text-white"><?= escape_html($note['title']); ?></p>
                <p class="text-xs text-gray-400"><?= escape_html($note['description']); ?></p>
                <p class="mt-1 text-[11px] uppercase tracking-[0.2em] text-primary">
                  <?= escape_html($note['time']); ?>
                </p>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="flex w-full flex-wrap items-center justify-end gap-3 lg:w-auto">
    <div class="flex items-center gap-3 rounded-full border border-white/15 bg-white/5 px-3 py-2">
      <div class="hidden md:block h-10 w-10 rounded-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuAG4Ba7KtqHHov9vll9R8JEWGDPWR_L0vNGQcMpK1nBdasGUaPmr-dQzQINTbZfej0xxTr06JamEnO0pW-8ubtJ0UpzGeJaR5mMAsJTreNH8ti3n4cl7iHf1VbH9JeryertIDt0UNAmqOfwisXBgsQfVK8LaB2UvXbsk7Spto87H1y8F6jYoCIdsfJwj1noHjGV769XBImHDQEIRupc0KmbCvSQbLWDTSJTdqY8kMLlP_S9RzCBPWuZj_uk4wFgjTcwruyGBIB5_4RP');"></div>
      <div class="hidden text-left md:block">
        <a href="<?= escape_html(site_url('admin/settings')); ?>" class="text-sm font-semibold text-white transition hover:text-[#4f9bff]">
          <?= escape_html($user['full_name'] ?? 'Scriptloaded Admin'); ?>
        </a>
        <a href="mailto:<?= escape_html($user['email'] ?? 'admin@scriptloaded.test'); ?>" class="text-xs text-white/50 transition hover:text-[#4f9bff]">
          <?= escape_html($user['email'] ?? 'admin@scriptloaded.test'); ?>
        </a>
      </div>
    </div>
    <a href="<?= escape_html(site_url('admin/auth?action=logout')); ?>" class="hidden lg:inline-flex items-center gap-2 rounded-full border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-sm font-semibold text-rose-100 transition hover:bg-rose-500/20">
      <span class="material-symbols-outlined text-base">logout</span>
      Logout
    </a>
  </div>
</header>
