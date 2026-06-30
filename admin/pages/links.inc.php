<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

global $db, $request, $user, $site_sections;

$section_key = $request->variable('section', 'site');

if (!isset($site_sections[$section_key])) {
    $section_key = 'site';
}

if (!in_array('links', $site_sections[$section_key]['modules'], true)) {
    trigger_error('Links are not enabled for this section.');
}

$section_name = $site_sections[$section_key]['name'];
$page_title = 'Manage Links - ' . $section_name;

$form_key = 'admin_links';
add_form_key($form_key);

$message = '';
$error = '';

$action = $request->variable('action', '');
$id = $request->variable('id', 0);

function admin_redirect_links(string $section_key): void
{
    redirect('/admin/?page=links&section=' . rawurlencode($section_key));
}

/* Save link */
if ($request->is_set_post('save')) {
    if (!check_form_key($form_key)) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $id = $request->variable('id', 0);
        $title = trim($request->variable('title', '', true));
        $url = trim($request->variable('url', '', true));
        $description = trim($request->variable('description', '', true));
        $sort_order = $request->variable('sort_order', 0);
        $is_active = $request->variable('is_active', 1);

        if ($title === '') {
            $error = 'Link name is required.';
        } elseif ($url === '') {
            $error = 'URL is required.';
        } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
            $error = 'Please enter a valid URL including http:// or https://.';
        } else {
            if ($id > 0) {
                $sql = 'UPDATE site_links_new SET ' . $db->sql_build_array('UPDATE', [
                    'title' => $title,
                    'url' => $url,
                    'description' => $description,
                    'sort_order' => $sort_order,
                    'is_active' => $is_active ? 1 : 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]) . '
                WHERE id = ' . (int) $id . "
                AND section_key = '" . $db->sql_escape($section_key) . "'";

                $db->sql_query($sql);
                $message = 'Link updated.';
            } else {
                $sql = 'INSERT INTO site_links_new ' . $db->sql_build_array('INSERT', [
                    'section_key' => $section_key,
                    'title' => $title,
                    'url' => $url,
                    'description' => $description,
                    'author_user_id' => (int) $user->data['user_id'],
                    'author_name' => (string) $user->data['username'],
                    'sort_order' => $sort_order,
                    'is_active' => $is_active ? 1 : 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $db->sql_query($sql);
                $message = 'Link added.';
            }
        }
    }
}

/* Soft delete / restore */
if ($action === 'delete' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_links_delete_' . $id)) {
        trigger_error('Invalid delete request.');
    }

    $sql = 'UPDATE site_links_new
            SET is_active = 0,
                updated_at = "' . $db->sql_escape(date('Y-m-d H:i:s')) . '"
            WHERE id = ' . (int) $id . "
            AND section_key = '" . $db->sql_escape($section_key) . "'";

    $db->sql_query($sql);
    admin_redirect_links($section_key);
}

if ($action === 'restore' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_links_restore_' . $id)) {
        trigger_error('Invalid restore request.');
    }

    $sql = 'UPDATE site_links_new
            SET is_active = 1,
                updated_at = "' . $db->sql_escape(date('Y-m-d H:i:s')) . '"
            WHERE id = ' . (int) $id . "
            AND section_key = '" . $db->sql_escape($section_key) . "'";

    $db->sql_query($sql);
    admin_redirect_links($section_key);
}

/* Load edit record */
$edit_link = [
    'id' => 0,
    'title' => '',
    'url' => '',
    'description' => '',
    'sort_order' => 0,
    'is_active' => 1,
];

if ($action === 'edit' && $id > 0) {
    $sql = 'SELECT *
            FROM site_links_new
            WHERE id = ' . (int) $id . "
            AND section_key = '" . $db->sql_escape($section_key) . "'";

    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    if ($row) {
        $edit_link = $row;
    }
}

/* Load section links */
$pagination = admin_get_pagination(
    $request,
    $db,
    "SELECT COUNT(*) AS total
     FROM site_links_new
     WHERE section_key = '" . $db->sql_escape($section_key) . "'"
);


$sql = "SELECT *
        FROM site_links_new
        WHERE section_key = '" . $db->sql_escape($section_key) . "'
        ORDER BY is_active DESC,
                 sort_order ASC,
                 title ASC";

$result = $db->sql_query_limit(
    $sql,
    $pagination['per_page'],
    $pagination['offset']
);


$links = [];


while ($row = $db->sql_fetchrow($result)) {

    $links[] = $row;

}


$db->sql_freeresult($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Manage Links</h1>
        <div class="text-secondary"><?= admin_h($section_name) ?></div>
    </div>

    <a class="btn btn-secondary" href="/admin/">Back to Dashboard</a>
</div>

<?php if ($message !== ''): ?>
    <div class="alert alert-success"><?= admin_h($message) ?></div>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= admin_h($error) ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <?= ((int) $edit_link['id'] > 0) ? 'Edit Link' : 'Add Link' ?>
    </div>

    <div class="card-body">
        <form method="post" action="/admin/?page=links&amp;section=<?= admin_h($section_key) ?>">
<?= build_hidden_fields([
    'id' => (int) $edit_link['id'],
]) ?>

<?= admin_form_token($form_key) ?>

            <div class="mb-3">
                <label class="form-label" for="title">Link Name</label>
                <input
                    type="text"
                    class="form-control"
                    id="title"
                    name="title"
                    value="<?= admin_h($edit_link['title']) ?>"
                    maxlength="200"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label" for="url">URL</label>
                <input
                    type="url"
                    class="form-control"
                    id="url"
                    name="url"
                    value="<?= admin_h($edit_link['url']) ?>"
                    maxlength="500"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label" for="description">Description</label>
                <textarea
                    class="form-control"
                    id="description"
                    name="description"
                    rows="3"
                ><?= admin_h($edit_link['description']) ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="sort_order">Sort Order</label>
                    <input
                        type="number"
                        class="form-control"
                        id="sort_order"
                        name="sort_order"
                        value="<?= (int) $edit_link['sort_order'] ?>"
                    >
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label" for="is_active">Status</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="1" <?= ((int) $edit_link['is_active'] === 1) ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ((int) $edit_link['is_active'] === 0) ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="save" value="1" class="btn btn-primary">
                Save Link
            </button>

            <?php if ((int) $edit_link['id'] > 0): ?>
                <a class="btn btn-outline-secondary" href="/admin/?page=links&amp;section=<?= admin_h($section_key) ?>">
                    Cancel Edit
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
<div class="card-header d-flex justify-content-between align-items-center">

    <div>
        Existing Links

        <span class="text-secondary small">
            <?= number_format($pagination['total']) ?> total
        </span>
    </div>

</div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 70px;">Sort</th>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!$links): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">
                            No links have been added for this section yet.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($links as $link): ?>
                    <?php
                    $link_id = (int) $link['id'];
                    $delete_hash = generate_link_hash('admin_links_delete_' . $link_id);
                    $restore_hash = generate_link_hash('admin_links_restore_' . $link_id);
                    ?>
                    <tr class="<?= ((int) $link['is_active'] === 0) ? 'table-secondary opacity-75' : '' ?>">
                        <td><?= (int) $link['sort_order'] ?></td>

                        <td>
                            <strong><?= admin_h($link['title']) ?></strong>

                            <?php if (!empty($link['description'])): ?>
                                <div class="small text-secondary">
                                    <?= admin_h($link['description']) ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a href="<?= admin_h($link['url']) ?>" target="_blank" rel="noopener">
                                <?= admin_h($link['url']) ?>
                            </a>
                        </td>

                        <td><?= admin_h($link['author_name'] ?? '') ?></td>

                        <td>
                            <?php if ((int) $link['is_active'] === 1): ?>
                                <span class="badge text-bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a
                                class="btn btn-sm btn-outline-primary"
                                href="/admin/?page=links&amp;section=<?= admin_h($section_key) ?>&amp;action=edit&amp;id=<?= $link_id ?>"
                            >
                                Edit
                            </a>

                            <?php if ((int) $link['is_active'] === 1): ?>
                                <a
                                    class="btn btn-sm btn-outline-danger"
                                    href="/admin/?page=links&amp;section=<?= admin_h($section_key) ?>&amp;action=delete&amp;id=<?= $link_id ?>&amp;hash=<?= admin_h($delete_hash) ?>"
                                    onclick="return confirm('Disable this link?');"
                                >
                                    Disable
                                </a>
                            <?php else: ?>
                                <a
                                    class="btn btn-sm btn-outline-success"
                                    href="/admin/?page=links&amp;section=<?= admin_h($section_key) ?>&amp;action=restore&amp;id=<?= $link_id ?>&amp;hash=<?= admin_h($restore_hash) ?>"
                                >
                                    Restore
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php
    admin_pagination_controls(
        'links',
        $section_key,
        $pagination
    );
    ?>

</div>