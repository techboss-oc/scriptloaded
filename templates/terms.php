<?php
$currentVisitor = function_exists('current_user') ? current_user() : null;
$isLoggedIn = (bool) $currentVisitor;
$isAdminVisitor = $isLoggedIn && !empty($currentVisitor['is_admin']);
$dashboardHref = $isAdminVisitor ? 'admin/index.php' : 'user/dashboard.php';
$authNavLabel = $isLoggedIn ? 'Dashboard' : 'Login';
$authNavIcon = $isLoggedIn ? 'space_dashboard' : 'login';
$authNavHref = $isLoggedIn ? $dashboardHref : 'user/login.php';
$primaryCtaLabel = $isLoggedIn ? 'View Dashboard' : 'Register';
$primaryCtaHref = $isLoggedIn ? $dashboardHref : 'user/register.php';

$primaryNavLinks = [
  ['label' => 'Home', 'href' => 'index.php'],
  ['label' => 'Marketplace', 'href' => 'listing.php'],
  ['label' => 'Featured Product', 'href' => 'product.php?slug=ecommerce-website-script'],
  ['label' => 'About', 'href' => 'about.php'],
  ['label' => 'Contact', 'href' => 'contact.php'],
];
if ($isLoggedIn) {
  $primaryNavLinks[] = ['label' => $authNavLabel, 'href' => $authNavHref];
}

$mobileNavLinks = [
  ['label' => 'Home', 'href' => 'index.php', 'icon' => 'home'],
  ['label' => 'Marketplace', 'href' => 'listing.php', 'icon' => 'storefront'],
  ['label' => 'Featured', 'href' => 'product.php?slug=ecommerce-website-script', 'icon' => 'rocket_launch'],
  ['label' => 'About', 'href' => 'about.php', 'icon' => 'info'],
  ['label' => 'Contact', 'href' => 'contact.php', 'icon' => 'call'],
];
if ($isLoggedIn) {
  $mobileNavLinks[] = ['label' => $authNavLabel, 'href' => $authNavHref, 'icon' => $authNavIcon];
}

$documentMeta = [
  'effective' => 'September 15, 2025',
  'last_update' => 'November 10, 2025',
  'jurisdiction' => 'Lagos, Nigeria',
];

$termSections = [
  [
    'title' => '1. Acceptance of terms',
    'items' => [
      'By creating a Scriptloaded account or purchasing a digital asset you agree to these terms, the Privacy Policy, and any product-specific usage notes.',
      'We may update these terms as features evolve. Continued use after notice constitutes acceptance of the revised version.',
    ],
  ],
  [
    'title' => '2. Marketplace roles',
    'items' => [
      'Scriptloaded provides infrastructure, escrow, and compliance tooling that enable creators to sell premium assets.',
      'Creators remain the intellectual property owners but grant buyers usage rights defined in each product listing.',
      'Buyers may only use assets as described on the product page and may not redistribute except where expressly allowed.',
    ],
  ],
  [
    'title' => '3. Payments & pricing',
    'items' => [
      'All prices are displayed in USD or NGN. Taxes, fees, and payout splits are shown before checkout completes.',
      'Payments are processed through trusted providers; Scriptloaded never stores full card numbers.',
      'Payouts to creators occur weekly once the minimum balance is met and dispute windows are cleared.',
    ],
  ],
  [
    'title' => '4. Usage guidelines',
    'items' => [
      'Each download includes implementation rights described on the product page. Review usage allowances before purchasing.',
      'You may not resell, repackage, or claim ownership of assets purchased on Scriptloaded without explicit permission.',
      'Attribution requirements and support levels are disclosed per product and must be honored.',
    ],
  ],
  [
    'title' => '5. Seller responsibilities',
    'items' => [
      'Upload only original work you own or have the right to distribute.',
      'Maintain accurate documentation, version history, and support response times promised to buyers.',
      'Respond to disputes or takedown requests within 72 hours. Repeated violations may result in account suspension.',
    ],
  ],
  [
    'title' => '6. Buyer responsibilities',
    'items' => [
      'Verify product compatibility before purchase. Digital downloads are non-refundable once delivered unless defective.',
      'Do not share download links or assets outside your team or approved clients.',
      'Report any suspected misuse or policy violations through the support portal.',
    ],
  ],
  [
    'title' => '7. Termination',
    'items' => [
      'We may suspend or terminate accounts that violate these terms, misuse payments, or compromise marketplace security.',
      'You may close your account anytime. Certain financial records must remain for compliance, but active listings will be removed.',
    ],
  ],
  [
    'title' => '8. Disclaimers & liability',
    'items' => [
      'Scriptloaded provides the platform “as is” without warranty of uninterrupted access.',
      'Our total liability for any claim is limited to the greater of ₦200,000 or the amount paid to Scriptloaded in the past 12 months.',
    ],
  ],
];

$ctaLinks = [
  ['label' => 'Contact Legal', 'href' => 'contact.php', 'icon' => 'support_agent'],
  ['label' => 'Report Abuse', 'href' => 'contact.php#support', 'icon' => 'flag'],
];

$footerLinkGroups = get_public_footer_link_groups([
  'isLoggedIn' => $isLoggedIn,
  'authNavLabel' => $authNavLabel,
  'dashboardHref' => $isLoggedIn ? $dashboardHref : null,
]);
?>
<!DOCTYPE html>
<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
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
  tailwind.config = {
    darkMode: 'class',
    theme: {
      extend: {
        colors: {
          primary: '#1C74E9',
          'background-dark': '#0B111A',
          'background-light': '#F5F7FB',
        },
        fontFamily: {
          display: ['Space Grotesk', 'sans-serif'],
        },
      },
    },
  };
</script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-white">
<div class="relative min-h-screen overflow-hidden">
  <div class="pointer-events-none absolute inset-0 opacity-80">
    <div class="absolute top-10 left-10 h-72 w-72 rounded-full bg-primary/20 blur-[150px]"></div>
    <div class="absolute bottom-0 right-0 h-[420px] w-[420px] rounded-full bg-primary/15 blur-[180px]"></div>
    <div class="absolute inset-x-0 top-1/3 h-60 bg-gradient-to-r from-primary/5 via-transparent to-primary/15 blur-[120px]"></div>
  </div>
  <div class="relative flex min-h-screen flex-col">
    <div class="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 lg:px-10">
      <?php include __DIR__ . '/partials/public_header.php'; ?>
    </div>
    <main class="mx-auto w-full max-w-5xl flex-1 px-4 pb-16 sm:px-6 lg:px-10">
      <section class="rounded-[32px] border border-white/10 bg-white/5 p-10 text-center backdrop-blur-xl shadow-2xl">
        <p class="text-sm uppercase tracking-[0.3em] text-primary">Terms &amp; Conditions</p>
        <h1 class="mt-4 text-4xl font-black leading-tight">The rules that keep Scriptloaded fair.</h1>
        <p class="mt-4 text-base text-gray-300">These terms outline your responsibilities as a creator, buyer, or partner on Scriptloaded. Please review them before listing assets or completing purchases.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-4 text-xs text-gray-300">
          <span class="rounded-full border border-white/15 px-4 py-2">Effective: <?= escape_html($documentMeta['effective']); ?></span>
          <span class="rounded-full border border-white/15 px-4 py-2">Last update: <?= escape_html($documentMeta['last_update']); ?></span>
          <span class="rounded-full border border-white/15 px-4 py-2">Jurisdiction: <?= escape_html($documentMeta['jurisdiction']); ?></span>
        </div>
      </section>
      <section class="mt-12 space-y-6">
        <?php foreach ($termSections as $section): ?>
          <article class="rounded-3xl border border-white/10 bg-gradient-to-br from-white/8 via-white/2 to-transparent p-8 backdrop-blur-xl">
            <h2 class="text-lg font-semibold text-white"><?= escape_html($section['title']); ?></h2>
            <ul class="mt-4 space-y-3 text-sm text-gray-300">
              <?php foreach ($section['items'] as $item): ?>
                <li class="flex gap-3"><span class="text-primary">•</span><span><?= escape_html($item); ?></span></li>
              <?php endforeach; ?>
            </ul>
          </article>
        <?php endforeach; ?>
      </section>
      <section class="mt-12 grid gap-4 rounded-3xl border border-white/10 bg-white/5 p-8 backdrop-blur-xl md:grid-cols-2">
        <?php foreach ($ctaLinks as $cta): ?>
          <a class="group flex items-center justify-between rounded-2xl border border-white/10 bg-white/0 px-5 py-4 text-left transition hover:border-primary/60 hover:bg-primary/10" href="<?= escape_html(site_url($cta['href'])); ?>">
            <div>
              <p class="text-xs uppercase tracking-[0.4em] text-gray-400">Action</p>
              <p class="text-lg font-semibold text-white"><?= escape_html($cta['label']); ?></p>
            </div>
            <span class="material-symbols-outlined text-2xl text-primary"><?= escape_html($cta['icon']); ?></span>
          </a>
        <?php endforeach; ?>
      </section>
      <section class="mt-8 rounded-3xl border border-dashed border-white/20 bg-white/5 p-8 text-sm text-gray-300">
        <p>Questions about these terms? Email legal@scriptloaded.test. We respond to legal inquiries within 5 business days.</p>
      </section>
    </main>
    <?php include __DIR__ . '/partials/public_footer.php'; ?>
  </div>
</div>
<script src="assets/js/mobile-menu.js"></script>
</body></html>
