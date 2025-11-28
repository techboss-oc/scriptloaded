<?php require __DIR__ . '/../inc/config.php'; ?>
<?php require __DIR__ . '/../inc/helpers.php'; ?>
<!DOCTYPE html>
<html class="dark" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width,initial-scale=1.0" name="viewport"/>
<title>Reset Password - Scriptloaded</title>
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
</head>
<body class="font-display bg-background-light dark:bg-background-dark">
<div class="relative flex min-h-screen w-full items-center justify-center overflow-hidden p-4">
  <div class="absolute inset-0 -z-10">
    <div class="absolute left-[-20%] top-[-10%] h-[500px] w-[500px] rounded-full bg-gradient-to-tr from-primary/40 to-cyan-400/40 opacity-60 blur-[150px]"></div>
    <div class="absolute right-[-20%] bottom-[-10%] h-[500px] w-[500px] rounded-full bg-gradient-to-tr from-primary/35 to-fuchsia-500/35 opacity-60 blur-[150px]"></div>
  </div>
  <div class="relative z-10 w-full max-w-lg space-y-8">
    <header class="flex items-center justify-between">
      <div class="flex items-center gap-3 text-slate-800 dark:text-white">
        <div class="size-10 text-primary">
          <svg fill="none" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
            <path clip-rule="evenodd" d="M39.475 21.6262C40.358 21.4363 40.6863 21.5589 40.7581 21.5934C40.7876 21.655 40.8547 21.857 40.8082 22.3336C40.7408 23.0255 40.4502 24.0046 39.8572 25.2301C38.6799 27.6631 36.5085 30.6631 33.5858 33.5858C30.6631 36.5085 27.6632 38.6799 25.2301 39.8572C24.0046 40.4502 23.0255 40.7407 22.3336 40.8082C21.8571 40.8547 21.6551 40.7875 21.5934 40.7581C21.5589 40.6863 21.4363 40.358 21.6262 39.475C21.8562 38.4054 22.4689 36.9657 23.5038 35.2817C24.7575 33.2417 26.5497 30.9744 28.7621 28.762C30.9744 26.5497 33.2417 24.7574 35.2817 23.5037C36.9657 22.4689 38.4054 21.8562 39.475 21.6262ZM4.41189 29.2403L18.7597 43.5881C19.8813 44.7097 21.4027 44.9179 22.7217 44.7893C24.0585 44.659 25.5148 44.1631 26.9723 43.4579C29.9052 42.0387 33.2618 39.5667 36.4142 36.4142C39.5667 33.2618 42.0387 29.9052 43.4579 26.9723C44.1631 25.5148 44.659 24.0585 44.7893 22.7217C44.9179 21.4027 44.7097 19.8813 43.5881 18.7597L29.2403 4.41187C27.8527 3.02428 25.8765 3.02573 24.2861 3.36776C22.6081 3.72863 20.7334 4.58419 18.8396 5.74801C16.4978 7.18716 13.9881 9.18353 11.5858 11.5858C9.18354 13.988 7.18717 16.4978 5.74802 18.8396C4.58421 20.7334 3.72865 22.6081 3.36778 24.2861C3.02574 25.8765 3.02429 27.8527 4.41189 29.2403Z" fill="currentColor" fill-rule="evenodd"></path>
          </svg>
        </div>
        <h2 class="text-xl font-bold">Scriptloaded</h2>
      </div>
      <button class="flex h-10 w-10 items-center justify-center rounded-lg border border-white/20 bg-white/10 text-slate-600 dark:text-slate-200">
        <span class="material-symbols-outlined">dark_mode</span>
      </button>
    </header>
    <main class="rounded-2xl border border-white/20 bg-white/30 p-6 shadow-xl backdrop-blur-2xl dark:border-white/10 dark:bg-black/30 sm:p-8">
      <div class="space-y-6">
        <div class="space-y-2">
          <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Reset Password</h1>
          <p class="text-sm text-slate-600 dark:text-slate-400">Enter your email address and we’ll send you a secure recovery link.</p>
        </div>
        <form action="forgot_password.php" method="post" class="flex flex-col gap-5">
          <input type="hidden" name="csrf" value="<?= escape_html(generate_csrf()); ?>" />
          <label class="flex flex-col gap-2 text-sm font-medium text-slate-800 dark:text-slate-200">
            Email Address
            <div class="relative flex items-center">
              <span class="material-symbols-outlined absolute left-3 text-slate-500 dark:text-slate-400">mail</span>
              <input class="w-full rounded-lg border border-white/30 bg-white/50 py-3 pl-10 pr-4 text-slate-900 placeholder-slate-500 focus:border-primary/70 focus:outline-none focus:ring-4 focus:ring-primary/15 dark:border-white/10 dark:bg-black/40 dark:text-white dark:placeholder-slate-400 dark:focus:ring-primary/30" type="email" name="email" placeholder="you@example.com" value="<?= escape_html($_POST['email'] ?? ''); ?>" required />
            </div>
          </label>
          <button type="submit" class="flex h-12 w-full items-center justify-center rounded-lg bg-primary text-base font-semibold text-white shadow-lg shadow-primary/30 transition hover:bg-primary/90">
            Send Reset Link
          </button>
        </form>
        <p class="text-center text-sm text-slate-600 dark:text-slate-400">Remembered your password? <a class="font-semibold text-primary hover:underline" href="login.php">Log In</a></p>
      </div>
    </main>
  </div>
</div>
</body></html>