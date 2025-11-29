<?php
$currentVisitor = function_exists('current_user') ? current_user() : null;
$isLoggedIn = (bool) $currentVisitor;
$isAdminVisitor = $isLoggedIn && !empty($currentVisitor['is_admin']);
$dashboardHref = $isAdminVisitor ? 'admin/index' : 'user/dashboard';
$authNavLabel = $isLoggedIn ? 'Dashboard' : 'Login';
$authNavIcon = $isLoggedIn ? 'space_dashboard' : 'login';
$authNavHref = $isLoggedIn ? $dashboardHref : 'user/login';
$primaryCtaLabel = $isLoggedIn ? 'View Dashboard' : 'Register';
$primaryCtaHref = $isLoggedIn ? $dashboardHref : 'user/register';

$primaryNavLinks = [
  ['label' => 'Home', 'href' => 'index'],
  ['label' => 'Marketplace', 'href' => 'listing'],
  ['label' => 'Featured Product', 'href' => 'product?slug=ecommerce-website-script'],
  ['label' => 'About', 'href' => 'about'],
  ['label' => 'Contact', 'href' => 'contact'],
];
if ($isLoggedIn) {
  $primaryNavLinks[] = ['label' => $authNavLabel, 'href' => $authNavHref];
}

$mobileNavLinks = [
  ['label' => 'Home', 'href' => 'index', 'icon' => 'home'],
  ['label' => 'Marketplace', 'href' => 'listing', 'icon' => 'storefront'],
  ['label' => 'Featured', 'href' => 'product?slug=ecommerce-website-script', 'icon' => 'rocket_launch'],
  ['label' => 'About', 'href' => 'about', 'icon' => 'info'],
  ['label' => 'Contact', 'href' => 'contact', 'icon' => 'call'],
];

$policyHighlights = [
  ['title' => 'Data We Collect', 'copy' => 'We store only the information needed to run Scriptloaded: account details, purchase history, payout preferences, and security logs.'],
  ['title' => 'How It’s Used', 'copy' => 'Your data powers fulfilment, fraud prevention, personalized recommendations, and regulatory reporting.'],
  ['title' => 'Your Controls', 'copy' => 'Download your data, update marketing preferences, or request deletion directly from your dashboard or by emailing privacy@scriptloaded.test.'],
];

$policySections = [
  [
    'label' => '1. Information we collect',
    'paragraphs' => [
      'Account identity: first name, last name, username, and verified email address.',
      'Transaction and payout history for compliance and downloadable receipts.',
      'Usage telemetry (device, browser, rough location, actions) captured with privacy-first analytics for abuse detection.',
      'Content you upload such as product descriptions, assets, documentation, and messages exchanged with buyers.',
    ],
  ],
  [
    'label' => '2. How we use information',
    'paragraphs' => [
      'Operate the marketplace: process payments, deliver downloads, issue invoices, and provide licensing artefacts.',
      'Security: monitor suspicious activity, investigate chargebacks, and enforce account limits.',
      'Product improvements: anonymized analytics help prioritize features, onboarding flows, and support tooling.',
      'Communications: send receipts, policy notices, beta invites, and mission-critical updates. Marketing emails are optional.',
    ],
  ],
  [
    'label' => '3. When we share data',
    'paragraphs' => [
      'Vetted processors such as payment gateways, cloud storage, analytics, and email infrastructure under strict DPAs.',
      'Legal, law-enforcement, or auditors when mandated by applicable regulation or licensing agreements.',
      'Buyers receive only what you intentionally publish (e.g., store profile, product listing, and support channels).',
    ],
  ],
  [
    'label' => '4. Your rights & choices',
    'paragraphs' => [
      'Access, export, or delete your personal data by contacting privacy@scriptloaded.test or opening a support ticket.',
      'Update billing, payout, and notification preferences from your dashboard anytime.',
      'Opt out of marketing emails via the unsubscribe link or from Account → Notifications.',
    ],
  ],
  [
    'label' => '5. Security & retention',
    'paragraphs' => [
      'All data is encrypted in transit (TLS 1.3) and at rest using 256-bit encryption.',
      'We retain transaction records for at least 7 years to meet financial regulations, after which they are anonymized.',
      'Incident response drills, access reviews, and vendor security assessments run every quarter.',
    ],
  ],
];

$updateMeta = [
  'effective' => 'September 15, 2025',
  'last_review' => 'November 10, 2025',
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
  <div class="pointer-events-none absolute inset-0">
    <div class="absolute -right-32 top-10 h-72 w-72 rounded-full bg-primary/15 blur-[150px]"></div>
    <div class="absolute -left-20 bottom-0 h-96 w-96 rounded-full bg-primary/25 blur-[180px]"></div>
    <div class="absolute inset-x-0 top-1/3 h-72 bg-gradient-to-r from-primary/10 via-transparent to-primary/20 blur-[120px]"></div>
  </div>
  <div class="relative flex min-h-screen flex-col">
    <div class="mx-auto w-full max-w-5xl px-4 py-6 sm:px-6 lg:px-10">
      <?php include __DIR__ . '/partials/public_header.php'; ?>
    </div>
    <main class="mx-auto w-full max-w-5xl flex-1 px-4 pb-16 sm:px-6 lg:px-10">
      <section class="rounded-[32px] border border-white/10 bg-white/5 backdrop-blur-xl p-10 text-center shadow-2xl">
        <p class="text-sm uppercase tracking-[0.3em] text-primary">Privacy Policy</p>
        <h1 class="mt-4 text-4xl font-black leading-tight">Your trust powers Scriptloaded.</h1>
        <p class="mt-4 text-base text-gray-300">We designed every workflow with privacy, creator control, and transparent data practices. This policy explains how your information is collected, used, and protected.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-6 text-sm text-gray-300">
          <div class="rounded-full border border-white/15 px-4 py-2">Effective: <?= escape_html($updateMeta['effective']); ?></div>
          <div class="rounded-full border border-white/15 px-4 py-2">Last review: <?= escape_html($updateMeta['last_review']); ?></div>
          <div class="rounded-full border border-white/15 px-4 py-2">Contact: privacy@scriptloaded.test</div>
        </div>
      </section>
      <section class="mt-12 grid gap-5 md:grid-cols-3">
        <?php foreach ($policyHighlights as $highlight): ?>
          <article class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl shadow-lg">
            <p class="text-xs uppercase tracking-[0.4em] text-primary">Summary</p>
            <h3 class="mt-3 text-xl font-semibold text-white"><?= escape_html($highlight['title']); ?></h3>
            <p class="mt-2 text-sm text-gray-300"><?= escape_html($highlight['copy']); ?></p>
          </article>
        <?php endforeach; ?>
      </section>
      <section class="mt-12 space-y-6">
        <?php foreach ($policySections as $section): ?>
          <article class="rounded-3xl border border-white/10 bg-gradient-to-br from-white/8 via-white/2 to-transparent p-8 backdrop-blur-xl">
            <h2 class="text-lg font-semibold text-white"><?= escape_html($section['label']); ?></h2>
            <ul class="mt-4 space-y-3 text-sm text-gray-300">
              <?php foreach ($section['paragraphs'] as $paragraph): ?>
                <li class="flex gap-3"><span class="text-primary">•</span><span><?= escape_html($paragraph); ?></span></li>
              <?php endforeach; ?>
            </ul>
          </article>
        <?php endforeach; ?>
      </section>
      <section class="mt-12 rounded-3xl border border-primary/30 bg-primary/10 p-8 text-center">
        <p class="text-sm uppercase tracking-[0.4em] text-primary">Need updates?</p>
        <h3 class="mt-3 text-2xl font-semibold">Request a data report or deletion.</h3>
        <p class="mt-2 text-sm text-gray-200">Email privacy@scriptloaded.test or open a support ticket from your dashboard. We reply within 72 hours.</p>
        <div class="mt-6 flex flex-wrap justify-center gap-4">
          <a class="inline-flex items-center gap-2 rounded-2xl bg-primary px-5 py-3 text-sm font-semibold text-white" href="<?= escape_html(site_url('contact')); ?>">
            Contact support
            <span class="material-symbols-outlined text-base">arrow_outward</span>
          </a>
          <a class="inline-flex items-center gap-2 rounded-2xl border border-white/30 px-5 py-3 text-sm font-semibold text-white" href="mailto:privacy@scriptloaded.test">
            Email privacy team
          </a>
        </div>
      </section>
    </main>
    <?php include __DIR__ . '/partials/public_footer.php'; ?>
  </div>
</div>
<script src="assets/js/mobile-menu.js"></script>
</body></html>
