<?php require __DIR__ . '/../inc/config.php'; ?>
<?php require __DIR__ . '/../inc/helpers.php'; ?>
<?php require __DIR__ . '/../inc/auth.php'; ?>
<?php require __DIR__ . '/../inc/csrf.php'; ?>
<?php
$errors = [];
if (current_user()) {
  header('Location: ' . site_url('user/dashboard.php'));
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $csrf = $_POST['csrf'] ?? '';
  $fullName = trim($_POST['full_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $passwordConfirm = $_POST['password_confirm'] ?? '';
  $acceptedTerms = isset($_POST['terms']);
  if (!validate_csrf($csrf)) {
    $errors[] = 'Invalid request. Please refresh and try again.';
  }
  if ($fullName === '') {
    $errors[] = 'Full name is required.';
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Enter a valid email address.';
  }
  if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters.';
  }
  if ($password !== $passwordConfirm) {
    $errors[] = 'Passwords do not match.';
  }
  if (!$acceptedTerms) {
    $errors[] = 'You must accept the Terms to continue.';
  }
  if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
      $errors[] = 'An account with that email already exists.';
    }
  }
  if (empty($errors)) {
    try {
      $stmt = $pdo->prepare('INSERT INTO users (email,password_hash,full_name) VALUES (:email,:password_hash,:full_name)');
      $stmt->execute([
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'full_name' => $fullName,
      ]);
      $userId = (int)$pdo->lastInsertId();
      login_user($userId, false);
      header('Location: ' . site_url('user/dashboard.php'));
      exit;
    } catch (Throwable $e) {
      $errors[] = 'Unable to create your account right now. Please try again.';
    }
  }
}

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
?>
<!DOCTYPE html>
<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width,initial-scale=1.0" name="viewport"/>
<title>Create Your Account - Scriptloaded</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link href="https://fonts.gstatic.com" rel="preconnect" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          primary: "#1c74e9",
          "background-light": "#f6f7f8",
          "background-dark": "#111821",
        },
        fontFamily: {
          display: ["Space Grotesk", "sans-serif"],
        },
      },
    },
  }
</script>
<style>
.glassmorphism {
  background: rgba(26, 37, 51, 0.6);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(51, 73, 102, 0.5);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.37);
}
.form-input.glassmorphism {
  background: rgba(26, 37, 51, 0.75);
  border: 1px solid #334966;
}
.form-input.glassmorphism:focus {
  border-color: #1c74e9;
  box-shadow: 0 0 0 2px rgba(28, 116, 233, 0.2);
}
.icon-container.glassmorphism {
  background: rgba(26, 37, 51, 0.75);
  border: 1px solid #334966;
  border-left: 0;
}
</style>
</head>
<body class="font-display text-white bg-background-light dark:bg-background-dark">
<div class="relative min-h-screen overflow-hidden">
  <div class="pointer-events-none absolute inset-0">
    <div class="absolute top-1/2 left-1/2 h-[60vh] w-[60vw] max-h-[520px] max-w-2xl -translate-x-1/2 -translate-y-1/2 rounded-full bg-gradient-to-tr from-primary to-purple-600 opacity-30 blur-3xl"></div>
    <div class="absolute left-20 top-10 h-72 w-72 rounded-full bg-fuchsia-500 opacity-20 blur-3xl"></div>
    <div class="absolute bottom-10 right-20 h-72 w-72 rounded-full bg-cyan-400 opacity-20 blur-3xl"></div>
  </div>
  <div class="relative flex min-h-screen flex-col">
    <?php include __DIR__ . '/../templates/partials/public_header.php'; ?>
    <main class="flex flex-1 items-center justify-center px-4 py-12 sm:px-6">
      <div class="relative z-10 w-full max-w-md flex-col items-center gap-8">
        <div class="glassmorphism flex w-full flex-col gap-6 rounded-2xl p-8 text-white">
      <div class="text-center space-y-1">
        <p class="text-2xl font-bold">Create Your Account</p>
        <p class="text-base text-[#92aac8]">Join Scriptloaded to buy and sell premium digital assets.</p>
      </div>
      <?php if (!empty($errors)): ?>
        <div class="rounded-lg border border-red-500/30 bg-red-500/20 p-4 text-sm text-red-100">
          <?php foreach ($errors as $error): ?>
            <p><?= escape_html($error); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <form action="<?= escape_html(site_url('user/register.php')); ?>" method="post" class="flex flex-col gap-4">
        <input type="hidden" name="csrf" value="<?= escape_html(generate_csrf()); ?>" />
        <label class="flex flex-col gap-2 text-sm">
          Full Name
          <input class="form-input glassmorphism h-12 rounded-lg px-3 text-white placeholder:text-[#92aac8] focus:outline-none" name="full_name" placeholder="Enter your full name" value="<?= escape_html($_POST['full_name'] ?? ''); ?>" required />
        </label>
        <label class="flex flex-col gap-2 text-sm">
          Email
          <input class="form-input glassmorphism h-12 rounded-lg px-3 text-white placeholder:text-[#92aac8] focus:outline-none" type="email" name="email" placeholder="Enter your email address" value="<?= escape_html($_POST['email'] ?? ''); ?>" required />
        </label>
        <label class="flex flex-col gap-2 text-sm">
          Password
          <div class="flex items-stretch">
            <input class="form-input glassmorphism h-12 flex-1 rounded-lg rounded-r-none border-r-0 px-3 text-white placeholder:text-[#92aac8] focus:outline-none" type="password" name="password" placeholder="Create a password" required />
            <div class="icon-container glassmorphism flex items-center rounded-r-lg pr-3 text-[#92aac8]">
              <span class="material-symbols-outlined">visibility_off</span>
            </div>
          </div>
        </label>
        <label class="flex flex-col gap-2 text-sm">
          Confirm Password
          <div class="flex items-stretch">
            <input class="form-input glassmorphism h-12 flex-1 rounded-lg rounded-r-none border-r-0 px-3 text-white placeholder:text-[#92aac8] focus:outline-none" type="password" name="password_confirm" placeholder="Confirm your password" required />
            <div class="icon-container glassmorphism flex items-center rounded-r-lg pr-3 text-[#92aac8]">
              <span class="material-symbols-outlined">visibility_off</span>
            </div>
          </div>
        </label>
        <label class="flex items-start gap-2 text-xs text-[#92aac8] pt-2">
          <input class="mt-1 h-4 w-4 rounded border-[#334966] bg-[#1a2533] text-primary focus:ring-primary" type="checkbox" name="terms" required />
          I agree to the <a class="font-medium text-white hover:text-primary" href="<?= escape_html(site_url('terms.php')); ?>">Terms of Service</a> and <a class="font-medium text-white hover:text-primary" href="<?= escape_html(site_url('privacy.php')); ?>">Privacy Policy</a>.
        </label>
        <button type="submit" class="mt-2 h-12 rounded-lg bg-primary text-base font-semibold text-white transition hover:bg-primary/90">
          Create Account
        </button>
      </form>
      <div class="relative flex items-center py-2">
        <div class="flex-grow border-t border-[#334966]"></div>
        <span class="mx-4 text-xs text-[#92aac8] tracking-[0.3em]">OR</span>
        <div class="flex-grow border-t border-[#334966]"></div>
      </div>
      <div class="grid grid-cols-2 gap-4 text-sm">
        <button type="button" class="flex h-11 w-full items-center justify-center gap-2 rounded-lg border border-white/20 bg-white/10 text-white transition hover:bg-white/20">
          <img alt="Google" class="h-5 w-5" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCb_XKITcF4nH1OETFj6_sEaMKyQ9I8UZrjG6AqGRQgJeqEB8_H9_2hHrskneeA-niN0limhigb_e3yWGW-3xdJr8foxl9yt4xa-Mzf84-kjqiexw5GAkBjozQ8TL8z6mb9MBVR69gC_Z4BCLY5TClqlnqtZql-DA-Pdzcp1-O7tfXKorOhSWIybwA0-bnksWXUzqx3wvz7wj4sImDoeGVR67dMeKUFKz2213MrYLW-KaZ0wveuzKU2zNo90CWw9M8KtVRaKy-momwF"/>Google
        </button>
        <button type="button" class="flex h-11 w-full items-center justify-center gap-2 rounded-lg border border-white/20 bg-white/10 text-white transition hover:bg-white/20">
          <img alt="GitHub" class="h-5 w-5" src="https://lh3.googleusercontent.com/aida-public/AB6AXuADNiN9szqGLJPmqmwzsOqfENC3cC5eil3GGKeXg8ffEYCYJdJX3YA3AeuhdEpc_Kim6Ai64jeUfdm8PCQGfdjw3MH7E_GukTUsQk1NWAq2LuYRfkL3S_cwLpbZM_WBj5UDzqWgogKL0l-6F06Hqb2FmNLoa0WDqvYMJSZyHXCpSgL-Ig2mpREQjnMQaE1RcAXgR99NWzw1SvHIXtaXFMzGvthdkIF2z2VFRZCzj-_bzp5U6SsC0c39m1DThkjsKYXW7O64uNfqge60"/>GitHub
        </button>
      </div>
        </div>
        <p class="mt-6 text-center text-sm text-[#92aac8]">Already have an account? <a class="font-semibold text-white hover:text-primary" href="<?= escape_html(site_url('user/login.php')); ?>">Log In</a></p>
      </div>
    </main>
  </div>
</div>
<script src="<?= escape_html(site_url('assets/js/mobile-menu.js')); ?>"></script>
</body></html>