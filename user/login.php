<?php require __DIR__ . '/../inc/config.php'; ?>
<?php require __DIR__ . '/../inc/helpers.php'; ?>
<?php require __DIR__ . '/../inc/auth.php'; ?>
<?php require __DIR__ . '/../inc/csrf.php'; ?>
<?php
$errors = [];
$redirectTarget = scriptloaded_sanitize_redirect_target($_REQUEST['redirect'] ?? 'user/dashboard.php', 'user/dashboard.php');
$existingUser = current_user();
if ($existingUser) {
  $redirect = !empty($existingUser['is_admin']) ? 'admin/index.php' : $redirectTarget;
  header('Location: ' . site_url($redirect));
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $csrf = $_POST['csrf'] ?? '';
  if (!validate_csrf($csrf)) {
    $errors[] = 'Invalid request. Please refresh and try again.';
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Enter a valid email address.';
  }
  if ($password === '') {
    $errors[] = 'Password is required.';
  }
  if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT id,password_hash,is_admin FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
      $errors[] = 'Invalid email or password.';
    } elseif (!empty($user['is_admin'])) {
      $errors[] = 'Admin accounts must use the dedicated Admin Login.';
    } else {
      login_user((int)$user['id'], false);
      $destination = $redirectTarget ?: 'user/dashboard.php';
      header('Location: ' . site_url($destination));
      exit;
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
<title>Login - Scriptloaded</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link href="https://fonts.gstatic.com" rel="preconnect" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          primary: "#1A73E8",
          "background-light": "#f6f7f8",
          "background-dark": "#111821",
        },
        fontFamily: {
          display: ["Space Grotesk", "sans-serif"],
        },
      },
    },
  };
</script>
<style>
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  font-size: 20px;
}
.form-input-glass {
  background-color: rgba(255, 255, 255, 0.05);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.2s ease-in-out;
}
.dark .form-input-glass:focus-within {
  border-color: #1A73E8;
  box-shadow: 0 0 15px rgba(26, 115, 232, 0.35);
}
.glass-card {
  background-color: rgba(30, 41, 59, 0.55);
  backdrop-filter: blur(24px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.35);
}
</style>
 </head>
 <body class="font-display bg-background-light dark:bg-background-dark text-white">
 <div class="relative min-h-screen overflow-hidden">
   <div class="pointer-events-none absolute inset-0">
     <div class="absolute -left-24 top-0 h-[520px] w-[520px] rounded-full bg-[radial-gradient(circle_farthest-side,rgba(26,115,232,0.18),rgba(255,255,255,0))]"></div>
     <div class="absolute right-[-10%] bottom-[-20%] h-[520px] w-[520px] rounded-full bg-[radial-gradient(circle_farthest-side,rgba(26,115,232,0.15),rgba(255,255,255,0))]"></div>
   </div>
   <div class="relative flex min-h-screen flex-col">
     <?php include __DIR__ . '/../templates/partials/public_header.php'; ?>
     <main class="flex flex-1 items-center justify-center px-4 py-12 sm:px-6">
       <div class="w-full max-w-md space-y-8">
         <div class="glass-card w-full rounded-2xl p-8 shadow-2xl">
      <div class="text-center space-y-2">
        <h1 class="text-3xl font-bold text-white">Welcome Back</h1>
        <p class="text-sm text-slate-300">Log in to continue to your Scriptloaded dashboard.</p>
      </div>
      <?php if (!empty($errors)): ?>
        <div class="mt-4 rounded-lg border border-red-500/30 bg-red-500/20 p-4 text-sm text-red-100">
          <?php foreach ($errors as $error): ?>
            <p><?= escape_html($error); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <form action="login.php" method="post" class="mt-6 space-y-4">
        <input type="hidden" name="csrf" value="<?= escape_html(generate_csrf()); ?>" />
        <input type="hidden" name="redirect" value="<?= escape_html($redirectTarget); ?>" />
        <label class="flex flex-col gap-2 text-sm font-medium text-white">
          Email or Username
          <div class="flex items-center rounded-lg form-input-glass">
            <span class="material-symbols-outlined text-slate-400 pl-3">person</span>
            <input class="flex-1 bg-transparent p-3 text-base text-white placeholder:text-slate-400 focus:outline-none" type="email" name="email" placeholder="you@example.com" value="<?= escape_html($_POST['email'] ?? ''); ?>" required />
          </div>
        </label>
        <label class="flex flex-col gap-2 text-sm font-medium text-white">
          Password
          <div class="flex items-center rounded-lg form-input-glass">
            <span class="material-symbols-outlined text-slate-400 pl-3">lock</span>
            <input class="flex-1 bg-transparent p-3 text-base text-white placeholder:text-slate-400 focus:outline-none" type="password" name="password" placeholder="Enter your password" required data-password-input />
            <button class="px-3 text-slate-400" type="button" aria-label="Show password" data-password-toggle>
              <span class="material-symbols-outlined" data-password-icon>visibility_off</span>
            </button>
          </div>
        </label>
        <div class="text-right text-sm">
          <a class="text-primary font-medium hover:underline" href="forgot_password.php">Forgot Password?</a>
        </div>
        <button type="submit" class="w-full rounded-lg bg-primary py-3 text-base font-bold text-white shadow-lg shadow-primary/30 transition hover:bg-primary/90">
          Login
        </button>
      </form>
      <p class="pt-4 text-center text-sm text-slate-300">
        Don't have an account?
        <a class="font-semibold text-primary hover:underline" href="<?= escape_html(site_url('user/register.php')); ?>">Sign Up</a>
      </p>
    </div>
  </main>
 </div>
</div>
<script src="<?= escape_html(site_url('assets/js/mobile-menu.js')); ?>"></script>
<script>
(function(){
  const input = document.querySelector('[data-password-input]');
  const toggle = document.querySelector('[data-password-toggle]');
  const icon = document.querySelector('[data-password-icon]');
  if(!input || !toggle || !icon){
    return;
  }
  toggle.addEventListener('click', function(){
    const isHidden = input.getAttribute('type') === 'password';
    input.setAttribute('type', isHidden ? 'text' : 'password');
    icon.textContent = isHidden ? 'visibility' : 'visibility_off';
    toggle.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
  });
})();
</script>
</body></html>