<?php
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/helpers.php';
require __DIR__ . '/../inc/auth.php';
require __DIR__ . '/../inc/csrf.php';

require_admin();

$productId = (int)($_GET['id'] ?? 0);
if ($productId < 1) {
    http_response_code(404);
    echo 'Product not found';
    exit;
}

$productStmt = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$productStmt->execute([':id' => $productId]);
$product = $productStmt->fetch();
if (!$product) {
    http_response_code(404);
    echo 'Product not found';
    exit;
}

$adminPageTitle = 'Edit Product';
$activeNav = 'products';

$successMessage = isset($_GET['created']) ? 'Product created successfully.' : (isset($_GET['updated']) ? 'Product updated successfully.' : null);
$errors = [];

$input = [
    'title' => $_POST ? trim($_POST['title'] ?? '') : ($product['title'] ?? ''),
    'slug' => $_POST ? trim($_POST['slug'] ?? '') : ($product['slug'] ?? ''),
    'short_description' => $_POST ? trim($_POST['short_description'] ?? '') : ($product['short_description'] ?? ''),
    'long_description' => $_POST ? trim($_POST['long_description'] ?? '') : ($product['long_description'] ?? ''),
    'price_usd' => $_POST ? trim($_POST['price_usd'] ?? '') : (string)$product['price_usd'],
    'price_ngn' => $_POST ? trim($_POST['price_ngn'] ?? '') : (string)$product['price_ngn'],
    'category' => $_POST ? trim($_POST['category'] ?? '') : ($product['category'] ?? ''),
    'tags' => $_POST ? trim($_POST['tags'] ?? '') : (is_string($product['tags']) ? implode(', ', json_decode($product['tags'], true) ?? []) : ''),
    'preview_image' => ($product['preview_image'] ?? ''),
    'live_preview_url' => $_POST ? trim($_POST['live_preview_url'] ?? '') : ($product['live_preview_url'] ?? ''),
    'video_url' => $_POST ? trim($_POST['video_url'] ?? '') : ($product['youtube_overview'] ?? $product['youtube_install'] ?? ''),
    'gallery' => is_string($product['gallery']) ? implode(PHP_EOL, json_decode($product['gallery'], true) ?? []) : '',
    'features' => $_POST ? trim($_POST['features'] ?? '') : (is_string($product['features']) ? implode(PHP_EOL, json_decode($product['features'], true) ?? []) : ''),
    'file_path' => $_POST ? trim($_POST['file_path'] ?? '') : ($product['file_path'] ?? ''),
    'is_active' => $_POST ? (isset($_POST['is_active']) ? 1 : 0) : (int)$product['is_active'],
];
  $categorySuggestions = get_product_categories();
  $categoryDatalistId = 'product-category-options';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid request token.';
    }
    if ($input['title'] === '') {
        $errors[] = 'Title is required.';
    }
    if ($input['slug'] === '') {
        $errors[] = 'Slug is required.';
    }
    if (!is_numeric($input['price_usd'])) {
        $errors[] = 'Price in USD must be numeric.';
    }
    if (!is_numeric($input['price_ngn'])) {
        $errors[] = 'Price in NGN must be numeric.';
    }
    foreach ([
      ['field' => 'video_url', 'limit' => 255, 'label' => 'Installation & overview video URL'],
      ['field' => 'live_preview_url', 'limit' => 255, 'label' => 'Live preview URL'],
      ['field' => 'file_path', 'limit' => 255, 'label' => 'File path'],
      ['field' => 'category', 'limit' => 100, 'label' => 'Category'],
    ] as $constraint) {
      $value = $input[$constraint['field']] ?? '';
      if ($value !== '' && strlen($value) > $constraint['limit']) {
        $errors[] = $constraint['label'] . ' must be ' . $constraint['limit'] . ' characters or fewer.';
      }
    }
    if (!$errors) {
        $slugCheckStmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE slug = :slug AND id <> :id');
        $slugCheckStmt->execute([':slug' => $input['slug'], ':id' => $productId]);
        if ($slugCheckStmt->fetchColumn() > 0) {
            $errors[] = 'Slug already in use by another product.';
        }
    }
    if (!$errors) {
        $galleryArray = array_values(array_filter(array_map('trim', preg_split("/(\r?\n)+/", $input['gallery']))));
        $featuresArray = array_values(array_filter(array_map('trim', preg_split("/(\r?\n)+/", $input['features']))));
        $tagsArray = array_values(array_filter(array_map('trim', explode(',', $input['tags']))));
        $stmt = $pdo->prepare('UPDATE products SET title=:title, slug=:slug, short_description=:short_description, long_description=:long_description, preview_image=:preview_image, gallery=:gallery, youtube_overview=:youtube_overview, youtube_install=:youtube_install, live_preview_url=:live_preview_url, features=:features, price_usd=:price_usd, price_ngn=:price_ngn, category=:category, tags=:tags, file_path=:file_path, is_active=:is_active WHERE id=:id');
        $stmt->execute([
            ':title' => $input['title'],
            ':slug' => $input['slug'],
            ':short_description' => $input['short_description'],
            ':long_description' => $input['long_description'],
            ':preview_image' => $input['preview_image'] ?: null,
            ':gallery' => $galleryArray ? json_encode($galleryArray) : null,
          ':youtube_overview' => $input['video_url'] ?: null,
          ':youtube_install' => $input['video_url'] ?: null,
          ':live_preview_url' => $input['live_preview_url'] ?: null,
          ':features' => $featuresArray ? json_encode($featuresArray) : null,
            ':price_usd' => (float)$input['price_usd'],
            ':price_ngn' => (int)round($input['price_ngn']),
            ':category' => $input['category'] ?: null,
            ':tags' => $tagsArray ? json_encode($tagsArray) : null,
            ':file_path' => $input['file_path'] ?: null,
            ':is_active' => $input['is_active'] ? 1 : 0,
            ':id' => $productId,
        ]);
        header('Location: ' . site_url('admin/product_edit.php?id=' . $productId . '&updated=1'));
        exit;
    }
}
?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/sidebar.php'; ?>
<main class="flex-1 flex flex-col bg-transparent">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <section class="admin-content flex-1 space-y-8 overflow-y-auto px-4 py-6 sm:px-6 lg:px-10" data-admin-content>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white">Edit Product</h1>
        <p class="text-sm text-white/70">Update listings and metadata.</p>
      </div>
      <a href="<?= escape_html(site_url('admin/products.php')); ?>" class="text-sm font-semibold text-primary hover:underline">Back to products</a>
    </div>
    <?php if ($successMessage): ?>
      <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
        <?= escape_html($successMessage); ?>
      </div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
        <?php foreach ($errors as $error): ?>
          <p><?= escape_html($error); ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <form method="post" class="space-y-8">
      <input type="hidden" name="csrf" value="<?= escape_html(generate_csrf()); ?>" />
      <div class="admin-panel grid gap-6 rounded-2xl p-6 shadow-soft lg:grid-cols-2">
        <div class="space-y-4">
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Product Title
            <input type="text" name="title" value="<?= escape_html($input['title']); ?>" required class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Slug
            <input type="text" name="slug" value="<?= escape_html($input['slug']); ?>" required class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Short Description
            <textarea name="short_description" rows="3" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white"><?= escape_html($input['short_description']); ?></textarea>
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Long Description
            <textarea name="long_description" rows="6" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white"><?= escape_html($input['long_description']); ?></textarea>
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Feature Highlights (one per line)
            <textarea name="features" rows="5" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white"><?= escape_html($input['features']); ?></textarea>
            <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">Each line becomes a bullet point on the product page.</span>
          </label>
          <label class="inline-flex items-center gap-3 text-sm font-semibold text-slate-900 dark:text-white">
            <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-slate-400 text-primary focus:ring-primary/50" <?= $input['is_active'] ? 'checked' : ''; ?> />
            Visible in storefront
          </label>
        </div>
        <div class="space-y-4">
          <div class="grid gap-4 md:grid-cols-2">
            <label class="block text-sm font-semibold text-slate-900 dark:text-white">
              Price (USD)
              <input type="number" min="0" step="0.01" name="price_usd" value="<?= escape_html($input['price_usd']); ?>" required class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
            </label>
            <label class="block text-sm font-semibold text-slate-900 dark:text-white">
              Price (NGN)
              <input type="number" min="0" step="1" name="price_ngn" value="<?= escape_html($input['price_ngn']); ?>" required class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
            </label>
          </div>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Category
            <input type="text" name="category" list="<?= escape_html($categoryDatalistId); ?>" value="<?= escape_html($input['category']); ?>" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
            <?php if ($categorySuggestions): ?>
              <datalist id="<?= escape_html($categoryDatalistId); ?>">
                <?php foreach ($categorySuggestions as $categoryOption): ?>
                  <option value="<?= escape_html($categoryOption['label']); ?>"></option>
                <?php endforeach; ?>
              </datalist>
            <?php endif; ?>
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Tags (comma separated)
            <input type="text" name="tags" value="<?= escape_html($input['tags']); ?>" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Live Preview URL
            <input type="url" name="live_preview_url" value="<?= escape_html($input['live_preview_url']); ?>" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Installation &amp; Overview Video URL
            <input type="url" name="video_url" value="<?= escape_html($input['video_url']); ?>" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
            <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">Provide one YouTube or embeddable link used for both walkthroughs.</span>
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Product File Path
            <input type="text" name="file_path" value="<?= escape_html($input['file_path']); ?>" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
          </label>
        </div>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-soft hover:bg-primary/90">
          <span class="material-symbols-outlined text-base">save</span>
          Update Product
        </button>
      </div>
    </form>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
