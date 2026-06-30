<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

global $db, $request, $user, $site_sections;

$section_key = $request->variable('section', 'ac');

if ($section_key !== 'ac') {
    trigger_error('Diplomacy is only available for Asheron\'s Call.');
}

if (!isset($site_sections[$section_key]) || !in_array('diplomacy', $site_sections[$section_key]['modules'], true)) {
    trigger_error('Diplomacy is not enabled for this section.');
}

$section_name = $site_sections[$section_key]['name'];

$form_key = 'admin_diplomacy';
add_form_key($form_key);

$message = '';
$error = '';

$action = $request->variable('action', '');
$id = $request->variable('id', 0);

function admin_redirect_diplomacy(): void
{
    redirect('/admin/?page=diplomacy&section=ac');
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
        $tag = trim($request->variable('tag', '', true));
        $monarch = trim($request->variable('monarch', '', true));
        $status = trim($request->variable('status', '', true));
        $url = trim($request->variable('url', '', true));
        $type = trim($request->variable('type', '', true));
        $sort_order = $request->variable('sort_order', 0);
        $is_active = $request->variable('is_active', 1);

        if ($name === '') {
            $error = 'Name is required.';
        } elseif ($status === '') {
            $error = 'Status is required.';
        } elseif ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
            $error = 'Please enter a valid URL including http:// or https://.';
        } else {
            if ($id > 0) {
                $sql = 'UPDATE ac_diplomacy SET ' . $db->sql_build_array('UPDATE', [
                    'name' => $name,
                    'tag' => $tag,
                    'monarch' => $monarch,
                    'status' => $status,
                    'url' => $url,
                    'type' => $type,
                    'sort_order' => $sort_order,
                    'is_active' => $is_active ? 1 : 0,
                    'updated_by_user_id' => (int) $user->data['user_id'],
                    'updated_by_name' => (string) $user->data['username'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]) . '
                WHERE id = ' . (int) $id;

                $db->sql_query($sql);
                $message = 'Diplomacy entry updated.';
            } else {
                $sql = 'INSERT INTO ac_diplomacy ' . $db->sql_build_array('INSERT', [
                    'name' => $name,
                    'tag' => $tag,
                    'monarch' => $monarch,
                    'status' => $status,
                    'url' => $url,
                    'type' => $type,
                    'sort_order' => $sort_order,
                    'is_active' => $is_active ? 1 : 0,
                    'created_by_user_id' => (int) $user->data['user_id'],
                    'created_by_name' => (string) $user->data['username'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $db->sql_query($sql);
                $message = 'Diplomacy entry added.';
            }
        }
    }
}

/*
 * Disable / Restore
 */
if ($action === 'delete' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_diplomacy_delete_' . $id)) {
        trigger_error('Invalid disable request.');
    }

    $sql = 'UPDATE ac_diplomacy SET ' . $db->sql_build_array('UPDATE', [
        'is_active' => 0,
        'updated_by_user_id' => (int) $user->data['user_id'],
        'updated_by_name' => (string) $user->data['username'],
        'updated_at' => date('Y-m-d H:i:s'),
    ]) . '
    WHERE id = ' . (int) $id;

    $db->sql_query($sql);
    admin_redirect_diplomacy();
}

if ($action === 'restore' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_diplomacy_restore_' . $id)) {
        trigger_error('Invalid restore request.');
    }

    $sql = 'UPDATE ac_diplomacy SET ' . $db->sql_build_array('UPDATE', [
        'is_active' => 1,
        'updated_by_user_id' => (int) $user->data['user_id'],
        'updated_by_name' => (string) $user->data['username'],
        'updated_at' => date('Y-m-d H:i:s'),
    ]) . '
    WHERE id = ' . (int) $id;

    $db->sql_query($sql);
    admin_redirect_diplomacy();
}

/*
 * Load edit row
 */
$edit_entry = [
    'id' => 0,
    'name' => '',
    'tag' => '',
    'monarch' => '',
    'status' => '',
    'url' => '',
    'type' => '',
    'sort_order' => 0,
    'is_active' => 1,
];

if ($action === 'edit' && $id > 0) {
    $sql = 'SELECT *
            FROM ac_diplomacy
            WHERE id = ' . (int) $id;

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
     FROM ac_diplomacy"
);

$sql = 'SELECT *
        FROM ac_diplomacy
        ORDER BY is_active DESC, sort_order ASC, status ASC, name ASC';

$result = $db->sql_query_limit($sql, $pagination['per_page'], $pagination['offset']);

$entries = [];

while ($row = $db->sql_fetchrow($result)) {
    $entries[] = $row;
}

$db->sql_freeresult($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Manage Diplomacy</h1>
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
        <?= ((int) $edit_entry['id'] > 0) ? 'Edit Diplomacy Entry' : 'Add Diplomacy Entry' ?>
    </div>

    <div class="card-body">
        <form method="post" action="/admin/?page=diplomacy&amp;section=ac">
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
                        maxlength="100"
                        required
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label" for="tag">Tag</label>
                    <input
                        type="text"
                        class="form-control"
                        id="tag"
                        name="tag"
                        value="<?= admin_h($edit_entry['tag']) ?>"
                        maxlength="100"
                    >
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label" for="monarch">Monarch</label>
                    <input
                        type="text"
                        class="form-control"
                        id="monarch"
                        name="monarch"
                        value="<?= admin_h($edit_entry['monarch']) ?>"
                        maxlength="100"
                    >
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label" for="status">Status</label>
                    <input
                        type="text"
                        class="form-control"
                        id="status"
                        name="status"
                        value="<?= admin_h($edit_entry['status']) ?>"
                        maxlength="100"
                        placeholder="Ally, Neutral, Enemy"
                        required
                    >
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="url">URL</label>
                <input
                    type="url"
                    class="form-control"
                    id="url"
                    name="url"
                    value="<?= admin_h($edit_entry['url']) ?>"
                    maxlength="500"
                >
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label" for="type">Type</label>
                    <input
                        type="text"
                        class="form-control"
                        id="type"
                        name="type"
                        value="<?= admin_h($edit_entry['type']) ?>"
                        maxlength="50"
                        placeholder="Guild, Allegiance, Clan"
                    >
                </div>

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
                <a class="btn btn-outline-secondary" href="/admin/?page=diplomacy&amp;section=ac">
                    Cancel Edit
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">

        <div>
            Existing Diplomacy Entries

            <span class="text-secondary small">
                <?= number_format($pagination['total']) ?> total
            </span>
        </div>

        <?php
        admin_per_page_selector(
            'diplomacy',
            'ac',
            $pagination
        );
        ?>

    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 70px;">Sort</th>
                    <th>Name</th>
                    <th>Tag</th>
                    <th>Monarch</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>URL</th>
                    <th style="width: 90px;">Active</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!$entries): ?>
                    <tr>
                        <td colspan="9" class="text-center text-secondary py-4">
                            No diplomacy entries have been added yet.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($entries as $entry): ?>
                    <?php
                    $entry_id = (int) $entry['id'];
                    $delete_hash = generate_link_hash('admin_diplomacy_delete_' . $entry_id);
                    $restore_hash = generate_link_hash('admin_diplomacy_restore_' . $entry_id);
                    ?>
                    <tr class="<?= ((int) $entry['is_active'] === 0) ? 'table-secondary opacity-75' : '' ?>">
                        <td><?= (int) $entry['sort_order'] ?></td>

                        <td><strong><?= admin_h($entry['name']) ?></strong></td>
                        <td><?= admin_h($entry['tag']) ?></td>
                        <td><?= admin_h($entry['monarch']) ?></td>
                        <td><?= admin_h($entry['status']) ?></td>
                        <td><?= admin_h($entry['type']) ?></td>

                        <td>
                            <?php if (!empty($entry['url'])): ?>
                                <a href="<?= admin_h($entry['url']) ?>" target="_blank" rel="noopener">
                                    Visit
                                </a>
                            <?php else: ?>
                                <span class="text-secondary">None</span>
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
                            <a
                                class="btn btn-sm btn-outline-primary"
                                href="/admin/?page=diplomacy&amp;section=ac&amp;action=edit&amp;id=<?= $entry_id ?>"
                            >
                                Edit
                            </a>

                            <?php if ((int) $entry['is_active'] === 1): ?>
                                <a
                                    class="btn btn-sm btn-outline-danger"
                                    href="/admin/?page=diplomacy&amp;section=ac&amp;action=delete&amp;id=<?= $entry_id ?>&amp;hash=<?= admin_h($delete_hash) ?>"
                                    onclick="return confirm('Disable this diplomacy entry?');"
                                >
                                    Disable
                                </a>
                            <?php else: ?>
                                <a
                                    class="btn btn-sm btn-outline-success"
                                    href="/admin/?page=diplomacy&amp;section=ac&amp;action=restore&amp;id=<?= $entry_id ?>&amp;hash=<?= admin_h($restore_hash) ?>"
                                >
                                    Restore
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
<?php admin_pagination_controls('diplomacy', 'ac', $pagination); ?>
    </div>
</div>