<?php
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/helpers.php';
require __DIR__ . '/../inc/auth.php';
require __DIR__ . '/../inc/csrf.php';

require_admin();

$adminPageTitle = 'Create Product';
$activeNav = 'products';

$errors = [];
$input = [
    'title' => trim($_POST['title'] ?? ''),
    'slug' => trim($_POST['slug'] ?? ''),
    'short_description' => trim($_POST['short_description'] ?? ''),
    'long_description' => trim($_POST['long_description'] ?? ''),
    'price_usd' => trim($_POST['price_usd'] ?? ''),
    'price_ngn' => trim($_POST['price_ngn'] ?? ''),
    'category' => trim($_POST['category'] ?? ''),
    'tags' => trim($_POST['tags'] ?? ''),
    'live_preview_url' => trim($_POST['live_preview_url'] ?? ''),
    'video_url' => trim($_POST['video_url'] ?? ''),
    'gallery' => trim($_POST['gallery'] ?? ''),
    'features' => trim($_POST['features'] ?? ''),
    'file_path' => trim($_POST['file_path'] ?? ''),
];
  $categorySuggestions = get_product_categories();
  $categoryDatalistId = 'product-category-options';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid request token. Please refresh and try again.';
    }
    if ($input['title'] === '') {
        $errors[] = 'Product title is required.';
    }
    if ($input['slug'] === '' && $input['title'] !== '') {
        $input['slug'] = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $input['title']));
        $input['slug'] = trim($input['slug'], '-');
    }
    if ($input['slug'] === '') {
        $errors[] = 'Product slug is required.';
    }
    if (!is_numeric($input['price_usd'])) {
        $errors[] = 'Price in USD must be numeric.';
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
    $priceUsd = (float)$input['price_usd'];
    $priceNgn = $input['price_ngn'] !== '' && is_numeric($input['price_ngn']) ? (float)$input['price_ngn'] : convert_usd_to_ngn($priceUsd, get_setting('currency_rate_usd_to_ngn'));
    if ($priceUsd <= 0) {
        $errors[] = 'Price in USD must be greater than zero.';
    }
    $slugCheckStmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE slug = :slug');
    $slugCheckStmt->execute([':slug' => $input['slug']]);
    if ($slugCheckStmt->fetchColumn() > 0) {
        $errors[] = 'A product with that slug already exists.';
    }
    if (!$errors) {
      $galleryArray = array_values(array_filter(array_map('trim', preg_split("/(\r?\n)+/", $input['gallery']))));
      $tagsArray = array_values(array_filter(array_map('trim', explode(',', $input['tags']))));
      $previewImageUrl = null;
      $featuresArray = array_values(array_filter(array_map('trim', preg_split("/(\r?\n)+/", $input['features']))));

      if (isset($_FILES['preview_upload']) && is_array($_FILES['preview_upload'])) {
        $previewError = $_FILES['preview_upload']['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($previewError !== UPLOAD_ERR_NO_FILE) {
          try {
            $previewImageUrl = store_product_image_upload($_FILES['preview_upload'], 'preview');
          } catch (Throwable $e) {
            $errors[] = 'Preview image upload failed: ' . $e->getMessage();
          }
        }
      }

      if (isset($_FILES['gallery_uploads']) && is_array($_FILES['gallery_uploads']['name'])) {
        $uploadCount = count($_FILES['gallery_uploads']['name']);
        for ($i = 0; $i < $uploadCount; $i++) {
          $error = $_FILES['gallery_uploads']['error'][$i] ?? UPLOAD_ERR_NO_FILE;
          if ($error === UPLOAD_ERR_NO_FILE) {
            continue;
          }
          $file = [
            'name' => $_FILES['gallery_uploads']['name'][$i] ?? null,
            'type' => $_FILES['gallery_uploads']['type'][$i] ?? null,
            'tmp_name' => $_FILES['gallery_uploads']['tmp_name'][$i] ?? null,
            'error' => $error,
            'size' => $_FILES['gallery_uploads']['size'][$i] ?? null,
          ];
          try {
            $galleryArray[] = store_product_image_upload($file, 'gallery');
          } catch (Throwable $e) {
            $errors[] = 'Gallery upload #' . ($i + 1) . ' failed: ' . $e->getMessage();
          }
        }
      }

      if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO products (title, slug, short_description, long_description, preview_image, gallery, youtube_overview, youtube_install, live_preview_url, features, price_usd, price_ngn, category, tags, file_path, is_active) VALUES (:title,:slug,:short_description,:long_description,:preview_image,:gallery,:youtube_overview,:youtube_install,:live_preview_url,:features,:price_usd,:price_ngn,:category,:tags,:file_path,1)');
        $stmt->execute([
          ':title' => $input['title'],
          ':slug' => $input['slug'],
          ':short_description' => $input['short_description'],
          ':long_description' => $input['long_description'],
          ':preview_image' => $previewImageUrl,
          ':gallery' => $galleryArray ? json_encode($galleryArray) : null,
          ':youtube_overview' => $input['video_url'] ?: null,
          ':youtube_install' => $input['video_url'] ?: null,
          ':live_preview_url' => $input['live_preview_url'] ?: null,
          ':features' => $featuresArray ? json_encode($featuresArray) : null,
          ':price_usd' => $priceUsd,
          ':price_ngn' => (int)round($priceNgn),
          ':category' => $input['category'] ?: null,
          ':tags' => $tagsArray ? json_encode($tagsArray) : null,
          ':file_path' => $input['file_path'] ?: null,
        ]);
        $newId = (int)$pdo->lastInsertId();
        header('Location: ' . site_url('admin/product_edit.php?id=' . $newId . '&created=1'));
        exit;
      }
    }
}
?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/sidebar.php'; ?>
<main class="flex-1 flex flex-col bg-white/40 dark:bg-slate-950/30">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <section class="admin-content flex-1 space-y-8 overflow-y-auto px-4 py-6 sm:px-6 lg:px-10" data-admin-content>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Create Product</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Publish new assets to the Scriptloaded marketplace.</p>
      </div>
      <a href="<?= escape_html(site_url('admin/products.php')); ?>" class="text-sm font-semibold text-primary hover:underline">Back to products</a>
    </div>
    <?php if ($errors): ?>
      <div class="rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-100">
        <?php foreach ($errors as $error): ?>
          <p><?= escape_html($error); ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="space-y-8">
      <input type="hidden" name="csrf" value="<?= escape_html(generate_csrf()); ?>" />
      <div class="admin-panel grid gap-6 rounded-2xl p-6 shadow-soft lg:grid-cols-2">
        <div class="space-y-4">
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Product Title
            <input type="text" name="title" value="<?= escape_html($input['title']); ?>" required class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 placeholder:text-slate-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Slug
            <input type="text" name="slug" value="<?= escape_html($input['slug']); ?>" required class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 placeholder:text-slate-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Short Description
            <textarea name="short_description" rows="3" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 placeholder:text-slate-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white"><?= escape_html($input['short_description']); ?></textarea>
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Long Description
            <textarea name="long_description" rows="6" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 placeholder:text-slate-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white"><?= escape_html($input['long_description']); ?></textarea>
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Feature Highlights (one per line)
            <textarea name="features" rows="5" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 placeholder:text-slate-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white"><?= escape_html($input['features']); ?></textarea>
            <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">Each line becomes a bullet point in the product page feature list.</span>
          </label>
        </div>
        <div class="space-y-4">
          <div class="grid gap-4 md:grid-cols-2">
            <label class="block text-sm font-semibold text-slate-900 dark:text-white">
              Price (USD)
              <input type="number" min="0" step="0.01" name="price_usd" value="<?= escape_html($input['price_usd']); ?>" required class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 placeholder:text-slate-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
            </label>
            <label class="block text-sm font-semibold text-slate-900 dark:text-white">
              Price (NGN)
              <input type="number" min="0" step="1" name="price_ngn" value="<?= escape_html($input['price_ngn']); ?>" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 placeholder:text-slate-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
              <span class="mt-1 block text-xs font-normal text-slate-500 dark:text-slate-400">Leave blank to auto-calc using currency settings.</span>
            </label>
          </div>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Category
            <input type="text" name="category" list="<?= escape_html($categoryDatalistId); ?>" value="<?= escape_html($input['category']); ?>" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 placeholder:text-slate-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
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
            <input type="text" name="tags" value="<?= escape_html($input['tags']); ?>" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 placeholder:text-slate-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Live Preview URL
            <input type="url" name="live_preview_url" value="<?= escape_html($input['live_preview_url']); ?>" placeholder="https://" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 placeholder:text-slate-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Installation &amp; Overview Video URL
            <input type="url" name="video_url" value="<?= escape_html($input['video_url']); ?>" placeholder="https://" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 placeholder:text-slate-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
            <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">Provide one YouTube or embeddable link for both overview and installation.</span>
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Upload Preview Image
            <input type="file" name="preview_upload" accept="image/*" class="mt-2 w-full rounded-xl border border-dashed border-white/30 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
            <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">PNG, JPG, GIF, or WEBP up to 5&nbsp;MB.</span>
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Gallery Image Uploads
            <input type="file" name="gallery_uploads[]" multiple accept="image/*" class="mt-2 w-full rounded-xl border border-dashed border-white/30 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
            <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">Add multiple screenshots; uploads are hosted automatically.</span>
          </label>
          <label class="block text-sm font-semibold text-slate-900 dark:text-white">
            Product File Path (storage reference)
            <input type="text" name="file_path" value="<?= escape_html($input['file_path']); ?>" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 placeholder:text-slate-500 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
            <span class="mt-1 block text-xs text-slate-500 dark:text-slate-400">Upload assets outside webroot and store relative path.</span>
          </label>
        </div>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-soft hover:bg-primary/90">
          <span class="material-symbols-outlined text-base">save</span>
          Save Product
        </button>
      </div>
    </form>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
