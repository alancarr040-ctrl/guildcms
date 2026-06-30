<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

global $db, $request, $user, $site_sections;

$section_key = $request->variable('section', 'ac');

if ($section_key !== 'ac') {
    trigger_error('KOS is only available for Asheron\'s Call.');
}

if (!isset($site_sections[$section_key]) || !in_array('kos', $site_sections[$section_key]['modules'], true)) {
    trigger_error('KOS is not enabled for this section.');
}

$section_name = $site_sections[$section_key]['name'];

$form_key = 'admin_kos';
add_form_key($form_key);

$message = '';
$error = '';

$action = $request->variable('action', '');
$id = $request->variable('id', 0);

function admin_redirect_kos(): void
{
    redirect('/admin/?page=kos&section=ac');
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
        $comment = trim($request->variable('comment', '', true));
        $is_active = $request->variable('is_active', 1);

        if ($name === '') {
            $error = 'Name is required.';
        } elseif ($comment === '') {
            $error = 'Comment is required.';
        } else {
            if ($id > 0) {
                $sql = 'UPDATE ac_kos SET ' . $db->sql_build_array('UPDATE', [
                    'name' => $name,
                    'comment' => $comment,
                    'is_active' => $is_active ? 1 : 0,
                    'updated_by_user_id' => (int) $user->data['user_id'],
                    'updated_by_name' => (string) $user->data['username'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]) . '
                WHERE id = ' . (int) $id;

                $db->sql_query($sql);
                $message = 'KOS entry updated.';
            } else {
                $sql = 'INSERT INTO ac_kos ' . $db->sql_build_array('INSERT', [
                    'name' => $name,
                    'comment' => $comment,
                    'submitter_user_id' => (int) $user->data['user_id'],
                    'submitter_name' => (string) $user->data['username'],
                    'is_active' => $is_active ? 1 : 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $db->sql_query($sql);
                $message = 'KOS entry added.';
            }
        }
    }
}

/*
 * Disable / Restore
 */
if ($action === 'delete' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_kos_delete_' . $id)) {
        trigger_error('Invalid disable request.');
    }

    $sql = 'UPDATE ac_kos SET ' . $db->sql_build_array('UPDATE', [
        'is_active' => 0,
        'updated_by_user_id' => (int) $user->data['user_id'],
        'updated_by_name' => (string) $user->data['username'],
        'updated_at' => date('Y-m-d H:i:s'),
    ]) . '
    WHERE id = ' . (int) $id;

    $db->sql_query($sql);
    admin_redirect_kos();
}

if ($action === 'restore' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_kos_restore_' . $id)) {
        trigger_error('Invalid restore request.');
    }

    $sql = 'UPDATE ac_kos SET ' . $db->sql_build_array('UPDATE', [
        'is_active' => 1,
        'updated_by_user_id' => (int) $user->data['user_id'],
        'updated_by_name' => (string) $user->data['username'],
        'updated_at' => date('Y-m-d H:i:s'),
    ]) . '
    WHERE id = ' . (int) $id;

    $db->sql_query($sql);
    admin_redirect_kos();
}

/*
 * Load edit row
 */
$edit_entry = [
    'id' => 0,
    'name' => '',
    'comment' => '',
    'is_active' => 1,
];

if ($action === 'edit' && $id > 0) {
    $sql = 'SELECT *
            FROM ac_kos
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
     FROM ac_kos"
);

$sql = 'SELECT *
        FROM ac_kos
        ORDER BY is_active DESC, created_at DESC, name ASC';

$result = $db->sql_query_limit($sql, $pagination['per_page'], $pagination['offset']);

$entries = [];

while ($row = $db->sql_fetchrow($result)) {
    $entries[] = $row;
}

$db->sql_freeresult($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Manage KOS</h1>
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
        <?= ((int) $edit_entry['id'] > 0) ? 'Edit KOS Entry' : 'Add KOS Entry' ?>
    </div>

    <div class="card-body">
        <form method="post" action="/admin/?page=kos&amp;section=ac">
<?= build_hidden_fields([
    'id' => (int) $edit_entry['id'],
]) ?>
<?= admin_form_token($form_key) ?>

            <div class="mb-3">
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

            <div class="mb-3">
                <label class="form-label" for="comment">Comment</label>
                <textarea
                    class="form-control"
                    id="comment"
                    name="comment"
                    rows="5"
                    required
                ><?= admin_h($edit_entry['comment']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label" for="is_active">Status</label>
                <select class="form-select" id="is_active" name="is_active">
                    <option value="1" <?= ((int) $edit_entry['is_active'] === 1) ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= ((int) $edit_entry['is_active'] === 0) ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <button type="submit" name="save" value="1" class="btn btn-primary">
                Save Entry
            </button>

            <?php if ((int) $edit_entry['id'] > 0): ?>
                <a class="btn btn-outline-secondary" href="/admin/?page=kos&amp;section=ac">
                    Cancel Edit
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">

        <div>
        Existing KOS Entries
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
                    <th>Name</th>
                    <th>Comment</th>
                    <th>Submitter</th>
                    <th>Created</th>
                    <th>Status</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!$entries): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">
                            No KOS entries have been added yet.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($entries as $entry): ?>
                    <?php
                    $entry_id = (int) $entry['id'];
                    $delete_hash = generate_link_hash('admin_kos_delete_' . $entry_id);
                    $restore_hash = generate_link_hash('admin_kos_restore_' . $entry_id);
                    ?>
                    <tr class="<?= ((int) $entry['is_active'] === 0) ? 'table-secondary opacity-75' : '' ?>">
                        <td>
                            <strong><?= admin_h($entry['name']) ?></strong>
                        </td>

                        <td>
                            <?= nl2br(admin_h($entry['comment'])) ?>
                        </td>

                        <td>
                            <?= admin_h($entry['submitter_name'] ?? '') ?>
                        </td>

                        <td>
                            <?= admin_h($entry['created_at'] ?? '') ?>
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
                                href="/admin/?page=kos&amp;section=ac&amp;action=edit&amp;id=<?= $entry_id ?>"
                            >
                                Edit
                            </a>

                            <?php if ((int) $entry['is_active'] === 1): ?>
                                <a
                                    class="btn btn-sm btn-outline-danger"
                                    href="/admin/?page=kos&amp;section=ac&amp;action=delete&amp;id=<?= $entry_id ?>&amp;hash=<?= admin_h($delete_hash) ?>"
                                    onclick="return confirm('Disable this KOS entry?');"
                                >
                                    Disable
                                </a>
                            <?php else: ?>
                                <a
                                    class="btn btn-sm btn-outline-success"
                                    href="/admin/?page=kos&amp;section=ac&amp;action=restore&amp;id=<?= $entry_id ?>&amp;hash=<?= admin_h($restore_hash) ?>"
                                >
                                    Restore
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
<?php admin_pagination_controls('kos', 'ac', $pagination); ?>
    </div>
</div>