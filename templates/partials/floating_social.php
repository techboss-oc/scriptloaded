<?php
if (defined('SCRIPTLOADED_FLOATING_SOCIAL_RENDERED')) {
    return;
}
define('SCRIPTLOADED_FLOATING_SOCIAL_RENDERED', true);

$channels = [
    [
        'key' => 'whatsapp_direct',
        'label' => 'WhatsApp Chat',
        'description' => 'Talk to Scriptloaded',
        'icon' => 'headset_mic',
        'gradient' => 'from-[#25D366] to-[#128C7E]',
    ],
    [
        'key' => 'whatsapp_group',
        'label' => 'WhatsApp Group',
        'description' => 'Connect with creators',
        'icon' => 'group',
        'gradient' => 'from-[#0B7742] to-[#075E3C]',
    ],
    [
        'key' => 'whatsapp_channel',
        'label' => 'WhatsApp Channel',
        'description' => 'Broadcast updates',
        'icon' => 'campaign',
        'gradient' => 'from-[#1FAA59] to-[#0B7742]',
    ],
    [
        'key' => 'telegram_group',
        'label' => 'Telegram Group',
        'description' => 'Join the dev chat',
        'icon' => 'send',
        'gradient' => 'from-[#37AEE2] to-[#1E88E5]',
    ],
    [
        'key' => 'youtube_channel',
        'label' => 'YouTube Channel',
        'description' => 'Tutorials & launches',
        'icon' => 'play_circle',
        'gradient' => 'from-[#C62828] to-[#8E0000]',
    ],
];

$directLink = null;
$sideLinks = [];
foreach ($channels as $channel) {
    $key = $channel['key'];
    $urlKey = 'social_' . $key . '_url';
    $enabledKey = 'social_' . $key . '_enabled';
    $url = trim((string)get_setting($urlKey, ''));
    $enabled = (int)get_setting($enabledKey, $key === 'whatsapp_direct' ? '1' : '0');
    if (!$enabled || $url === '') {
        continue;
    }
    $linkData = [
        'url' => $url,
        'label' => $channel['label'],
        'description' => $channel['description'],
        'icon' => $channel['icon'],
        'gradient' => $channel['gradient'],
        'key' => $key,
    ];
    if ($key === 'whatsapp_direct') {
        $directLink = $linkData;
    } else {
        $sideLinks[] = $linkData;
    }
}

if (!$directLink && !$sideLinks) {
    return;
}
?>
<?php if ($directLink): ?>
  <div class="fixed bottom-5 right-4 z-[70] pointer-events-none">
    <div class="pointer-events-auto">
      <a href="<?= escape_html($directLink['url']); ?>" target="_blank" rel="noreferrer" class="group flex h-14 w-14 items-center justify-center rounded-full border border-white/20 bg-gradient-to-br from-[#25D366] to-[#128C7E] text-white shadow-2xl transition-transform hover:scale-105" aria-label="<?= escape_html($directLink['label']); ?>" title="<?= escape_html($directLink['label']); ?>">
        <span aria-hidden="true" class="inline-flex items-center justify-center">
          <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 fill-current">
            <path d="M27.61 4.39A14 14 0 0 0 4.26 24.19L3 29l4.93-1.3A14 14 0 1 0 27.61 4.39Zm-11.09 23.2a11.91 11.91 0 0 1-6.07-1.67l-.43-.25-3.62.95.96-3.53-.28-.44A11.9 11.9 0 1 1 27.9 16a11.89 11.89 0 0 1-11.38 11.59Z"/>
            <path d="M21.38 17.14c-.34-.17-2.05-1.01-2.36-1.12s-.55-.17-.78.17-.9 1.13-1.1 1.36-.4.2-.74.03a7.61 7.61 0 0 1-2.9-2.21 3.41 3.41 0 0 1-.62-1.19c-.13-.32 0-.5.16-.68s.46-.54.57-.72a.59.59 0 0 0 0-.57c-.06-.17-.78-1.86-1.07-2.55s-.57-.58-.78-.59h-.66a1.26 1.26 0 0 0-.91.43 3.83 3.83 0 0 0-1.2 2.84 6.64 6.64 0 0 0 1.38 3.52 10.17 10.17 0 0 0 3.88 3.3 12.63 12.63 0 0 0 1.78.66 4.28 4.28 0 0 0 1.94.12 3.14 3.14 0 0 0 2.06-1.45 2.58 2.58 0 0 0 .18-1.44c-.06-.06-.32-.17-.66-.34Z"/>
          </svg>
        </span>
        <span class="sr-only"><?= escape_html($directLink['label']); ?></span>
      </a>
    </div>
  </div>
<?php endif; ?>

<?php if ($sideLinks): ?>
  <div class="fixed bottom-5 left-4 z-[70] pointer-events-none" data-floating-social-root>
    <div class="pointer-events-auto flex flex-col gap-3">
      <button type="button" class="flex items-center gap-2 rounded-full border border-white/15 bg-background-dark/80 px-4 py-2 text-sm font-semibold text-white shadow-2xl backdrop-blur-md transition hover:border-primary/60" data-floating-social-toggle aria-expanded="false">
        <span class="material-symbols-outlined text-base">diversity_2</span>
        Community Hub
        <span class="material-symbols-outlined text-base transition-transform" data-floating-social-toggle-icon>expand_more</span>
      </button>
      <div class="floating-social-links flex flex-col gap-3" data-floating-social-links>
        <?php foreach ($sideLinks as $link): ?>
          <a href="<?= escape_html($link['url']); ?>" target="_blank" rel="noreferrer" class="group flex items-center gap-3 rounded-3xl border border-white/10 bg-background-dark/80 px-3 py-2 text-white shadow-xl backdrop-blur-md transition hover:-translate-y-0.5 hover:border-primary/60 hover:bg-background-dark/90" aria-label="<?= escape_html($link['label']); ?>">
            <div class="flex size-12 items-center justify-center rounded-2xl bg-gradient-to-br <?= $link['gradient']; ?> text-white shadow-lg">
              <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1;"><?= $link['icon']; ?></span>
            </div>
            <div>
              <p class="text-sm font-semibold leading-tight"><?= escape_html($link['label']); ?></p>
              <p class="text-xs text-white/70 leading-tight"><?= escape_html($link['description']); ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <script>
  (function(){
    const root = document.querySelector('[data-floating-social-root]');
    if (!root) {
      return;
    }
    const toggle = root.querySelector('[data-floating-social-toggle]');
    const icon = root.querySelector('[data-floating-social-toggle-icon]');
    const links = root.querySelector('[data-floating-social-links]');
    if (!toggle || !icon || !links) {
      return;
    }
    const collapseClass = 'floating-social-collapsed';
    const mql = window.matchMedia('(max-width: 640px)');
    const applyState = (collapsed) => {
      root.classList.toggle(collapseClass, collapsed);
      toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
      icon.style.transform = collapsed ? 'rotate(180deg)' : 'rotate(0deg)';
      links.style.display = collapsed ? 'none' : 'flex';
    };
    applyState(mql.matches);
    mql.addEventListener('change', (event) => applyState(event.matches));
    toggle.addEventListener('click', () => {
      const next = !root.classList.contains(collapseClass);
      applyState(next);
    });
  })();
  </script>
<?php endif; ?>
```