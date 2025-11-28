<?php require __DIR__ . '/../inc/config.php'; ?>
<?php require __DIR__ . '/../inc/helpers.php'; ?>
<?php require __DIR__ . '/../inc/auth.php'; ?>
<?php require __DIR__ . '/../inc/csrf.php'; ?>
<?php
$errors = [];
$currentUser = current_user();
if ($currentUser && !empty($currentUser['is_admin'])) {
  header('Location: ' . site_url('admin/index.php'));
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
    $isValidAdmin = $user && !empty($user['is_admin']) && password_verify($password, $user['password_hash']);
    if (!$isValidAdmin) {
      $errors[] = 'Invalid admin credentials.';
    } else {
      login_user((int)$user['id'], true);
      header('Location: ' . site_url('admin/index.php'));
      exit;
    }
  }
}
?>
<!DOCTYPE html>
<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width,initial-scale=1.0" name="viewport"/>
<title>Admin Login - Scriptloaded</title>
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
          "background-dark": "#0b1220",
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
}
.glass-card {
  background: rgba(13, 19, 33, 0.75);
  border: 1px solid rgba(255, 255, 255, 0.08);
  box-shadow: 0 20px 60px rgba(1, 9, 23, 0.55);
}
</style>
</head>
<body class="font-display bg-background-light dark:bg-background-dark">
<div class="relative flex min-h-screen items-center justify-center overflow-hidden px-4">
  <div class="absolute inset-0 -z-10">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(26,115,232,0.25),_transparent)]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_bottom,_rgba(12,15,35,0.9),_transparent)]"></div>
  </div>
  <main class="w-full max-w-lg">
    <div class="flex flex-col items-center gap-3 text-center text-white">
      <span class="material-symbols-outlined text-4xl text-primary">shield_person</span>
      <p class="text-xs uppercase tracking-[0.4em] text-primary/70">Admin Console</p>
      <h1 class="text-4xl font-bold">Scriptloaded Admin</h1>
      <p class="text-sm text-white/70">Sign in with your administrator credentials to manage the marketplace.</p>
    </div>
    <section class="glass-card mt-8 rounded-3xl p-8 text-white">
      <?php if (!empty($errors)): ?>
        <div class="mb-6 rounded-2xl border border-red-500/40 bg-red-500/15 p-4 text-sm text-red-100">
          <?php foreach ($errors as $error): ?>
            <p><?= escape_html($error); ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <form action="login.php" method="post" class="space-y-5">
        <input type="hidden" name="csrf" value="<?= escape_html(generate_csrf()); ?>" />
        <label class="flex flex-col gap-2 text-sm font-medium text-white/80">
          Admin Email
          <div class="flex items-center rounded-2xl border border-white/10 bg-white/5 px-3">
            <span class="material-symbols-outlined text-primary/80">alternate_email</span>
            <input class="flex-1 bg-transparent p-3 text-base text-white placeholder:text-white/50 focus:outline-none" type="email" name="email" placeholder="admin@example.com" value="<?= escape_html($_POST['email'] ?? ''); ?>" required />
          </div>
        </label>
        <label class="flex flex-col gap-2 text-sm font-medium text-white/80">
          Password
          <div class="flex items-center rounded-2xl border border-white/10 bg-white/5 px-3">
            <span class="material-symbols-outlined text-primary/80">lock</span>
            <input class="flex-1 bg-transparent p-3 text-base text-white placeholder:text-white/50 focus:outline-none" type="password" name="password" placeholder="Enter password" required />
          </div>
        </label>
        <button type="submit" class="w-full rounded-2xl bg-primary py-3 text-base font-semibold text-white shadow-lg shadow-primary/30 transition hover:bg-primary/90">
          Sign In
        </button>
      </form>
      <p class="pt-6 text-center text-sm text-white/70">
        Need to access the creator dashboard? <a class="font-semibold text-primary hover:underline" href="<?= escape_html(site_url('user/login.php')); ?>">Use the user login</a>.
      </p>
    </section>
  </main>
</div>
</body></html>
