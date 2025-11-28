<?php
$currentVisitor = function_exists('current_user') ? current_user() : null;
$isLoggedIn = (bool) $currentVisitor;
$isAdminVisitor = $isLoggedIn && !empty($currentVisitor['is_admin']);
$dashboardHref = $isAdminVisitor ? 'admin/index.php' : 'user/dashboard.php';
$authNavLabel = $isLoggedIn ? 'Dashboard' : 'Login';
$authNavIcon = $isLoggedIn ? 'space_dashboard' : 'login';
$authNavHref = $isLoggedIn ? $dashboardHref : 'user/login.php';

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

$values = [
  ['title' => 'Creator-first', 'description' => 'We’re obsessed with giving creators everything they need to launch premium digital products fast.'],
  ['title' => 'Trust & Security', 'description' => 'Every upload passes rigorous reviews, compliance checks, and malware scanning.'],
  ['title' => 'Global reach', 'description' => 'Our marketplace is optimized for multiple currencies and localized landing pages.'],
];

$milestones = [
  ['year' => '2019', 'title' => 'First launch', 'copy' => 'Scriptloaded goes live with 12 partner developers and 30 digital assets.'],
  ['year' => '2021', 'title' => 'Global expansion', 'copy' => 'Introduced multi-currency pricing plus a curated African creator program.'],
  ['year' => '2024', 'title' => 'Automation era', 'copy' => 'Rolled out AI-assisted reviews, auto-updates, and instant payouts.'],
];

$teamMembers = [
  ['name' => 'Ijeoma Daniel', 'role' => 'Founder & CEO', 'avatar' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=200&q=80'],
  ['name' => 'Marco Ruiz', 'role' => 'Head of Marketplace', 'avatar' => 'https://images.unsplash.com/photo-1529665253569-6d01c0eaf7b6?auto=format&fit=crop&w=200&q=80'],
  ['name' => 'Aisha Abdul', 'role' => 'Lead Engineer', 'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=80'],
];

$impactStats = [
  ['label' => 'Creators onboarded', 'value' => '1,200+'],
  ['label' => 'Digital assets shipped', 'value' => '8,500+'],
  ['label' => 'Successful downloads', 'value' => '240K+'],
  ['label' => 'Support CSAT', 'value' => '4.9/5'],
];

$footerLinkGroups = get_public_footer_link_groups([
  'isLoggedIn' => $isLoggedIn,
  'authNavLabel' => $authNavLabel,
  'dashboardHref' => $isLoggedIn ? $dashboardHref : null,
]);
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>About Scriptloaded</title>
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
        borderRadius: {
          xl: '1.5rem',
        },
      },
    },
  };
</script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-white">
<div class="relative min-h-screen overflow-hidden">
  <div class="pointer-events-none absolute inset-0 opacity-70">
    <div class="absolute -left-32 top-10 h-80 w-80 rounded-full bg-primary/30 blur-3xl"></div>
    <div class="absolute bottom-10 right-0 h-[420px] w-[420px] rounded-full bg-primary/20 blur-3xl"></div>
    <div class="absolute inset-x-0 top-1/3 h-64 bg-gradient-to-r from-primary/10 via-transparent to-primary/10 blur-[120px]"></div>
  </div>
  <div class="relative flex min-h-screen flex-col">
  <div class="bg-gradient-to-b from-background-dark via-background-dark/95 to-background-dark/60">
    <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 lg:px-10">
      <?php include __DIR__ . '/partials/public_header.php'; ?>
    </div>
    <section class="mx-auto max-w-4xl px-4 pb-16 text-center sm:px-6 lg:px-10">
      <p class="text-primary text-sm font-semibold uppercase tracking-[0.3em]">About Scriptloaded</p>
      <h1 class="mt-4 text-4xl font-black leading-tight tracking-tight sm:text-5xl">Building the operating system for modern script creators.</h1>
      <p class="mt-5 text-base text-gray-300 sm:text-lg">We connect talented builders to a global audience with a curated marketplace, AI-assisted reviews, and lifetime support experiences their buyers rave about.</p>
    </section>
  </div>
  <main class="flex-1">
    <section class="mx-auto grid max-w-6xl gap-6 px-4 py-12 sm:grid-cols-3 sm:px-6 lg:px-10">
      <?php foreach ($impactStats as $stat): ?>
        <article class="rounded-2xl border border-white/10 bg-white/5 px-5 py-6 text-center">
          <p class="text-3xl font-bold text-primary"><?= escape_html($stat['value']); ?></p>
          <p class="mt-2 text-sm text-gray-300"><?= escape_html($stat['label']); ?></p>
        </article>
      <?php endforeach; ?>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-10">
      <div class="rounded-[32px] border border-white/10 bg-white/5 p-8 sm:p-12">
        <div class="grid gap-8 lg:grid-cols-3">
          <div class="lg:col-span-2">
            <p class="text-sm uppercase tracking-[0.3em] text-primary">Our story</p>
            <h2 class="mt-3 text-3xl font-bold">From weekend idea to global launch platform.</h2>
            <p class="mt-4 text-gray-300">Scriptloaded was born out of frustration—our founders spent months pitching premium scripts to marketplaces that treated niche creators like an afterthought. We decided to build the platform we wanted: rigorous reviews, instant payouts, personalized support, and a community that values clean code.</p>
          </div>
          <div class="space-y-4">
            <?php foreach ($values as $value): ?>
              <article class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <p class="text-base font-semibold text-white"><?= escape_html($value['title']); ?></p>
                <p class="mt-2 text-sm text-gray-300"><?= escape_html($value['description']); ?></p>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-10">
      <p class="text-sm uppercase tracking-[0.3em] text-primary">Milestones</p>
      <h2 class="mt-3 text-3xl font-bold">Highlights from the journey.</h2>
      <div class="mt-8 space-y-6">
        <?php foreach ($milestones as $milestone): ?>
          <article class="rounded-2xl border border-white/10 bg-white/5 px-6 py-5">
            <p class="text-xs font-semibold uppercase tracking-[0.4em] text-primary"><?= escape_html($milestone['year']); ?></p>
            <h3 class="mt-2 text-xl font-semibold text-white"><?= escape_html($milestone['title']); ?></h3>
            <p class="mt-2 text-sm text-gray-300"><?= escape_html($milestone['copy']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-10">
      <div class="flex flex-col gap-6 rounded-[32px] border border-white/10 bg-gradient-to-br from-primary/10 via-white/5 to-transparent p-8 sm:p-12 lg:flex-row lg:items-center">
        <div class="flex-1">
          <p class="text-sm uppercase tracking-[0.3em] text-primary">Our promise</p>
          <h2 class="mt-3 text-3xl font-bold">Why builders trust Scriptloaded.</h2>
          <p class="mt-4 text-gray-200">We handle fraud detection, compliance, customer support, and documentation templates so creators can focus on quality. Every purchase unlocks lifetime updates and priority support straight from the creators.</p>
        </div>
        <div class="flex flex-col gap-3 rounded-2xl border border-white/10 bg-white/5 p-6">
          <p class="text-white text-lg font-semibold">Ready to sell with us?</p>
          <p class="text-sm text-gray-300">Join our creator onboarding waitlist for one-on-one launch support.</p>
          <a class="inline-flex items-center justify-center rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-white" href="<?= escape_html(site_url('user/register.php')); ?>">Apply as a Creator</a>
        </div>
      </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-10">
      <p class="text-sm uppercase tracking-[0.3em] text-primary">Meet the team</p>
      <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($teamMembers as $member): ?>
          <article class="rounded-2xl border border-white/10 bg-white/5 p-6 text-center">
            <img class="mx-auto h-20 w-20 rounded-full object-cover" src="<?= escape_html($member['avatar']); ?>" alt="<?= escape_html($member['name']); ?>"/>
            <p class="mt-4 text-lg font-semibold text-white"><?= escape_html($member['name']); ?></p>
            <p class="text-sm text-gray-400"><?= escape_html($member['role']); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    </section>
    <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-10">
      <div class="grid gap-6 rounded-[32px] border border-white/10 bg-white/5 p-8 lg:grid-cols-2">
        <div>
          <p class="text-sm uppercase tracking-[0.3em] text-primary">Stay in flow</p>
          <h2 class="mt-3 text-3xl font-bold">Need a custom partnership or press kit?</h2>
          <p class="mt-3 text-gray-300">Our partner team helps accelerators, dev communities, and agencies bring Scriptloaded creators to their audiences. Tell us what you are building and we will craft a co-marketing plan.</p>
          <div class="mt-6 flex flex-wrap gap-3">
            <a class="inline-flex items-center justify-center rounded-2xl bg-primary px-5 py-3 text-sm font-semibold text-white shadow shadow-primary/40" href="<?= escape_html(site_url('contact.php')); ?>">Talk to Partnerships</a>
            <a class="inline-flex items-center justify-center rounded-2xl border border-white/20 px-5 py-3 text-sm font-semibold text-white" href="<?= escape_html(site_url('user/register.php')); ?>">Join as Creator</a>
          </div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-background-dark/60 p-6">
          <p class="text-sm uppercase tracking-[0.3em] text-primary">Response times</p>
          <dl class="mt-4 space-y-4 text-sm text-gray-300">
            <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
              <dt>Partner inquiries</dt>
              <dd class="font-semibold text-white">&lt; 24 hours</dd>
            </div>
            <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
              <dt>Press &amp; media</dt>
              <dd class="font-semibold text-white">Same day</dd>
            </div>
            <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
              <dt>Enterprise demos</dt>
              <dd class="font-semibold text-white">Within 48 hours</dd>
            </div>
          </dl>
        </div>
      </div>
    </section>
  </main>
  <?php include __DIR__ . '/partials/public_footer.php'; ?>
  </div>
</div>
<script src="assets/js/mobile-menu.js"></script>
</body></html>
