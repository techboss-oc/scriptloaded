<?php
/** @var array $userProfile */
/** @var array $navLinks */
/** @var string $activeNav */
/** @var string $pageTitle */
/** @var string $pageContent */

$headerNotifications = $headerNotifications ?? [
  ['title' => 'New download ready', 'description' => 'Quantum UI Kit is available.', 'time' => '2m ago', 'icon' => 'download', 'status' => 'unread'],
  ['title' => 'Support reply', 'description' => 'Aisha replied to ticket #4521.', 'time' => '1h ago', 'icon' => 'support_agent', 'status' => 'unread'],
  ['title' => 'Billing reminder', 'description' => 'Invoice INV-2048 was paid.', 'time' => 'Yesterday', 'icon' => 'receipt_long', 'status' => 'read'],
];
$headerProfileLinks = $headerProfileLinks ?? [
  ['label' => 'View Profile', 'href' => 'profile', 'icon' => 'person'],
  ['label' => 'Support', 'href' => 'support', 'icon' => 'support_agent'],
];
$headerLogoutHref = site_url('user/logout');
$unreadNotificationCount = (int)array_reduce($headerNotifications, static function ($carry, $notification) {
  return $carry + (!empty($notification['status']) && strtolower((string)$notification['status']) === 'unread' ? 1 : 0);
}, 0);
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= escape_html($pageTitle); ?> • Scriptloaded</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet"/>
<script>
  tailwind.config = {
    darkMode: 'class',
    theme: {
      extend: {
        colors: {
          primary: '#1A73E8',
          'background-light': '#f6f7f8',
          'background-dark': '#111821',
        },
        fontFamily: {
          display: ['Space Grotesk', 'sans-serif'],
        },
        boxShadow: {
          'neo-light': '6px 6px 12px #d8dade, -6px -6px 12px #ffffff',
          'neo-dark': '6px 6px 12px #0c111b, -6px -6px 12px #161f2d',
        },
      },
    },
  };
</script>
<style>
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
  .dashboard-sidebar {
    transition: width 0.25s ease, padding 0.25s ease;
  }
  body.sidebar-collapsed .dashboard-sidebar {
    width: 80px;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
  }
  .sidebar-label,
  .sidebar-meta,
  .sidebar-user-details,
  .sidebar-collapse-text {
    transition: opacity 0.2s ease;
  }
  body.sidebar-collapsed .sidebar-label,
  body.sidebar-collapsed .sidebar-meta,
  body.sidebar-collapsed .sidebar-user-details,
  body.sidebar-collapsed .sidebar-collapse-text {
    opacity: 0;
    pointer-events: none;
    visibility: hidden;
    display: none;
  }
  body.sidebar-collapsed .dashboard-sidebar .nav-link {
    justify-content: center;
  }
  body.sidebar-collapsed .dashboard-sidebar .nav-link .material-symbols-outlined {
    margin-right: 0;
  }
  body.sidebar-collapsed .sidebar-logo {
    justify-content: center;
  }
  body.sidebar-collapsed .sidebar-collapse-btn {
    justify-content: center;
  }
  body.sidebar-collapsed .sidebar-footer {
    align-items: center;
  }
  body.sidebar-collapsed .sidebar-footer .nav-link {
    justify-content: center;
  }
  .dashboard-popover-panel {
    opacity: 0;
    visibility: hidden;
    transform: translateY(0.5rem);
    pointer-events: none;
    transition: opacity 0.15s ease, transform 0.15s ease, visibility 0.15s ease;
  }
  .dashboard-popover-panel.is-open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
  }
</style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-gray-900 dark:text-gray-100">
<div class="relative flex min-h-screen w-full">
  <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-cyan-400/10 blur-3xl"></div>
  <aside class="dashboard-sidebar sticky top-0 hidden h-screen w-64 flex-shrink-0 border-r border-white/10 bg-white/5 p-5 backdrop-blur-xl dark:bg-black/20 lg:block" data-dashboard-sidebar>
    <div class="flex h-full flex-col">
      <div class="flex items-center gap-3 rounded-2xl bg-white/10 px-3 py-2 text-white sidebar-logo">
        <span class="material-symbols-outlined text-3xl text-primary">hub</span>
        <div class="sidebar-user-details">
          <p class="text-sm text-gray-400 sidebar-meta">Account</p>
          <p class="text-lg font-semibold text-white">Scriptloaded</p>
        </div>
      </div>
      <button type="button" class="sidebar-collapse-btn mt-3 inline-flex items-center gap-2 rounded-xl border border-white/15 px-3 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-gray-300 transition hover:border-primary/40 hover:text-white" data-dashboard-sidebar-toggle>
        <span class="material-symbols-outlined text-base" data-dashboard-sidebar-icon>chevron_left</span>
        <span class="sidebar-collapse-text">Collapse</span>
      </button>
      <div class="mt-6 flex-1 space-y-1">
        <?php foreach ($navLinks as $link): ?>
          <?php $isActive = $link['slug'] === $activeNav; ?>
          <a href="<?= escape_html($link['href']); ?>" class="nav-link flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition <?= $isActive ? 'bg-primary/20 text-white ring-1 ring-primary/50' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' <?= $isActive ? '1' : '0'; ?>;"><?= escape_html($link['icon']); ?></span>
            <span class="sidebar-label"><?= escape_html($link['label']); ?></span>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="sidebar-footer mt-6 border-t border-white/10 pt-4">
        <div class="flex items-center gap-3 sidebar-user-details">
          <img class="h-11 w-11 rounded-full object-cover" src="<?= escape_html($userProfile['avatar']); ?>" alt="<?= escape_html($userProfile['name']); ?>"/>
          <div>
            <p class="font-semibold text-white text-sm sidebar-label"><?= escape_html($userProfile['name']); ?></p>
            <p class="text-xs text-gray-400 sidebar-meta"><?= escape_html($userProfile['email']); ?></p>
          </div>
        </div>
        <a href="<?= escape_html(site_url('user/logout')); ?>" class="nav-link mt-4 flex items-center gap-3 rounded-xl border border-white/10 px-3 py-2 text-sm font-semibold text-gray-300 transition hover:border-primary/60 hover:text-white">
          <span class="material-symbols-outlined text-base">logout</span>
          <span class="sidebar-label">Logout</span>
        </a>
      </div>
    </div>
  </aside>
  <main class="flex-1 overflow-y-auto">
    <header class="sticky top-0 z-10 flex flex-wrap items-center justify-between gap-4 border-b border-white/10 bg-background-light/80 px-4 py-4 backdrop-blur-lg dark:bg-background-dark/80 sm:px-6 relative">
      <div class="flex items-center gap-3 pr-20 md:pr-0">
        <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-white/15 bg-white/5 text-white lg:hidden" aria-label="Open navigation" data-user-mobile-menu-open>
          <span class="material-symbols-outlined">menu</span>
        </button>
        <div>
          <p class="text-xs uppercase tracking-[0.3em] text-primary"><?= escape_html($userProfile['plan']); ?></p>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?= escape_html($pageTitle); ?></h1>
        </div>
      </div>
      <div class="flex items-center gap-3 md:gap-4 absolute right-4 top-4 w-auto md:static md:right-auto md:top-auto">
        <label class="hidden md:flex min-w-[240px] flex-col">
          <div class="flex h-11 items-center rounded-xl border border-white/15 bg-white/10 px-3 text-sm text-gray-300">
            <span class="material-symbols-outlined mr-2 text-lg">search</span>
            <input type="text" placeholder="Search your assets" class="w-full bg-transparent placeholder:text-gray-400 focus:outline-none"/>
          </div>
        </label>
        <div class="relative" data-dashboard-popover="notifications">
          <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/10 text-white transition hover:border-primary/40 hover:text-primary" aria-label="Notifications" data-popover-trigger>
            <span class="material-symbols-outlined">notifications</span>
            <?php if ($unreadNotificationCount > 0): ?>
              <span class="absolute -top-1 -right-1 inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-primary px-1 text-[10px] font-semibold"><?= $unreadNotificationCount; ?></span>
            <?php endif; ?>
          </button>
          <div class="dashboard-popover-panel absolute right-0 top-12 w-80 rounded-2xl border border-white/10 bg-background-dark/95 p-4 shadow-2xl" data-popover-panel>
            <div class="flex items-center justify-between">
              <p class="text-sm font-semibold text-white">Notifications</p>
              <?php if ($unreadNotificationCount > 0): ?>
                <span class="text-xs uppercase tracking-[0.3em] text-primary"><?= $unreadNotificationCount; ?> new</span>
              <?php endif; ?>
            </div>
            <div class="mt-3 space-y-3 max-h-64 overflow-y-auto pr-1">
              <?php if ($headerNotifications): ?>
                <?php foreach ($headerNotifications as $notification): ?>
                  <article class="flex gap-3 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-gray-200">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary/10 text-primary">
                      <span class="material-symbols-outlined text-base"><?= escape_html($notification['icon'] ?? 'notifications'); ?></span>
                    </div>
                    <div class="flex-1">
                      <p class="font-semibold text-white">
                        <?= escape_html($notification['title'] ?? 'Update'); ?>
                      </p>
                      <p class="text-xs text-gray-400">
                        <?= escape_html($notification['description'] ?? ''); ?>
                      </p>
                      <p class="mt-1 text-xs text-gray-500">
                        <?= escape_html($notification['time'] ?? ''); ?>
                      </p>
                    </div>
                    <?php if (!empty($notification['status']) && strtolower((string)$notification['status']) === 'unread'): ?>
                      <span class="mt-1 inline-flex h-2 w-2 rounded-full bg-primary"></span>
                    <?php endif; ?>
                  </article>
                <?php endforeach; ?>
              <?php else: ?>
                <p class="text-xs text-gray-400">You're all caught up.</p>
              <?php endif; ?>
            </div>
            <a href="<?= escape_html(site_url('user/support')); ?>" class="mt-3 inline-flex w-full items-center justify-center rounded-xl border border-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:border-primary/60">View activity</a>
          </div>
        </div>
        <div class="relative" data-dashboard-popover="profile">
          <button type="button" class="inline-flex items-center gap-2 rounded-2xl border border-white/10 bg-white/10 px-2 py-1 pl-1 pr-3 text-sm font-semibold text-white transition hover:border-primary/50" data-popover-trigger>
            <img class="h-10 w-10 rounded-xl object-cover" src="<?= escape_html($userProfile['avatar']); ?>" alt="<?= escape_html($userProfile['name']); ?>"/>
            <div class="hidden text-left md:block">
              <p class="text-sm font-semibold text-white leading-none"><?= escape_html($userProfile['name']); ?></p>
              <p class="text-[11px] text-gray-400 leading-tight"><?= escape_html($userProfile['plan']); ?></p>
            </div>
            <span class="material-symbols-outlined text-base text-gray-400">expand_more</span>
          </button>
          <div class="dashboard-popover-panel absolute right-0 top-14 w-64 rounded-2xl border border-white/10 bg-background-dark/95 p-4 shadow-2xl" data-popover-panel>
            <div class="flex items-center gap-3">
              <img class="h-11 w-11 rounded-xl object-cover" src="<?= escape_html($userProfile['avatar']); ?>" alt="<?= escape_html($userProfile['name']); ?>"/>
              <div>
                <p class="text-sm font-semibold text-white"><?= escape_html($userProfile['name']); ?></p>
                <p class="text-xs text-gray-400"><?= escape_html($userProfile['email']); ?></p>
              </div>
            </div>
            <div class="mt-4 space-y-1 text-sm">
              <?php foreach ($headerProfileLinks as $link): ?>
                <a class="flex items-center gap-2 rounded-xl px-3 py-2 text-gray-300 transition hover:bg-white/10" href="<?= escape_html(site_url($link['href'])); ?>">
                  <span class="material-symbols-outlined text-base"><?= escape_html($link['icon']); ?></span>
                  <?= escape_html($link['label']); ?>
                </a>
              <?php endforeach; ?>
            </div>
            <a class="mt-4 flex items-center justify-center rounded-xl bg-primary px-3 py-2 text-sm font-semibold text-white" href="<?= escape_html($headerLogoutHref); ?>">Logout</a>
          </div>
        </div>
      </div>
    </header>
    <div class="px-4 py-6 sm:px-6 lg:px-10">
      <?= $pageContent; ?>
    </div>
  </main>
</div>
<div class="lg:hidden">
  <div class="fixed inset-0 z-40 bg-black/60 opacity-0 pointer-events-none transition-opacity duration-300" data-user-mobile-menu-overlay></div>
  <nav class="fixed inset-y-0 left-0 z-50 flex w-80 max-w-[85vw] -translate-x-full flex-col border-r border-white/10 bg-background-dark/95 px-5 py-6 shadow-2xl transition-transform duration-300" data-user-mobile-menu-panel>
    <div class="flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img class="h-12 w-12 rounded-full object-cover" src="<?= escape_html($userProfile['avatar']); ?>" alt="<?= escape_html($userProfile['name']); ?>"/>
        <div>
          <p class="text-sm text-gray-400">Logged in as</p>
          <p class="text-base font-semibold text-white"><?= escape_html($userProfile['name']); ?></p>
        </div>
      </div>
      <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/15 text-white" aria-label="Close navigation" data-user-mobile-menu-close>
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <div class="mt-8 flex-1 space-y-1 overflow-y-auto pr-1">
      <?php foreach ($navLinks as $link): ?>
        <?php $isActive = $link['slug'] === $activeNav; ?>
        <a href="<?= escape_html($link['href']); ?>" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition <?= $isActive ? 'bg-primary/20 text-white ring-1 ring-primary/40' : 'text-gray-300 hover:bg-white/10'; ?>" data-user-mobile-menu-link>
          <span class="material-symbols-outlined text-2xl" style="font-variation-settings:'FILL' <?= $isActive ? '1' : '0'; ?>;"><?= escape_html($link['icon']); ?></span>
          <?= escape_html($link['label']); ?>
        </a>
      <?php endforeach; ?>
    </div>
    <div class="mt-6 space-y-3 border-t border-white/10 pt-4">
      <a href="<?= escape_html(site_url('profile')); ?>" class="flex items-center justify-between rounded-xl border border-white/15 px-4 py-3 text-sm font-semibold text-white">
        Profile
        <span class="material-symbols-outlined text-base">chevron_right</span>
      </a>
      <a href="<?= escape_html(site_url('logout')); ?>" class="flex items-center justify-center rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white">
        Logout
      </a>
    </div>
  </nav>
</div>
<script>
  (function () {
    const toggleBtn = document.querySelector('[data-dashboard-sidebar-toggle]');
    const icon = document.querySelector('[data-dashboard-sidebar-icon]');
    if (!toggleBtn || !icon) {
      return;
    }
    const storageKey = 'scriptloadedDashboardSidebarCollapsed';
    const body = document.body;
    const getStored = () => {
      try {
        return localStorage.getItem(storageKey) === '1';
      } catch (e) {
        return false;
      }
    };
    const setStored = (collapsed) => {
      try {
        localStorage.setItem(storageKey, collapsed ? '1' : '0');
      } catch (e) {
        /* no-op */
      }
    };
    const applyState = (collapsed) => {
      body.classList.toggle('sidebar-collapsed', collapsed);
      icon.textContent = collapsed ? 'chevron_right' : 'chevron_left';
      toggleBtn.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
      setStored(collapsed);
    };
    applyState(getStored());
    toggleBtn.addEventListener('click', () => {
      const nextState = !body.classList.contains('sidebar-collapsed');
      applyState(nextState);
    });
  })();

  (function () {
    const openBtn = document.querySelector('[data-user-mobile-menu-open]');
    const closeBtn = document.querySelector('[data-user-mobile-menu-close]');
    const overlay = document.querySelector('[data-user-mobile-menu-overlay]');
    const panel = document.querySelector('[data-user-mobile-menu-panel]');
    if (!openBtn || !overlay || !panel) {
      return;
    }
    const closeMenu = () => {
      overlay.classList.add('pointer-events-none');
      overlay.classList.add('opacity-0');
      overlay.classList.remove('opacity-100');
      panel.classList.add('-translate-x-full');
    };
    const openMenu = () => {
      overlay.classList.remove('pointer-events-none');
      overlay.classList.remove('opacity-0');
      overlay.classList.add('opacity-100');
      panel.classList.remove('-translate-x-full');
    };
    openBtn.addEventListener('click', openMenu);
    overlay.addEventListener('click', closeMenu);
    if (closeBtn) {
      closeBtn.addEventListener('click', closeMenu);
    }
    document.querySelectorAll('[data-user-mobile-menu-link]').forEach((link) => {
      link.addEventListener('click', closeMenu);
    });
  })();

  (function () {
    const popovers = document.querySelectorAll('[data-dashboard-popover]');
    if (!popovers.length) {
      return;
    }
    const closeAll = () => {
      popovers.forEach((group) => {
        const panel = group.querySelector('[data-popover-panel]');
        const trigger = group.querySelector('[data-popover-trigger]');
        panel?.classList.remove('is-open');
        trigger?.setAttribute('aria-expanded', 'false');
      });
    };
    popovers.forEach((group) => {
      const trigger = group.querySelector('[data-popover-trigger]');
      const panel = group.querySelector('[data-popover-panel]');
      if (!trigger || !panel) {
        return;
      }
      trigger.setAttribute('aria-haspopup', 'true');
      trigger.setAttribute('aria-expanded', 'false');
      trigger.addEventListener('click', (event) => {
        event.stopPropagation();
        const isOpen = panel.classList.contains('is-open');
        closeAll();
        if (!isOpen) {
          panel.classList.add('is-open');
          trigger.setAttribute('aria-expanded', 'true');
        }
      });
      panel.addEventListener('click', (event) => {
        event.stopPropagation();
      });
    });
    document.addEventListener('click', closeAll);
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        closeAll();
      }
    });
  })();
</script>
</body>
</html>
