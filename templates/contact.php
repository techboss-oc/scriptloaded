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
  ['label' => 'Featured Product', 'href' => 'product.php?slug=ecommerce-website-script', 'icon' => 'rocket_launch'],
  ['label' => 'About', 'href' => 'about.php', 'icon' => 'info'],
  ['label' => 'Contact', 'href' => 'contact.php', 'icon' => 'call'],
];
if ($isLoggedIn) {
  $mobileNavLinks[] = ['label' => $authNavLabel, 'href' => $authNavHref, 'icon' => $authNavIcon];
}

$contactChannels = [
  ['icon' => 'mail', 'title' => 'Email support', 'copy' => 'Get a same-day response from our success team.', 'value' => 'support@scriptloaded.test'],
  ['icon' => 'phone_in_talk', 'title' => 'Priority line', 'copy' => 'Available weekdays 9am-5pm GMT+1.', 'value' => '+234 800 000 0000'],
  ['icon' => 'forum', 'title' => 'Creator community', 'copy' => 'Share wins, swap templates, and get early betas.', 'value' => 'community.scriptloaded.test'],
];

$faqs = [
  ['q' => 'Where can I reset my password?', 'a' => 'Use the Forgot Password link on the user portal to receive a secure reset email.'],
  ['q' => 'How fast do you review submissions?', 'a' => 'Most uploads are reviewed within 48 hours. Priority support plans are faster.'],
  ['q' => 'Can I get custom deployment help?', 'a' => 'Yes. Contact us with your project scope and we will match you with a verified implementer.'],
];

$footerLinkGroups = get_public_footer_link_groups([
  'isLoggedIn' => $isLoggedIn,
  'authNavLabel' => $authNavLabel,
  'dashboardHref' => $isLoggedIn ? $dashboardHref : null,
]);

$supportStats = [
  ['label' => 'Average first reply', 'value' => '2h 10m'],
  ['label' => 'Global offices', 'value' => '4 hubs'],
  ['label' => 'Creator NPS', 'value' => '+67'],
];
?>
<!DOCTYPE html>
<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Contact Scriptloaded Support</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<style>
  .material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
</style>
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
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-white">
<div class="relative min-h-screen overflow-hidden">
  <div class="pointer-events-none absolute inset-0">
    <div class="absolute -right-20 top-10 h-72 w-72 rounded-full bg-primary/20 blur-[120px]"></div>
    <div class="absolute -left-16 bottom-0 h-96 w-96 rounded-full bg-primary/30 blur-3xl"></div>
    <div class="absolute inset-x-0 top-1/3 h-72 bg-gradient-to-r from-primary/10 via-transparent to-primary/20 blur-[160px]"></div>
  </div>
  <div class="relative flex min-h-screen flex-col">
  <div class="bg-gradient-to-br from-background-dark via-background-dark/95 to-background-dark/70">
    <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 lg:px-10">
      <?php include __DIR__ . '/partials/public_header.php'; ?>
    </div>
    <section class="mx-auto max-w-3xl px-4 pb-16 text-center sm:px-6 lg:px-10">
      <p class="text-primary text-sm font-semibold uppercase tracking-[0.3em]">Contact</p>
      <h1 class="mt-4 text-4xl font-black leading-tight tracking-tight sm:text-5xl">Let’s talk about your next launch.</h1>
      <p class="mt-4 text-base text-gray-300 sm:text-lg">Fill out the form or choose any of the support channels below. Our team replies within one business day.</p>
    </section>
  </div>
  <main class="flex-1">
    <section class="mx-auto grid max-w-5xl gap-8 px-4 py-12 md:grid-cols-[1.2fr_0.8fr] sm:px-6 lg:px-10">
      <form class="rounded-3xl border border-white/10 bg-white/5 px-6 py-8 shadow-2xl space-y-5" method="post">
        <div>
          <label class="text-sm font-semibold text-gray-200">Full name</label>
          <input class="mt-2 h-12 w-full rounded-xl border border-white/15 bg-white/10 px-4 text-sm text-white placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/30" name="name" placeholder="Jane Doe" required/>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="text-sm font-semibold text-gray-200">Email</label>
            <input class="mt-2 h-12 w-full rounded-xl border border-white/15 bg-white/10 px-4 text-sm text-white placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/30" type="email" name="email" placeholder="you@example.com" required/>
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-200">Company</label>
            <input class="mt-2 h-12 w-full rounded-xl border border-white/15 bg-white/10 px-4 text-sm text-white placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/30" name="company" placeholder="Studio name"/>
          </div>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-200">Topic</label>
          <select class="mt-2 h-12 w-full rounded-xl border border-white/15 bg-white/10 px-4 text-sm text-white focus:border-primary focus:ring-2 focus:ring-primary/30" name="topic">
            <option class="bg-background-dark" value="support">Customer support</option>
            <option class="bg-background-dark" value="creator">Become a creator</option>
            <option class="bg-background-dark" value="enterprise">Enterprise partnership</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-200">Message</label>
          <textarea class="mt-2 w-full rounded-2xl border border-white/15 bg-white/10 px-4 py-3 text-sm text-white placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/30" rows="5" name="message" placeholder="Tell us how we can help"></textarea>
        </div>
        <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-primary px-4 py-3 text-sm font-semibold text-white shadow shadow-primary/40">Send message</button>
        <p class="text-center text-xs text-gray-400">By submitting, you agree to our privacy policy. We keep responses within one business day.</p>
      </form>
      <div class="space-y-6">
        <?php foreach ($contactChannels as $channel): ?>
          <article class="rounded-3xl border border-white/10 bg-white/5 px-5 py-6">
            <div class="flex items-center gap-3">
              <span class="material-symbols-outlined text-2xl text-primary"><?= escape_html($channel['icon']); ?></span>
              <div>
                <p class="text-base font-semibold text-white"><?= escape_html($channel['title']); ?></p>
                <p class="text-sm text-gray-400"><?= escape_html($channel['copy']); ?></p>
              </div>
            </div>
            <p class="mt-4 text-sm font-semibold text-gray-200"><?= escape_html($channel['value']); ?></p>
          </article>
        <?php endforeach; ?>
        <div class="rounded-3xl border border-white/10 bg-white/5 px-5 py-6">
          <p class="text-sm uppercase tracking-[0.3em] text-primary">HQ</p>
          <p class="mt-2 text-base font-semibold text-white">Lagos, Nigeria</p>
          <p class="text-sm text-gray-400">We’re remote-first with team members across four time zones.</p>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-10">
      <div class="rounded-3xl border border-white/10 bg-white/5 px-6 py-8">
        <p class="text-sm uppercase tracking-[0.3em] text-primary">FAQs</p>
        <div class="mt-6 space-y-5">
          <?php foreach ($faqs as $faq): ?>
            <article class="rounded-2xl border border-white/10 bg-background-dark/40 px-5 py-4">
              <p class="text-base font-semibold text-white"><?= escape_html($faq['q']); ?></p>
              <p class="mt-2 text-sm text-gray-300"><?= escape_html($faq['a']); ?></p>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <section class="mx-auto max-w-5xl px-4 pb-16 sm:px-6 lg:px-10">
      <div class="grid gap-6 rounded-[32px] border border-white/10 bg-white/5 p-8 lg:grid-cols-[1.2fr_0.8fr]">
        <div>
          <p class="text-sm uppercase tracking-[0.3em] text-primary">White-glove support</p>
          <h2 class="mt-3 text-3xl font-bold">Need a faster path to the team?</h2>
          <p class="mt-3 text-gray-300">Priority creators and enterprise partners get dedicated Slack channels, beta access, and quarterly roadmap reviews. Tell us about your workload and we will tailor a plan.</p>
          <div class="mt-6 flex flex-wrap gap-3">
            <a class="inline-flex items-center justify-center rounded-2xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow shadow-primary/40" href="<?= escape_html(site_url('user/register.php')); ?>">Join the Program</a>
            <a class="inline-flex items-center justify-center rounded-2xl border border-white/20 px-5 py-3 text-sm font-semibold text-white" href="<?= escape_html(site_url('about.php')); ?>">Learn about us</a>
          </div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-background-dark/60 p-6">
          <p class="text-sm uppercase tracking-[0.3em] text-primary">Support stats</p>
          <div class="mt-4 grid gap-4">
            <?php foreach ($supportStats as $stat): ?>
              <article class="rounded-2xl bg-white/5 px-4 py-3 text-sm text-gray-300">
                <p class="text-gray-400"><?= escape_html($stat['label']); ?></p>
                <p class="text-xl font-semibold text-white"><?= escape_html($stat['value']); ?></p>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>
  </main>
  <?php include __DIR__ . '/partials/public_footer.php'; ?>
  </div>
</div>
<script src="assets/js/mobile-menu.js"></script>
</body></html>
