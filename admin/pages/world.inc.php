<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

global $db, $request, $user, $site_sections;

$section_key = $request->variable('section', 'wow');

if (!isset($site_sections[$section_key])) {
    $section_key = 'wow';
}

if (!in_array('world', $site_sections[$section_key]['modules'], true)) {
    trigger_error('World management is not enabled for this section.');
}

$section_name = $site_sections[$section_key]['name'];

$form_key = 'admin_world';
add_form_key($form_key);

$message = '';
$error = '';

$action = $request->variable('action', '');
$id = $request->variable('id', 0);

function admin_world_slug(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
    $value = trim((string) $value, '-');

    return $value !== '' ? $value : 'world-entry';
}

function admin_redirect_world(string $section_key): void
{
    redirect('/admin/?page=world&section=' . rawurlencode($section_key));
}

/*
 * Save
 */
if ($request->is_set_post('save')) {
    if (!check_form_key($form_key)) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $id = $request->variable('id', 0);

        $name = trim($request->variable('name', '', true));
        $slug = trim($request->variable('slug', '', true));
        $image_url = trim($request->variable('image_url', '', true));
        $background_url = trim($request->variable('background_url', '', true));
        $description = trim($request->variable('description', '', true));
        $history = trim($request->variable('history', '', true));
        $sort_order = $request->variable('sort_order', 0);
        $is_active = $request->variable('is_active', 1);

        if ($name === '') {
            $error = 'Name is required.';
        }

        if ($slug === '') {
            $slug = admin_world_slug($name);
        } else {
            $slug = admin_world_slug($slug);
        }

        if ($image_url !== '' && !filter_var($image_url, FILTER_VALIDATE_URL) && str_starts_with($image_url, '/') === false) {
            $error = 'Zone image must be a valid URL or local path beginning with /.';
        }

        if ($background_url !== '' && !filter_var($background_url, FILTER_VALIDATE_URL) && str_starts_with($background_url, '/') === false) {
            $error = 'Background image must be a valid URL or local path beginning with /.';
        }

        if ($error === '') {
            $sql = "SELECT id
                    FROM site_world
                    WHERE section_key = '" . $db->sql_escape($section_key) . "'
                    AND slug = '" . $db->sql_escape($slug) . "'
                    AND id <> " . (int) $id;

            $result = $db->sql_query($sql);
            $duplicate = $db->sql_fetchrow($result);
            $db->sql_freeresult($result);

            if ($duplicate) {
                $error = 'That slug is already used in this section.';
            }
        }

        if ($error === '') {
            if ($id > 0) {
                $sql = 'UPDATE site_world SET ' . $db->sql_build_array('UPDATE', [
                    'name' => $name,
                    'slug' => $slug,
                    'image_url' => $image_url,
                    'background_url' => $background_url,
                    'description' => $description,
                    'history' => $history,
                    'sort_order' => $sort_order,
                    'is_active' => $is_active ? 1 : 0,
                    'updated_by_user_id' => (int) $user->data['user_id'],
                    'updated_by_name' => (string) $user->data['username'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]) . '
                WHERE id = ' . (int) $id . "
                AND section_key = '" . $db->sql_escape($section_key) . "'";

                $db->sql_query($sql);
                $message = 'World entry updated.';
            } else {
                $sql = 'INSERT INTO site_world ' . $db->sql_build_array('INSERT', [
                    'section_key' => $section_key,
                    'name' => $name,
                    'slug' => $slug,
                    'image_url' => $image_url,
                    'background_url' => $background_url,
                    'description' => $description,
                    'history' => $history,
                    'sort_order' => $sort_order,
                    'is_active' => $is_active ? 1 : 0,
                    'created_by_user_id' => (int) $user->data['user_id'],
                    'created_by_name' => (string) $user->data['username'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $db->sql_query($sql);
                $message = 'World entry added.';
            }
        }
    }
}

/*
 * Disable / Restore
 */
if ($action === 'delete' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_world_delete_' . $id)) {
        trigger_error('Invalid disable request.');
    }

    $sql = 'UPDATE site_world SET ' . $db->sql_build_array('UPDATE', [
        'is_active' => 0,
        'updated_by_user_id' => (int) $user->data['user_id'],
        'updated_by_name' => (string) $user->data['username'],
        'updated_at' => date('Y-m-d H:i:s'),
    ]) . '
    WHERE id = ' . (int) $id . "
    AND section_key = '" . $db->sql_escape($section_key) . "'";

    $db->sql_query($sql);
    admin_redirect_world($section_key);
}

if ($action === 'restore' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_world_restore_' . $id)) {
        trigger_error('Invalid restore request.');
    }

    $sql = 'UPDATE site_world SET ' . $db->sql_build_array('UPDATE', [
        'is_active' => 1,
        'updated_by_user_id' => (int) $user->data['user_id'],
        'updated_by_name' => (string) $user->data['username'],
        'updated_at' => date('Y-m-d H:i:s'),
    ]) . '
    WHERE id = ' . (int) $id . "
    AND section_key = '" . $db->sql_escape($section_key) . "'";

    $db->sql_query($sql);
    admin_redirect_world($section_key);
}

/*
 * Load edit row
 */
$edit_entry = [
    'id' => 0,
    'name' => '',
    'slug' => '',
    'image_url' => '',
    'background_url' => '',
    'description' => '',
    'history' => '',
    'sort_order' => 0,
    'is_active' => 1,
];

if ($action === 'edit' && $id > 0) {
    $sql = 'SELECT *
            FROM site_world
            WHERE id = ' . (int) $id . "
            AND section_key = '" . $db->sql_escape($section_key) . "'";

    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    if ($row) {
        $edit_entry = $row;
    }
}

/*
 * Load entries
 */
$pagination = admin_get_pagination(
    $request,
    $db,
    "SELECT COUNT(*) AS total
     FROM site_world
     WHERE section_key = '" . $db->sql_escape($section_key) . "'"
);

$sql = "SELECT *
        FROM site_world
        WHERE section_key = '" . $db->sql_escape($section_key) . "'
        ORDER BY is_active DESC, sort_order ASC, name ASC";

$result = $db->sql_query_limit($sql, $pagination['per_page'], $pagination['offset']);

$entries = [];

while ($row = $db->sql_fetchrow($result)) {
    $entries[] = $row;
}

$db->sql_freeresult($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Manage World</h1>
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
        <?= ((int) $edit_entry['id'] > 0) ? 'Edit World Entry' : 'Add World Entry' ?>
    </div>

    <div class="card-body">
        <form method="post" action="/admin/?page=world&amp;section=<?= admin_h($section_key) ?>">
<?= build_hidden_fields([
    'id' => (int) $edit_entry['id'],
]) ?>

<?= admin_form_token($form_key) ?>


            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="name">Name</label>
                    <input
                        type="text"
                        class="form-control"
                        id="name"
                        name="name"
                        value="<?= admin_h($edit_entry['name']) ?>"
                        maxlength="150"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label" for="slug">Slug</label>
                    <input
                        type="text"
                        class="form-control"
                        id="slug"
                        name="slug"
                        value="<?= admin_h($edit_entry['slug']) ?>"
                        maxlength="180"
                        placeholder="auto-created from name if blank"
                    >
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="image_url">Zone Image URL / Path</label>
                <input
                    type="text"
                    class="form-control"
                    id="image_url"
                    name="image_url"
                    value="<?= admin_h($edit_entry['image_url']) ?>"
                    maxlength="500"
                    placeholder="/images/wow/zones/example.jpg or https://cdn.example.com/example.jpg"
                >
            </div>

            <?php if (!empty($edit_entry['image_url'])): ?>
                <div class="mb-3">
                    <img src="<?= admin_h($edit_entry['image_url']) ?>" alt="" class="img-thumbnail" style="max-width: 240px; height: auto;">
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label" for="background_url">Background Image URL / Path</label>
                <input
                    type="text"
                    class="form-control"
                    id="background_url"
                    name="background_url"
                    value="<?= admin_h($edit_entry['background_url']) ?>"
                    maxlength="500"
                >
            </div>

            <?php if (!empty($edit_entry['background_url'])): ?>
                <div class="mb-3">
                    <img src="<?= admin_h($edit_entry['background_url']) ?>" alt="" class="img-thumbnail" style="max-width: 240px; height: auto;">
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label" for="description">Description / Zone Text</label>
                <textarea
                    class="form-control"
                    id="description"
                    name="description"
                    rows="10"
                ><?= admin_h($edit_entry['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label" for="history">History</label>
                <textarea
                    class="form-control"
                    id="history"
                    name="history"
                    rows="10"
                ><?= admin_h($edit_entry['history']) ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="sort_order">Sort Order</label>
                    <input
                        type="number"
                        class="form-control"
                        id="sort_order"
                        name="sort_order"
                        value="<?= (int) $edit_entry['sort_order'] ?>"
                    >
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label" for="is_active">Status</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="1" <?= ((int) $edit_entry['is_active'] === 1) ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ((int) $edit_entry['is_active'] === 0) ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="save" value="1" class="btn btn-primary">
                Save Entry
            </button>

            <?php if ((int) $edit_entry['id'] > 0): ?>
                <a class="btn btn-outline-secondary" href="/admin/?page=world&amp;section=<?= admin_h($section_key) ?>">
                    Cancel Edit
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">

        <div>
            Existing World Entries

            <span class="text-secondary small">
                <?= number_format($pagination['total']) ?> total
            </span>
        </div>

        <?php
        admin_per_page_selector(
            'world',
            $section_key,
            $pagination
        );
        ?>

    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 70px;">Sort</th>
                    <th style="width: 110px;">Image</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Status</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!$entries): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">
                            No world entries have been added yet.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($entries as $entry): ?>
                    <?php
                    $entry_id = (int) $entry['id'];
                    $delete_hash = generate_link_hash('admin_world_delete_' . $entry_id);
                    $restore_hash = generate_link_hash('admin_world_restore_' . $entry_id);
                    ?>
                    <tr class="<?= ((int) $entry['is_active'] === 0) ? 'table-secondary opacity-75' : '' ?>">
                        <td><?= (int) $entry['sort_order'] ?></td>

                        <td>
                            <?php if (!empty($entry['image_url'])): ?>
                                <a href="<?= admin_h($entry['image_url']) ?>" target="_blank" rel="noopener">
                                    <img
                                        src="<?= admin_h($entry['image_url']) ?>"
                                        alt=""
                                        class="img-thumbnail"
                                        style="max-width: 90px; height: auto;"
                                        loading="lazy"
                                    >
                                </a>
                            <?php else: ?>
                                <span class="text-secondary small">No image</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <strong><?= admin_h($entry['name']) ?></strong>

                            <?php if (!empty($entry['legacy_table']) && !empty($entry['legacy_id'])): ?>
                                <div class="small text-secondary">
                                    Imported from <?= admin_h($entry['legacy_table']) ?> #<?= (int) $entry['legacy_id'] ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <td>
                            <code><?= admin_h($entry['slug']) ?></code>
                        </td>

                        <td>
                            <?php if ((int) $entry['is_active'] === 1): ?>
                                <span class="badge text-bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a
                                class="btn btn-sm btn-outline-primary"
                                href="/admin/?page=world&amp;section=<?= admin_h($section_key) ?>&amp;action=edit&amp;id=<?= $entry_id ?>"
                            >
                                Edit
                            </a>

                            <?php if ((int) $entry['is_active'] === 1): ?>
                                <a
                                    class="btn btn-sm btn-outline-danger"
                                    href="/admin/?page=world&amp;section=<?= admin_h($section_key) ?>&amp;action=delete&amp;id=<?= $entry_id ?>&amp;hash=<?= admin_h($delete_hash) ?>"
                                    onclick="return confirm('Disable this world entry?');"
                                >
                                    Disable
                                </a>
                            <?php else: ?>
                                <a
                                    class="btn btn-sm btn-outline-success"
                                    href="/admin/?page=world&amp;section=<?= admin_h($section_key) ?>&amp;action=restore&amp;id=<?= $entry_id ?>&amp;hash=<?= admin_h($restore_hash) ?>"
                                >
                                    Restore
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
<?php admin_pagination_controls('world', $section_key, $pagination); ?>
    </div>
</div>