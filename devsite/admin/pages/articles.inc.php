<?php
declare(strict_types=1);

/*
 * TheRegs CMS - Admin Articles
 * Version: Phase 1.0 Fixed
 * Build: 2026-06-25
 *
 * Features:
 * - Add/edit articles
 * - Move articles between sections/categories
 * - Custom categories
 * - Title icon + article image fields
 * - Improved article listing table
 * - Disable/restore workflow
 * - Permanent delete for inactive articles
 * - Audit logging
 */


if (!defined('IN_ADMIN')) {
    exit;
}

global $db, $request, $user, $site_sections;

$section_key = $request->variable('section', 'wow');

if (!isset($site_sections[$section_key])) {
    $section_key = 'wow';
}

$section_name = $site_sections[$section_key]['name'] ?? $section_key;

$default_categories = [
    'introduction' => 'Introduction',
    'history' => 'History',
    'lore' => 'Lore',
    'guides' => 'Guides',
    'news' => 'News',
    'faq' => 'FAQ',
    'rules' => 'Rules',
];

$category = trim($request->variable('category', 'introduction', true));
$category = preg_replace('/[^a-z0-9_-]+/i', '-', $category);
$category = trim((string) $category, '-');
$category = $category !== '' ? strtolower($category) : 'introduction';

$form_key = 'admin_articles';
add_form_key($form_key);

$message = '';
$error = '';

$action = $request->variable('action', '');
$id = $request->variable('id', 0);

if ($request->variable('added', 0) === 1) {
    $message = 'Article added.';
}

if ($request->variable('updated', 0) === 1) {
    $message = 'Article updated.';
}

if ($request->variable('deleted', 0) === 1) {
    $message = 'Article permanently deleted.';
}

function admin_article_slug(string $value): string
{
    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
    $value = trim((string) $value, '-');

    return $value !== '' ? $value : 'article-entry';
}

function admin_article_category_key(string $value): string
{
    $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9_-]+/i', '-', $value);
    $value = trim((string) $value, '-');

    return $value !== '' ? $value : 'introduction';
}

function admin_article_category_label(string $category): string
{
    return ucwords(str_replace(['_', '-'], ' ', $category));
}

function admin_article_date(?string $value): string
{
    if (empty($value)) {
        return '';
    }

    $timestamp = strtotime($value);

    return $timestamp ? date('Y-m-d H:i', $timestamp) : (string) $value;
}


function admin_article_next_sort(string $section_key, string $category): int
{
    global $db;

    if ($category === 'news') {
        return 0;
    }

    $sql = "SELECT COALESCE(MAX(sort_order),0) + 10 AS next_sort
            FROM site_articles
            WHERE section_key = '" . $db->sql_escape($section_key) . "'
            AND category = '" . $db->sql_escape($category) . "'";

    $result = $db->sql_query($sql);
    $next = (int) $db->sql_fetchfield('next_sort');
    $db->sql_freeresult($result);

    return $next;
}

function admin_article_decode_html(string $value): string
{
    $previous = null;
    $decoded = $value;

    while ($decoded !== $previous) {
        $previous = $decoded;
        $decoded = html_entity_decode($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    return $decoded;
}

function admin_redirect_articles(string $section_key, string $category): void
{
    redirect('/admin/?page=articles&section=' . rawurlencode($section_key) . '&category=' . rawurlencode($category));
}

function admin_articles_form_url(string $section_key, string $category, string $action, int $id = 0): string
{
    $url = '/admin/?page=articles&section=' . rawurlencode($section_key) .
        '&category=' . rawurlencode($category) .
        '&action=' . rawurlencode($action);

    if ($id > 0) {
        $url .= '&id=' . $id;
    }

    return $url;
}

function admin_audit_write_local(string $section_key, string $page_name, string $action_name, string $item_table, int $item_id, string $item_title, string $details = ''): void
{
    global $db, $user, $request;

    $data = [
        'user_id' => (int) ($user->data['user_id'] ?? 0),
        'username' => (string) ($user->data['username'] ?? ''),
        'section_key' => $section_key,
        'page_name' => $page_name,
        'action_name' => $action_name,
        'item_table' => $item_table,
        'item_id' => $item_id > 0 ? $item_id : null,
        'item_title' => $item_title,
        'ip_address' => (string) $request->server('REMOTE_ADDR', ''),
        'user_agent' => substr((string) $request->server('HTTP_USER_AGENT', ''), 0, 255),
        'details' => $details,
        'created_at' => date('Y-m-d H:i:s'),
    ];

    $db->sql_query('INSERT INTO admin_audit_log ' . $db->sql_build_array('INSERT', $data));
}

$edit_entry = [
    'id' => 0,
    'section_key' => $section_key,
    'category' => $category,
    'title' => '',
    'title_icon' => '',
    'slug' => '',
    'body' => '',
    'image_url' => '',
    'sort_order' => 0,
    'is_active' => 1,
];

if ($request->is_set_post('save')) {
    if (!check_form_key($form_key)) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $id = $request->variable('id', 0);

        /*
         * Allow an article to be moved to another section/category while editing.
         * This fixes accidental posts created under the wrong site or category.
         */
        $posted_section_key = $request->variable('article_section', $section_key);
        $posted_section_key = strtolower(trim(preg_replace('/[^a-z0-9_-]+/i', '-', $posted_section_key), '-'));

        if (isset($site_sections[$posted_section_key])) {
            $section_key = $posted_section_key;
            $section_name = $site_sections[$section_key]['name'] ?? $section_key;
        }

        $posted_category = trim($request->variable('category', $category, true));
        $custom_category = trim($request->variable('category_custom', '', true));

        if ($custom_category !== '') {
            $posted_category = $custom_category;
        }

        $category = admin_article_category_key($posted_category);

        $title = admin_article_decode_html(trim($request->variable('title', '', true)));
        $slug = trim($request->variable('slug', '', true));
        $body = admin_article_decode_html(trim($request->variable('body', '', true)));
        $image_url = trim($request->variable('image_url', '', true));
        $title_icon = trim($request->variable('title_icon', '', true));
        $sort_order = $request->variable('sort_order', 0);

        if ($id === 0 && $sort_order === 0) {
            $sort_order = admin_article_next_sort($section_key, $category);
        }

        $is_active = $request->variable('is_active', 1);

        $slug = ($slug === '') ? admin_article_slug($title) : admin_article_slug($slug);

        $edit_entry = array_merge($edit_entry, [
            'id' => $id,
            'section_key' => $section_key,
            'category' => $category,
            'title' => $title,
            'slug' => $slug,
            'body' => $body,
            'image_url' => $image_url,
            'title_icon' => $title_icon,
            'sort_order' => $sort_order,
            'is_active' => $is_active ? 1 : 0,
        ]);

        if ($title === '') {
            $error = 'Article title is required.';
        } elseif ($body === '') {
            $error = 'Article body is required.';
        } elseif ($image_url !== '' && !filter_var($image_url, FILTER_VALIDATE_URL) && str_starts_with($image_url, '/') === false) {
            $error = 'Image URL must be a valid URL or local path beginning with /.';
        } elseif ($title_icon !== '' && !filter_var($title_icon, FILTER_VALIDATE_URL) && str_starts_with($title_icon, '/') === false) {
            $error = 'Title Icon URL must be a valid URL or local path beginning with /.';
        }

        if ($error === '') {
            $sql = "SELECT id FROM site_articles
                    WHERE section_key = '" . $db->sql_escape($section_key) . "'
                    AND category = '" . $db->sql_escape($category) . "'
                    AND slug = '" . $db->sql_escape($slug) . "'
                    AND id <> " . (int) $id;

            $result = $db->sql_query($sql);
            $duplicate = $db->sql_fetchrow($result);
            $db->sql_freeresult($result);

            if ($duplicate) {
                $error = 'That slug is already used for this section/category.';
            }
        }

        if ($error === '') {
            $data = [
                'section_key' => $section_key,
                'category' => $category,
                'title' => $title,
                'slug' => $slug,
                'body' => $body,
                'image_url' => $image_url,
                'title_icon' => $title_icon,
                'sort_order' => $sort_order,
                'is_active' => $is_active ? 1 : 0,
            ];

            if ($id > 0) {
                $data['updated_by_user_id'] = (int) $user->data['user_id'];
                $data['updated_by_name'] = (string) $user->data['username'];
                $data['updated_at'] = date('Y-m-d H:i:s');

                $sql = 'UPDATE site_articles SET ' . $db->sql_build_array('UPDATE', $data) . '
                        WHERE id = ' . (int) $id;

                $db->sql_query($sql);
                admin_audit_write_local($section_key, 'articles', 'update', 'site_articles', (int) $id, $title, 'Section: ' . $section_key . ', Category: ' . $category);

                redirect('/admin/?page=articles&section=' . rawurlencode($section_key) . '&category=' . rawurlencode($category) . '&updated=1');
            } else {
                $data['created_by_user_id'] = (int) $user->data['user_id'];
                $data['created_by_name'] = (string) $user->data['username'];
                $data['created_at'] = date('Y-m-d H:i:s');

                $db->sql_query('INSERT INTO site_articles ' . $db->sql_build_array('INSERT', $data));
                $new_id = (int) $db->sql_nextid();

                admin_audit_write_local($section_key, 'articles', 'create', 'site_articles', $new_id, $title, 'Section: ' . $section_key . ', Category: ' . $category);

                redirect('/admin/?page=articles&section=' . rawurlencode($section_key) . '&category=' . rawurlencode($category) . '&added=1');
            }
        }

        $action = ($id > 0) ? 'edit' : 'add';
    }
}

if ($action === 'delete' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_articles_delete_' . $id)) {
        trigger_error('Invalid disable request.');
    }

    $sql = 'SELECT title FROM site_articles WHERE id = ' . (int) $id;
    $result = $db->sql_query($sql);
    $old = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = 'UPDATE site_articles SET ' . $db->sql_build_array('UPDATE', [
        'is_active' => 0,
        'updated_by_user_id' => (int) $user->data['user_id'],
        'updated_by_name' => (string) $user->data['username'],
        'updated_at' => date('Y-m-d H:i:s'),
    ]) . ' WHERE id = ' . (int) $id . "
    AND section_key = '" . $db->sql_escape($section_key) . "'";

    $db->sql_query($sql);
    admin_audit_write_local($section_key, 'articles', 'disable', 'site_articles', (int) $id, (string) ($old['title'] ?? ''), 'Category: ' . $category);

    admin_redirect_articles($section_key, $category);
}

if ($action === 'restore' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_articles_restore_' . $id)) {
        trigger_error('Invalid restore request.');
    }

    $sql = 'SELECT title FROM site_articles WHERE id = ' . (int) $id;
    $result = $db->sql_query($sql);
    $old = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = 'UPDATE site_articles SET ' . $db->sql_build_array('UPDATE', [
        'is_active' => 1,
        'updated_by_user_id' => (int) $user->data['user_id'],
        'updated_by_name' => (string) $user->data['username'],
        'updated_at' => date('Y-m-d H:i:s'),
    ]) . ' WHERE id = ' . (int) $id . "
    AND section_key = '" . $db->sql_escape($section_key) . "'";

    $db->sql_query($sql);
    admin_audit_write_local($section_key, 'articles', 'restore', 'site_articles', (int) $id, (string) ($old['title'] ?? ''), 'Category: ' . $category);

    admin_redirect_articles($section_key, $category);
}


if ($action === 'hard_delete' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_articles_hard_delete_' . $id)) {
        trigger_error('Invalid permanent delete request.');
    }

    $sql = 'SELECT id, section_key, category, title, is_active
            FROM site_articles
            WHERE id = ' . (int) $id;

    $result = $db->sql_query($sql);
    $old = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    if (!$old) {
        trigger_error('Article entry was not found.');
    }

    if ((int) ($old['is_active'] ?? 1) === 1) {
        trigger_error('Only inactive articles can be permanently deleted. Disable the article first.');
    }

    $delete_section_key = (string) ($old['section_key'] ?? $section_key);
    $delete_category = (string) ($old['category'] ?? $category);
    $delete_title = (string) ($old['title'] ?? '');

    $sql = 'DELETE FROM site_articles WHERE id = ' . (int) $id;
    $db->sql_query($sql);

    admin_audit_write_local(
        $delete_section_key,
        'articles',
        'hard_delete',
        'site_articles',
        (int) $id,
        $delete_title,
        'Section: ' . $delete_section_key . ', Category: ' . $delete_category
    );

    redirect('/admin/?page=articles&section=' . rawurlencode($delete_section_key) . '&category=' . rawurlencode($delete_category) . '&deleted=1');
}

if ($action === 'edit' && $id > 0 && !$request->is_set_post('save')) {
    $sql = 'SELECT * FROM site_articles
            WHERE id = ' . (int) $id . "
            AND section_key = '" . $db->sql_escape($section_key) . "'";

    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    if ($row) {
        $edit_entry = array_merge($edit_entry, $row);
        $category = (string) $edit_entry['category'];
    } else {
        $error = 'Article entry was not found.';
        $action = '';
    }
}

$show_form = ($action === 'add' || ($action === 'edit' && (int) $edit_entry['id'] > 0));

$pagination = admin_get_pagination(
    $request,
    $db,
    "SELECT COUNT(*) AS total FROM site_articles
     WHERE section_key = '" . $db->sql_escape($section_key) . "'
     AND category = '" . $db->sql_escape($category) . "'"
);

$sql = "SELECT * FROM site_articles
        WHERE section_key = '" . $db->sql_escape($section_key) . "'
        AND category = '" . $db->sql_escape($category) . "'
        ORDER BY
            is_active DESC,
            CASE WHEN category = 'news' THEN created_at END DESC,
            CASE WHEN category = 'news' THEN id END DESC,
            CASE WHEN category <> 'news' THEN sort_order END ASC,
            title ASC";

$result = $db->sql_query_limit($sql, $pagination['per_page'], $pagination['offset']);

$entries = [];

while ($row = $db->sql_fetchrow($result)) {
    $entries[] = $row;
}

$db->sql_freeresult($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Manage Articles</h1>
        <div class="text-secondary"><?= admin_h($section_name) ?> / <?= admin_h($category) ?></div>
    </div>

    <div class="d-flex gap-2">
        <?php if ($show_form): ?>
            <a class="btn btn-outline-secondary" href="/admin/?page=articles&amp;section=<?= admin_h($section_key) ?>&amp;category=<?= admin_h($category) ?>">Back to Articles</a>
        <?php else: ?>
            <a class="btn btn-primary" href="/admin/?page=articles&amp;section=<?= admin_h($section_key) ?>&amp;category=<?= admin_h($category) ?>&amp;action=add">Add Article</a>
        <?php endif; ?>

        <a class="btn btn-secondary" href="/admin/">Back to Dashboard</a>
    </div>
</div>

<?php if ($message !== ''): ?>
    <div class="alert alert-success"><?= admin_h($message) ?></div>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= admin_h($error) ?></div>
<?php endif; ?>

<?php if (!$show_form): ?>
    <div class="card mb-4">
        <div class="card-header">Article Category</div>
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <input type="hidden" name="page" value="articles">

                <div class="col-md-4">
                    <label class="form-label" for="section-select">Section</label>
                    <select class="form-select" id="section-select" name="section">
                        <?php foreach ($site_sections as $section_option_key => $section_option): ?>
                            <option value="<?= admin_h($section_option_key) ?>" <?= $section_key === $section_option_key ? 'selected' : '' ?>>
                                <?= admin_h($section_option['name'] ?? $section_option_key) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label" for="category-select">Category</label>
                    <select class="form-select" id="category-select" name="category">
                        <?php foreach ($default_categories as $cat_key => $cat_label): ?>
                            <option value="<?= admin_h($cat_key) ?>" <?= $category === $cat_key ? 'selected' : '' ?>>
                                <?= admin_h($cat_label) ?>
                            </option>
                        <?php endforeach; ?>

                        <?php if (!isset($default_categories[$category])): ?>
                            <option value="<?= admin_h($category) ?>" selected>
                                <?= admin_h(ucwords(str_replace('-', ' ', $category))) ?>
                            </option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-outline-light w-100" type="submit">View Articles</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if ($show_form): ?>
    <div class="card mb-4">
        <div class="card-header"><?= ((int) $edit_entry['id'] > 0) ? 'Edit Article: ' . admin_h($edit_entry['title']) : 'Add Article' ?></div>

        <div class="card-body">
            <form method="post" action="<?= admin_h(admin_articles_form_url($section_key, $category, (int) $edit_entry['id'] > 0 ? 'edit' : 'add', (int) $edit_entry['id'])) ?>">
                <?= build_hidden_fields(['id' => (int) $edit_entry['id']]) ?>
                <?= admin_form_token($form_key) ?>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="article_section">Section</label>
                        <select class="form-select" id="article_section" name="article_section">
                            <?php foreach ($site_sections as $section_option_key => $section_option): ?>
                                <option value="<?= admin_h($section_option_key) ?>" <?= (string) $edit_entry['section_key'] === (string) $section_option_key ? 'selected' : '' ?>>
                                    <?= admin_h($section_option['name'] ?? $section_option_key) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Move this article to another site section if it was added in the wrong place.</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="category">Category</label>
                        <select class="form-select" id="category" name="category">
                            <?php foreach ($default_categories as $cat_key => $cat_label): ?>
                                <option value="<?= admin_h($cat_key) ?>" <?= $edit_entry['category'] === $cat_key ? 'selected' : '' ?>>
                                    <?= admin_h($cat_label) ?>
                                </option>
                            <?php endforeach; ?>

                            <?php if (!isset($default_categories[(string) $edit_entry['category']])): ?>
                                <option value="<?= admin_h($edit_entry['category']) ?>" selected>
                                    <?= admin_h(admin_article_category_label((string) $edit_entry['category'])) ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="category_custom">Custom Category</label>
                        <input type="text" class="form-control" id="category_custom" name="category_custom" value="" maxlength="80" placeholder="optional, overrides category dropdown">
                        <div class="form-text">Example: events, patch-notes, raid-guides.</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= admin_h($edit_entry['title']) ?>" maxlength="255" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" class="form-control" id="slug" name="slug" value="<?= admin_h($edit_entry['slug']) ?>" maxlength="180" placeholder="auto-created from title if blank">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="image_url">Image URL / Path</label>
                    <input type="text" class="form-control" id="image_url" name="image_url" value="<?= admin_h($edit_entry['image_url']) ?>" maxlength="500">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="title_icon">Title Icon URL / Path</label>
                    <input type="text" class="form-control" id="title_icon" name="title_icon" value="<?= admin_h($edit_entry['title_icon'] ?? '') ?>" maxlength="500">
                    <div class="form-text">Small image displayed beside the article title.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="body">Body</label>
                    <textarea class="form-control" id="body" name="body" rows="14" required><?= admin_h($edit_entry['body']) ?></textarea>
                    <div class="form-text">HTML is allowed. Example: &lt;p&gt;Article text&lt;/p&gt;</div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="sort_order">Sort Order <small class="text-secondary">(auto if 0)</small></label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?= (int) $edit_entry['sort_order'] ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="is_active">Status</label>
                        <select class="form-select" id="is_active" name="is_active">
                            <option value="1" <?= ((int) $edit_entry['is_active'] === 1) ? 'selected' : '' ?>>Active</option>
                            <option value="0" <?= ((int) $edit_entry['is_active'] === 0) ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="save" value="1" class="btn btn-primary">Save Article</button>
                <a class="btn btn-outline-secondary" href="/admin/?page=articles&amp;section=<?= admin_h($section_key) ?>&amp;category=<?= admin_h($category) ?>">Cancel</a>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if (!$show_form): ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                Existing Articles
                <span class="text-secondary small"><?= number_format($pagination['total']) ?> total</span>
            </div>

            <div class="d-flex gap-2 align-items-center">
                <?php admin_per_page_selector('articles', $section_key, $pagination); ?>
                <a class="btn btn-sm btn-primary" href="/admin/?page=articles&amp;section=<?= admin_h($section_key) ?>&amp;category=<?= admin_h($category) ?>&amp;action=add">Add Article</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:70px;">Sort</th>
                        <th style="width:120px;">Images / Icons</th>
                        <th>Title</th>
                        <th>Slug</th>
                        <th style="width:140px;">Author</th>
                        <th style="width:160px;">Date</th>
                        <th style="width:100px;">Status</th>
                        <th style="width:180px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!$entries): ?>
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-4">No articles have been added yet.</td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($entries as $entry): ?>
                        <?php
                        $entry_id = (int) $entry['id'];
                        $delete_hash = generate_link_hash('admin_articles_delete_' . $entry_id);
                        $restore_hash = generate_link_hash('admin_articles_restore_' . $entry_id);
                        $hard_delete_hash = generate_link_hash('admin_articles_hard_delete_' . $entry_id);

                        $entry_author = 'The Regs';
                        if (!empty($entry['created_by_name'])) {
                            $entry_author = (string) $entry['created_by_name'];
                        } elseif (!empty($entry['updated_by_name'])) {
                            $entry_author = (string) $entry['updated_by_name'];
                        } elseif (!empty($entry['author_name'])) {
                            $entry_author = (string) $entry['author_name'];
                        } elseif (!empty($entry['author'])) {
                            $entry_author = (string) $entry['author'];
                        }

                        $entry_created = admin_article_date($entry['created_at'] ?? '');
                        $entry_updated = admin_article_date($entry['updated_at'] ?? '');
                        ?>
                        <tr class="<?= ((int) $entry['is_active'] === 0) ? 'table-secondary opacity-75' : '' ?>">
                            <td>
                                <?php if ((string) $entry['category'] === 'news'): ?>
                                    <span class="text-secondary">Date</span>
                                <?php else: ?>
                                    <?= (int) $entry['sort_order'] ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="d-flex gap-2 align-items-center">
                                    <?php if (!empty($entry['title_icon'])): ?>
                                        <img src="<?= admin_h($entry['title_icon']) ?>" alt="Icon" title="Title icon" style="max-width:36px; max-height:36px;" class="rounded border border-secondary">
                                    <?php endif; ?>

                                    <?php if (!empty($entry['image_url'])): ?>
                                        <img src="<?= admin_h($entry['image_url']) ?>" alt="Image" title="Article image" style="max-width:48px; max-height:36px;" class="rounded border border-secondary">
                                    <?php endif; ?>

                                    <?php if (empty($entry['title_icon']) && empty($entry['image_url'])): ?>
                                        <span class="text-secondary small">None</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td>
                                <strong><?= admin_h($entry['title']) ?></strong>
                                <div class="small text-secondary">
                                    <?= admin_h(admin_article_category_label((string) $entry['category'])) ?>
                                </div>
                            </td>

                            <td><code><?= admin_h($entry['slug']) ?></code></td>

                            <td><?= admin_h($entry_author) ?></td>

                            <td>
                                <?php if ($entry_created !== ''): ?>
                                    <div><?= admin_h($entry_created) ?></div>
                                <?php endif; ?>

                                <?php if ($entry_updated !== ''): ?>
                                    <div class="small text-secondary">Updated <?= admin_h($entry_updated) ?></div>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ((int) $entry['is_active'] === 1): ?>
                                    <span class="badge text-bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge text-bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="/admin/?page=articles&amp;section=<?= admin_h($section_key) ?>&amp;category=<?= admin_h($category) ?>&amp;action=edit&amp;id=<?= $entry_id ?>">Edit</a>

                                <?php if ((int) $entry['is_active'] === 1): ?>
                                    <a class="btn btn-sm btn-outline-danger" href="/admin/?page=articles&amp;section=<?= admin_h($section_key) ?>&amp;category=<?= admin_h($category) ?>&amp;action=delete&amp;id=<?= $entry_id ?>&amp;hash=<?= admin_h($delete_hash) ?>" onclick="return confirm('Disable this article?');">Disable</a>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-outline-success" href="/admin/?page=articles&amp;section=<?= admin_h($section_key) ?>&amp;category=<?= admin_h($category) ?>&amp;action=restore&amp;id=<?= $entry_id ?>&amp;hash=<?= admin_h($restore_hash) ?>">Restore</a>
                                    <a class="btn btn-sm btn-outline-danger" href="/admin/?page=articles&amp;section=<?= admin_h($section_key) ?>&amp;category=<?= admin_h($category) ?>&amp;action=hard_delete&amp;id=<?= $entry_id ?>&amp;hash=<?= admin_h($hard_delete_hash) ?>" onclick="return confirm('Permanently delete this article? This cannot be undone.');">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php admin_pagination_controls('articles', $section_key, $pagination); ?>
    </div>
<?php endif; ?>
