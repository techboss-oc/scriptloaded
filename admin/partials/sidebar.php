<?php
$activeNav = $activeNav ?? '';
$user = current_user();
$sidebarLinks = $sidebarLinks ?? require __DIR__ . '/sidebar_links.php';
?>
<aside class="admin-sidebar relative z-30 hidden lg:flex lg:w-64 flex-shrink-0 p-4" data-admin-sidebar>
  <div class="flex h-full flex-col gap-6 rounded-2xl glassmorphism p-4 border border-black/5 dark:border-white/5">
    <div class="flex items-center gap-3 px-2">
      <svg class="h-10 w-10 text-primary" fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
        <path clip-rule="evenodd" d="M39.475 21.6262C40.358 21.4363 40.6863 21.5589 40.7581 21.5934C40.7876 21.655 40.8547 21.857 40.8082 22.3336C40.7408 23.0255 40.4502 24.0046 39.8572 25.2301C38.6799 27.6631 36.5085 30.6631 33.5858 33.5858C30.6631 36.5085 27.6632 38.6799 25.2301 39.8572C24.0046 40.4502 23.0255 40.7407 22.3336 40.8082C21.8571 40.8547 21.6551 40.7875 21.5934 40.7581C21.5589 40.6863 21.4363 40.358 21.6262 39.475C21.8562 38.4054 22.4689 36.9657 23.5038 35.2817C24.7575 33.2417 26.5497 30.9744 28.7621 28.762C30.9744 26.5497 33.2417 24.7574 35.2817 23.5037C36.9657 22.4689 38.4054 21.8562 39.475 21.6262ZM4.41189 29.2403L18.7597 43.5881C19.8813 44.7097 21.4027 44.9179 22.7217 44.7893C24.0585 44.659 25.5148 44.1631 26.9723 43.4579C29.9052 42.0387 33.2618 39.5667 36.4142 36.4142C39.5667 33.2618 42.0387 29.9052 43.4579 26.9723C44.1631 25.5148 44.659 24.0585 44.7893 22.7217C44.9179 21.4027 44.7097 19.8813 43.5881 18.7597L29.2403 4.41187C27.8527 3.02428 25.8765 3.02573 24.2861 3.36776C22.6081 3.72863 20.7334 4.58419 18.8396 5.74801C16.4978 7.18716 13.9881 9.18353 11.5858 11.5858C9.18354 13.988 7.18717 16.4978 5.74802 18.8396C4.58421 20.7334 3.72865 22.6081 3.36778 24.2861C3.02574 25.8765 3.02429 27.8527 4.41189 29.2403Z" fill="currentColor" fill-rule="evenodd"></path>
      </svg>
      <div class="admin-sidebar-hideable">
        <p class="text-base font-bold text-white">Scriptloaded</p>
        <p class="text-xs text-white/60">Admin Panel</p>
      </div>
    </div>
    <button type="button" class="admin-sidebar-link flex items-center gap-2 rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-white/70 transition hover:border-primary/40" data-admin-sidebar-toggle>
      <span class="material-symbols-outlined text-base" data-admin-sidebar-icon>chevron_left</span>
      <span class="admin-sidebar-hideable">Collapse</span>
    </button>
    <nav class="flex flex-col gap-1">
      <?php foreach ($sidebarLinks as $link): ?>
        <?php $isActive = $activeNav === $link['key']; ?>
        <a href="<?= escape_html($link['href']); ?>" class="admin-sidebar-link flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition <?php if ($isActive): ?>bg-primary/20 text-white<?php else: ?>text-white/70 hover:text-white hover:bg-white/10<?php endif; ?>">
          <span class="material-symbols-outlined" <?php if ($isActive): ?>style="font-variation-settings: 'FILL' 1"<?php endif; ?>><?= $link['icon']; ?></span>
          <span class="admin-sidebar-hideable"><?= escape_html($link['label']); ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="admin-sidebar-footer mt-auto flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 p-3 text-white">
      <div class="h-12 w-12 rounded-full bg-cover bg-center" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDxUTPXd_eg3vwKf982szq5jX7PfLVArFh_i-MPmUB3IEAAf4XjtXbpmwYF7KFjziiiulNfQSfO3sfqCQPIfqPtarm1ZFzEC-oi6JWDDh4QZF_t490gYBpC3oLsez6pzS01ld8kqPfXPr5wK-WM79Zr-_FZSZqZQcYcGzaSx7LWkAmajQNQYnpylqVLOAnROcQ2bwhr9GKbqwFNDxenBwfAnqXKAOir2dRxMyk-r0Is01C21zH5yc0zTf-Ce9s1_tNHglsrVAdwAvvb');"></div>
      <div class="admin-sidebar-hideable">
        <p class="text-sm font-semibold"><?= escape_html($user['full_name'] ?? 'Admin'); ?></p>
        <p class="text-xs text-white/70"><?= escape_html($user['email'] ?? ''); ?></p>
      </div>
    </div>
  </div>
</aside>
