<?php
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/helpers.php';
require __DIR__ . '/../inc/auth.php';
require __DIR__ . '/../inc/csrf.php';

require_admin();

$adminPageTitle = 'Product Categories';
$activeNav = 'categories';

$errors = [];
$successMessage = null;
if (isset($_GET['saved'])) {
  $successMessage = 'Category saved successfully.';
} elseif (isset($_GET['deleted'])) {
  $successMessage = 'Category deleted successfully.';
}

$categories = get_product_categories();
$categoryMap = [];
foreach ($categories as $category) {
  $categoryMap[$category['slug']] = $category;
}

$editSlug = trim($_GET['edit'] ?? '');
$editCategory = ($editSlug !== '' && isset($categoryMap[$editSlug])) ? $categoryMap[$editSlug] : null;
if ($editSlug !== '' && !$editCategory) {
  $errors[] = 'The requested category could not be found (it may have been deleted).';
}

$input = [
  'label' => $editCategory['label'] ?? '',
  'type' => $editCategory['type'] ?? '',
  'description' => $editCategory['description'] ?? '',
];
$originalSlugValue = $editCategory['slug'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? 'save';
  if (!validate_csrf($_POST['csrf'] ?? '')) {
    $errors[] = 'Invalid request token. Please refresh and try again.';
  }

  if (!$errors && $action === 'delete') {
    $slugToDelete = trim($_POST['slug'] ?? '');
    if ($slugToDelete === '') {
      $errors[] = 'Missing category reference.';
    } elseif (!isset($categoryMap[$slugToDelete])) {
      $errors[] = 'Category not found or already removed.';
    }

    if (!$errors) {
      $remaining = array_values(array_filter($categories, function ($category) use ($slugToDelete) {
        return $category['slug'] !== $slugToDelete;
      }));
      if (!save_product_categories($remaining)) {
        $errors[] = 'Unable to delete category right now. Please try again.';
      } else {
        header('Location: ' . site_url('admin/categories.php?deleted=1'));
        exit;
      }
    }
  } elseif (!$errors) {
    $input['label'] = trim($_POST['label'] ?? '');
    $input['type'] = trim($_POST['type'] ?? '');
    $input['description'] = trim($_POST['description'] ?? '');
    $originalSlugValue = trim($_POST['original_slug'] ?? '');

    if ($input['label'] === '') {
      $errors[] = 'Category name is required.';
    }

    $newSlug = slugify($input['label']);
    if (!$errors) {
      foreach ($categories as $category) {
        if ($originalSlugValue !== '' && $category['slug'] === $originalSlugValue) {
          continue;
        }
        if ($category['slug'] === $newSlug) {
          $errors[] = 'A category with that name already exists.';
          break;
        }
      }
    }

    if (!$errors) {
      $updated = [];
      foreach ($categories as $category) {
        if ($originalSlugValue !== '' && $category['slug'] === $originalSlugValue) {
          continue;
        }
        $updated[] = $category;
      }
      $updated[] = [
        'slug' => $newSlug,
        'label' => $input['label'],
        'type' => $input['type'] !== '' ? $input['type'] : null,
        'description' => $input['description'] !== '' ? $input['description'] : null,
      ];
      if (!save_product_categories($updated)) {
        $errors[] = 'Unable to save category right now. Please try again.';
      } else {
        header('Location: ' . site_url('admin/categories.php?saved=1'));
        exit;
      }
    }
  }

  $categories = get_product_categories(true);
  $categoryMap = [];
  foreach ($categories as $category) {
    $categoryMap[$category['slug']] = $category;
  }
}

$csrfToken = generate_csrf();
$isEditing = $originalSlugValue !== '';
?>
<?php require __DIR__ . '/partials/header.php'; ?>
<?php require __DIR__ . '/partials/sidebar.php'; ?>
<main class="flex-1 flex flex-col bg-transparent">
  <?php require __DIR__ . '/partials/topbar.php'; ?>
  <section class="admin-content flex-1 space-y-8 overflow-y-auto px-4 py-6 sm:px-6 lg:px-10" data-admin-content>
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-white">Product Categories</h1>
        <p class="text-sm text-white/70">Manage the catalog groupings buyers see throughout Scriptloaded.</p>
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
    <section class="admin-panel rounded-2xl p-6 shadow-soft">
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
          <h2 class="text-lg font-semibold text-white">Existing Categories</h2>
          <p class="text-sm text-white/70">Edit or delete categories to keep the storefront navigation tidy.</p>
        </div>
        <div class="text-xs font-semibold uppercase tracking-wide text-white/60">
          <?= count($categories); ?> total
        </div>
      </div>
      <?php if ($categories): ?>
        <div class="mt-6 overflow-x-auto" data-admin-table-wrapper>
          <table class="admin-table min-w-full divide-y divide-white/10 text-sm text-white/80" data-admin-table>
            <thead class="text-xs uppercase tracking-wide text-white/60">
              <tr>
                <th class="px-4 py-2 text-left">Category</th>
                <th class="px-4 py-2 text-left">Type</th>
                <th class="px-4 py-2 text-left">Slug</th>
                <th class="px-4 py-2 text-left">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
              <?php foreach ($categories as $category): ?>
                <tr class="align-top">
                  <td class="px-4 py-4">
                    <p class="font-semibold text-white"><?= escape_html($category['label']); ?></p>
                    <?php if (!empty($category['description'])): ?>
                      <p class="mt-1 text-xs text-white/60"><?= escape_html($category['description']); ?></p>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-4 text-white/70">
                    <?= $category['type'] ? escape_html($category['type']) : '—'; ?>
                  </td>
                  <td class="px-4 py-4">
                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/80">
                      <?= escape_html($category['slug']); ?>
                    </span>
                  </td>
                  <td class="px-4 py-4">
                    <div class="flex flex-wrap gap-2">
                      <a href="<?= escape_html(site_url('admin/categories.php?edit=' . rawurlencode($category['slug']))); ?>" class="inline-flex items-center gap-1 rounded-2xl border border-primary/30 px-3 py-1 text-xs font-semibold text-primary hover:bg-primary/10">
                        <span class="material-symbols-outlined text-sm">edit</span>
                        Edit
                      </a>
                      <form method="post" class="inline-flex" onsubmit="return confirm('Delete this category?');">
                        <input type="hidden" name="csrf" value="<?= escape_html($csrfToken); ?>" />
                        <input type="hidden" name="action" value="delete" />
                        <input type="hidden" name="slug" value="<?= escape_html($category['slug']); ?>" />
                        <button type="submit" class="inline-flex items-center gap-1 rounded-2xl border border-rose-500/40 px-3 py-1 text-xs font-semibold text-rose-500 hover:bg-rose-500/10">
                          <span class="material-symbols-outlined text-sm">delete</span>
                          Delete
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="mt-6 rounded-2xl border border-dashed border-white/30 bg-white/5 p-6 text-sm text-white/70">
          No categories yet. Use the form below to create your first grouping.
        </div>
      <?php endif; ?>
    </section>

    <section class="admin-panel rounded-2xl p-6 shadow-soft">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
          <h2 class="text-lg font-semibold text-white"><?= $isEditing ? 'Edit Category' : 'Add Category'; ?></h2>
          <p class="text-sm text-white/70">
            <?= $isEditing ? 'Update the selected category details.' : 'Create a new category for upcoming products.'; ?>
          </p>
        </div>
        <?php if ($isEditing): ?>
          <a href="<?= escape_html(site_url('admin/categories.php')); ?>" class="text-sm font-semibold text-primary hover:underline">Cancel edit</a>
        <?php endif; ?>
      </div>
      <form method="post" class="mt-6 space-y-4">
        <input type="hidden" name="csrf" value="<?= escape_html($csrfToken); ?>" />
        <input type="hidden" name="action" value="save" />
        <input type="hidden" name="original_slug" value="<?= escape_html($originalSlugValue); ?>" />
        <label class="block text-sm font-semibold text-slate-900 dark:text-white">
          Category Name
          <input type="text" name="label" value="<?= escape_html($input['label']); ?>" required class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
        </label>
        <label class="block text-sm font-semibold text-slate-900 dark:text-white">
          Type (optional)
          <input type="text" name="type" value="<?= escape_html($input['type']); ?>" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" />
        </label>
        <label class="block text-sm font-semibold text-slate-900 dark:text-white">
          Short Description
          <textarea name="description" rows="3" class="mt-2 w-full rounded-xl border border-white/20 bg-transparent px-4 py-3 text-sm text-slate-900 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30 dark:border-slate-700 dark:text-white" placeholder="Highlight what ships in this bundle."><?= escape_html($input['description']); ?></textarea>
        </label>
        <div class="flex justify-end">
          <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-soft hover:bg-primary/90">
            <span class="material-symbols-outlined text-base">save</span>
            <?= $isEditing ? 'Update Category' : 'Add Category'; ?>
          </button>
        </div>
      </form>
    </section>
  </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
