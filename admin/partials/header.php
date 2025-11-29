<?php
$adminPageTitle = $adminPageTitle ?? 'Admin';
$sidebarLinks = $sidebarLinks ?? require __DIR__ . '/sidebar_links.php';
$adminMobileUser = current_user();
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8" />
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<title><?= escape_html($adminPageTitle); ?> - Scriptloaded Admin</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<script>
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          primary: "#1A73E8",
          "primary-light": "#4285F4",
          "background-light": "#f0f2f5",
          "background-dark": "#0d1117",
          "text-light-primary": "#1f2937",
          "text-dark-primary": "#e5e7eb",
          "border-light": "rgba(255,255,255,0.3)",
          "border-dark": "rgba(51,65,85,0.5)",
        },
        fontFamily: {
          display: ["Space Grotesk", "sans-serif"],
        },
        boxShadow: {
          soft: "0 10px 25px rgba(0,0,0,0.05)",
          "soft-dark": "0 20px 45px rgba(0,0,0,0.3)",
        },
      },
    },
  };
</script>
<style>
  :root {
    --admin-bg: #040914;
    --admin-bg-soft: #060d1e;
    --admin-surface: rgba(7, 13, 28, 0.92);
    --admin-surface-soft: rgba(12, 19, 39, 0.75);
    --admin-surface-highlight: rgba(20, 85, 197, 0.2);
    --admin-border: rgba(255, 255, 255, 0.08);
    --admin-border-strong: rgba(66, 153, 255, 0.45);
    --admin-text: #f8fbff;
    --admin-muted: #9cb6d9;
    --admin-accent: #4f9bff;
    --admin-accent-strong: #7c4dff;
  }
  body.admin-theme {
    background: radial-gradient(circle at 20% 20%, rgba(79, 155, 255, 0.18), transparent 45%),
                radial-gradient(circle at 80% 10%, rgba(124, 77, 255, 0.18), transparent 40%),
                radial-gradient(circle at 70% 80%, rgba(18, 116, 244, 0.12), transparent 45%),
                var(--admin-bg);
    color: var(--admin-text);
  }
  body.admin-theme .text-slate-300,
  body.admin-theme .text-slate-400,
  body.admin-theme .text-slate-500,
  body.admin-theme .text-slate-600,
  body.admin-theme .dark\:text-slate-400,
  body.admin-theme .dark\:text-slate-300,
  body.admin-theme .text-gray-400,
  body.admin-theme .text-gray-300 {
    color: var(--admin-muted);
  }
  body.admin-theme .text-slate-700,
  body.admin-theme .text-slate-800,
  body.admin-theme .text-slate-900,
  body.admin-theme .dark\:text-white,
  body.admin-theme .text-gray-900,
  body.admin-theme .dark\:text-gray-100 {
    color: var(--admin-text);
  }
  body.admin-theme .placeholder\:text-slate-400::placeholder,
  body.admin-theme .placeholder\:text-slate-500::placeholder,
  body.admin-theme .placeholder\:text-slate-600::placeholder {
    color: var(--admin-muted);
  }
  .glassmorphism {
    background: linear-gradient(145deg, rgba(11, 19, 38, 0.9), rgba(5, 10, 23, 0.75));
    backdrop-filter: blur(26px);
    border: 1px solid var(--admin-border);
  }
  .glass-panel {
    background: var(--admin-surface-soft);
    border: 1px solid var(--admin-border);
    backdrop-filter: blur(18px);
  }
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
  .admin-sidebar {
    transition: width 0.25s ease, padding 0.25s ease;
  }
  body.admin-sidebar-collapsed .admin-sidebar {
    width: 88px;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
  }
  body.admin-sidebar-collapsed .admin-sidebar .admin-sidebar-hideable {
    opacity: 0;
    pointer-events: none;
    visibility: hidden;
    display: none;
  }
  body.admin-sidebar-collapsed .admin-sidebar .admin-sidebar-link {
    justify-content: center;
  }
  body.admin-sidebar-collapsed .admin-sidebar .admin-sidebar-link .material-symbols-outlined {
    margin-right: 0;
  }
  body.admin-sidebar-collapsed .admin-sidebar .admin-sidebar-footer {
    align-items: center;
  }
  body.admin-sidebar-collapsed .admin-sidebar .admin-sidebar-footer .admin-sidebar-link {
    justify-content: center;
  }
  .admin-popover-panel {
    opacity: 0;
    visibility: hidden;
    transform: translateY(0.5rem);
    pointer-events: none;
    transition: opacity 0.15s ease, transform 0.15s ease, visibility 0.15s ease;
  }
  .admin-popover-panel.is-open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
  }
  .admin-content {
    width: 100%;
    max-width: 100%;
  }
  @media (max-width: 640px) {
    .admin-content {
      padding-left: 1.25rem !important;
      padding-right: 1.25rem !important;
      padding-top: 1.5rem !important;
    }
    .admin-content .rounded-2xl,
    .admin-content .rounded-xl {
      border-radius: 1.2rem;
    }
  }
  .admin-panel {
    border-radius: 1.5rem;
    border: 1px solid var(--admin-border);
    background: radial-gradient(circle at 15% 20%, rgba(79, 155, 255, 0.12), transparent 55%),
                radial-gradient(circle at 90% 10%, rgba(124, 77, 255, 0.15), transparent 50%),
                rgba(6, 12, 29, 0.95);
    box-shadow: 0 25px 45px rgba(5, 8, 26, 0.45);
  }
  html:not(.dark) .admin-panel {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(231, 238, 255, 0.95));
    border-color: rgba(15, 23, 42, 0.12);
    box-shadow: 0 20px 35px rgba(15, 23, 42, 0.15);
  }
  .admin-table {
    width: 100%;
    border-collapse: separate;
  }
  .admin-table th,
  .admin-table td {
    vertical-align: top;
  }
  @media (max-width: 768px) {
    .admin-table {
      display: block;
      width: 100%;
      max-width: 100%;
    }
    .admin-table thead {
      display: none;
    }
    .admin-table tbody {
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
      width: 100%;
    }
    .admin-table tbody tr {
      display: block;
      width: 100%;
      border-radius: 1.25rem;
      padding: 1.15rem 1.25rem;
      border: 1px solid rgba(255, 255, 255, 0.12);
      background: rgba(8, 13, 24, 0.92);
      box-shadow: 0 25px 45px rgba(5, 8, 20, 0.4);
    }
    html:not(.dark) .admin-table tbody tr {
      background: rgba(248, 250, 255, 0.98);
      border-color: rgba(15, 23, 42, 0.08);
      box-shadow: 0 15px 30px rgba(15, 23, 42, 0.12);
    }
    .admin-table tbody td {
      display: flex;
      flex-direction: column;
      gap: 0.55rem;
      padding: 0;
      border: 0;
      width: 100%;
      font-size: 0.96rem;
    }
    .admin-table tbody td::before {
      content: attr(data-label);
      font-size: 0.72rem;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: rgba(148, 163, 184, 1);
    }
    .admin-table tbody td[colspan]::before {
      content: '';
      display: none;
    }
    html:not(.dark) .admin-table tbody td::before {
      color: rgba(71, 85, 105, 1);
    }
    .admin-table tbody td > *:only-child {
      width: 100%;
    }
    .admin-table tbody form,
    .admin-table tbody .inline-flex,
    .admin-table tbody .flex,
    .admin-table tbody .space-y-2 {
      width: 100%;
    }
  }
</style>
</head>
<body class="admin-theme bg-background-light dark:bg-background-dark font-display text-[var(--admin-text)]">
<div class="relative flex min-h-screen w-full flex-col">
  <div class="lg:hidden">
    <div class="fixed inset-0 z-40 bg-black/70 opacity-0 pointer-events-none transition-opacity duration-300" data-admin-mobile-menu-overlay></div>
    <nav id="admin-mobile-menu" class="fixed inset-y-0 left-0 z-50 flex w-80 max-w-[85vw] -translate-x-full flex-col border-r border-white/10 bg-background-dark/95 px-5 py-6 shadow-2xl transition-transform duration-300" data-admin-mobile-menu-panel>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="size-11 rounded-2xl bg-primary/20 inline-flex items-center justify-center text-primary">
            <span class="material-symbols-outlined">hub</span>
          </div>
          <div>
            <p class="text-white font-semibold text-base">Scriptloaded</p>
            <p class="text-xs text-gray-400">Admin Panel</p>
          </div>
        </div>
        <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/15 text-white" data-admin-mobile-menu-close aria-label="Close navigation">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>
      <div class="mt-8 flex-1 space-y-1 overflow-y-auto pr-1">
        <?php foreach ($sidebarLinks as $link): ?>
          <?php $isActive = ($activeNav ?? '') === $link['key']; ?>
          <a href="<?= escape_html($link['href']); ?>" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition <?= $isActive ? 'bg-primary/20 text-white ring-1 ring-primary/40' : 'text-gray-300 hover:bg-white/10'; ?>" data-admin-mobile-menu-link>
            <span class="material-symbols-outlined" style="font-variation-settings:'FILL' <?= $isActive ? '1' : '0'; ?>;"><?= escape_html($link['icon']); ?></span>
            <?= escape_html($link['label']); ?>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="mt-4 space-y-3 border-t border-white/10 pt-4">
        <div class="flex items-center gap-3">
          <?php $mobileAvatar = $adminMobileUser['avatar'] ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuDxUTPXd_eg3vwKf982szq5jX7PfLVArFh_i-MPmUB3IEAAf4XjtXbpmwYF7KFjziiiulNfQSfO3sfqCQPIfqPtarm1ZFzEC-oi6JWDDh4QZF_t490gYBpC3oLsez6pzS01ld8kqPfXPr5wK-WM79Zr-_FZSZqZQcYcGzaSx7LWkAmajQNQYnpylqVLOAnROcQ2bwhr9GKbqwFNDxenBwfAnqXKAOir2dRxMyk-r0Is01C21zH5yc0zTf-Ce9s1_tNHglsrVAdwAvvb'; ?>
          <img class="h-11 w-11 rounded-full object-cover" src="<?= escape_html($mobileAvatar); ?>" alt="<?= escape_html($adminMobileUser['full_name'] ?? 'Admin'); ?>">
          <div>
            <p class="text-sm font-semibold text-white"><?= escape_html($adminMobileUser['full_name'] ?? 'Scriptloaded Admin'); ?></p>
            <p class="text-xs text-gray-400"><?= escape_html($adminMobileUser['email'] ?? 'admin@scriptloaded.test'); ?></p>
          </div>
        </div>
        <a href="<?= escape_html(site_url('admin/settings.php')); ?>" class="flex items-center justify-between rounded-xl border border-white/15 px-4 py-3 text-sm font-semibold text-white">
          Profile
          <span class="material-symbols-outlined text-base">chevron_right</span>
        </a>
        <a href="<?= escape_html(site_url('admin/auth.php?action=logout')); ?>" class="flex items-center justify-center rounded-xl bg-primary px-4 py-3 text-sm font-semibold text-white">
          Logout
        </a>
      </div>
    </nav>
  </div>
  <div class="flex h-full min-h-screen w-full lg:gap-6">
