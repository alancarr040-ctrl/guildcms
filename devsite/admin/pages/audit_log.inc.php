<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

global $db, $request, $site_sections;

$section_key = $request->variable('section', '');
$page_filter = trim($request->variable('page_filter', '', true));
$action_filter = trim($request->variable('action_filter', '', true));
$user_filter = trim($request->variable('user_filter', '', true));

$where = ['1=1'];

if ($section_key !== '') {
    $where[] = "section_key = '" . $db->sql_escape($section_key) . "'";
}

if ($page_filter !== '') {
    $where[] = "page_name = '" . $db->sql_escape($page_filter) . "'";
}

if ($action_filter !== '') {
    $where[] = "action_name = '" . $db->sql_escape($action_filter) . "'";
}

if ($user_filter !== '') {
    $where[] = "username LIKE '%" . $db->sql_escape($user_filter) . "%'";
}

$where_sql = implode(' AND ', $where);

$pagination = admin_get_pagination(
    $request,
    $db,
    'SELECT COUNT(*) AS total FROM admin_audit_log WHERE ' . $where_sql
);

$sql = 'SELECT *
        FROM admin_audit_log
        WHERE ' . $where_sql . '
        ORDER BY created_at DESC, id DESC';

$result = $db->sql_query_limit($sql, $pagination['per_page'], $pagination['offset']);

$entries = [];

while ($row = $db->sql_fetchrow($result)) {
    $entries[] = $row;
}

$db->sql_freeresult($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Admin Audit Log</h1>
        <div class="text-secondary">Recent admin actions</div>
    </div>

    <a class="btn btn-secondary" href="/admin/">Back to Dashboard</a>
</div>

<div class="card mb-4">
    <div class="card-header">Filters</div>

    <div class="card-body">
        <form method="get" class="row g-2">
            <input type="hidden" name="page" value="audit_log">

            <div class="col-md-3">
                <label class="form-label">Section</label>
                <select class="form-select" name="section">
                    <option value="">All Sections</option>

                    <?php foreach ($site_sections as $key => $section): ?>
                        <option value="<?= admin_h($key) ?>" <?= $section_key === $key ? 'selected' : '' ?>>
                            <?= admin_h($section['name'] ?? $key) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Page</label>
                <input class="form-control" type="text" name="page_filter" value="<?= admin_h($page_filter) ?>" placeholder="articles, races, links">
            </div>

            <div class="col-md-2">
                <label class="form-label">Action</label>
                <input class="form-control" type="text" name="action_filter" value="<?= admin_h($action_filter) ?>" placeholder="create, update">
            </div>

            <div class="col-md-2">
                <label class="form-label">User</label>
                <input class="form-control" type="text" name="user_filter" value="<?= admin_h($user_filter) ?>">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-outline-light w-100" type="submit">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            Audit Entries
            <span class="text-secondary small"><?= number_format($pagination['total']) ?> total</span>
        </div>

        <?php admin_per_page_selector('audit_log', $section_key, $pagination); ?>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Section</th>
                    <th>Page</th>
                    <th>Action</th>
                    <th>Item</th>
                    <th>IP</th>
                    <th>Details</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!$entries): ?>
                    <tr>
                        <td colspan="8" class="text-center text-secondary py-4">
                            No audit entries found.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($entries as $entry): ?>
                    <tr>
                        <td><?= admin_h($entry['created_at']) ?></td>
                        <td>
                            <?= admin_h($entry['username']) ?>
                            <div class="small text-secondary">ID <?= (int) $entry['user_id'] ?></div>
                        </td>
                        <td><code><?= admin_h($entry['section_key']) ?></code></td>
                        <td><code><?= admin_h($entry['page_name']) ?></code></td>
                        <td><span class="badge text-bg-info"><?= admin_h($entry['action_name']) ?></span></td>
                        <td>
                            <?= admin_h($entry['item_title']) ?>
                            <?php if (!empty($entry['item_table'])): ?>
                                <div class="small text-secondary">
                                    <?= admin_h($entry['item_table']) ?>
                                    <?php if (!empty($entry['item_id'])): ?>
                                        #<?= (int) $entry['item_id'] ?>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><code><?= admin_h($entry['ip_address']) ?></code></td>
                        <td><?= admin_h($entry['details']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php admin_pagination_controls('audit_log', $section_key, $pagination); ?>
</div>
