<?php
declare(strict_types=1);

global $db, $request, $user;

if (!function_exists('cleanup_h')) {
    function cleanup_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('content_cleanup_clean_html')) {
    function content_cleanup_clean_html(string $html): string
    {
        $html = trim($html);

        $patterns = [
            '~<div\s+class=["\']back_text["\']>\s*~i' => '',
            '~<div\s+class=["\']back_title["\']>\s*~i' => '',
            '~</div>~i' => '',
            '~<center>\s*~i' => '',
            '~</center>\s*~i' => '',
            '~<font[^>]*>~i' => '',
            '~</font>~i' => '',
            '~<br\s*/?>\s*(<br\s*/?>\s*)+~i' => "\n\n",
            '~\s+style=["\'][^"\']*["\']~i' => '',
            '~\s+width=["\'][^"\']*["\']~i' => '',
            '~\s+height=["\'][^"\']*["\']~i' => '',
            '~\s+align=["\'][^"\']*["\']~i' => '',
			'~\s+class=["\']imagewrap["\']~i' => '',
			'~\s+class=["\']locimg["\']~i' => '',
			'~\s+class=["\'][^"\']*["\']~i' => '',
			'~\s+style=[^ >]+~i' => '',
			'~\s+width=[^ >]+~i' => '',
			'~\s+height=[^ >]+~i' => '',
			'~\s+align=[^ >]+~i' => '',
			'~<br\s*/?>~i' => "\n",
			'~<table[^>]*>~i' => '',
			'~</table>~i' => '',
			'~<tbody[^>]*>~i' => '',
			'~</tbody>~i' => '',
			'~<tr[^>]*>~i' => '',
			'~</tr>~i' => '',
			'~<td[^>]*>~i' => '',
			'~</td>~i' => '',
			"\t\t\t<p>" => '<p>',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $html = preg_replace($pattern, $replacement, $html);
        }

        return trim((string) $html);
    }
}

$targets = [
    'wow_zone' => [
        'label' => 'WoW Zones',
        'pk' => 'id',
        'title' => 'name',
        'fields' => ['zone_text', 'zone_history'],
    ],
    'wow_races' => [
        'label' => 'WoW Races',
        'pk' => 'id',
        'title' => 'name',
        'fields' => [
            'text1',
            'history_text',
            'start_zone_text',
            'home_city_text',
            'leader_text',
            'traits1_txt',
            'traits2_txt',
            'trait3_txt',
            'trait4_txt',
        ],
    ],
];

$bad_patterns = [
    '<center',
    '</center',
    '<font',
    '</font',
    '<table',
    '</table',
    '<tr',
    '</tr',
    '<td',
    '</td',
    'class="imagewrap"',
    "class='imagewrap'",
    'class="locimg"',
    "class='locimg'",
    'class="back_text"',
    "class='back_text'",
    'class="back_title"',
    "class='back_title'",
    'style=',
    'width=',
    'height=',
    '<br />',
	'<br />',
    '<br>',
    '</div>',
    'float:',
    'align=',
    'class=',
];

$selected_table = $request->variable('table', '');
$action = $request->variable('action', '');
$preview_id = $request->variable('id', 0);
$preview_field = $request->variable('field', '');
$history_id = $request->variable('history_id', 0);

$rows = [];
$preview = null;
$history_rows = [];

$form_key = 'content_cleanup_apply';
$restore_form_key = 'content_cleanup_restore';

add_form_key($form_key);
add_form_key($restore_form_key);

$message = '';
$error = '';

if ($request->variable('applied', 0) === 1) {
    $message = 'Cleanup applied successfully.';
}

if ($request->variable('restored', 0) === 1) {
    $message = 'Original content restored successfully.';
}

/*
 * Restore original content from history
 */
if (
    $action === 'restore' &&
    $request->is_set_post('restore_cleanup') &&
    $history_id > 0
) {
    if (!check_form_key($restore_form_key)) {
        $error = 'Invalid restore submission.';
    } else {
        $sql = 'SELECT *
                FROM content_cleanup_history
                WHERE id = ' . (int) $history_id;

        $result = $db->sql_query($sql);
        $history = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

        if (!$history) {
            $error = 'Cleanup history record could not be found.';
        } elseif (!isset($targets[$history['table_name']])) {
            $error = 'Cleanup history refers to an unknown table.';
        } elseif (!in_array((string) $history['field_name'], $targets[$history['table_name']]['fields'], true)) {
            $error = 'Cleanup history refers to an invalid field.';
        } else {
            $restore_table = str_replace('`', '', (string) $history['table_name']);
            $restore_field = str_replace('`', '', (string) $history['field_name']);
            $restore_pk = str_replace('`', '', $targets[$history['table_name']]['pk']);
            $restore_value = (string) $history['old_value'];

            $sql = 'UPDATE `' . $restore_table . '`
                    SET `' . $restore_field . "` = '" . $db->sql_escape($restore_value) . "'
                    WHERE `" . $restore_pk . '` = ' . (int) $history['record_id'];

            $db->sql_query($sql);

            redirect(
                '/admin/?page=content_cleanup&section=maintenance&table=' .
                rawurlencode((string) $history['table_name']) .
                '&restored=1'
            );
        }
    }
}

/*
 * Apply cleanup
 */
if (
    $action === 'apply' &&
    $request->is_set_post('apply_cleanup') &&
    isset($targets[$selected_table]) &&
    $preview_id > 0 &&
    in_array($preview_field, $targets[$selected_table]['fields'], true)
) {
    if (!check_form_key($form_key)) {
        $error = 'Invalid form submission.';
    } else {
        $target = $targets[$selected_table];

        $pk = str_replace('`', '', $target['pk']);
        $title_col = str_replace('`', '', $target['title']);
        $safe_table = str_replace('`', '', $selected_table);
        $safe_field = str_replace('`', '', $preview_field);

        $sql = "
            SELECT `{$pk}`, `{$title_col}`, `{$safe_field}`
            FROM `{$safe_table}`
            WHERE `{$pk}` = " . (int) $preview_id;

        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

        if ($row) {
            $old_value = (string) $row[$preview_field];
            $new_value = content_cleanup_clean_html($old_value);
            $title_value = (string) $row[$title_col];

            if ($old_value !== $new_value) {
                $sql = 'INSERT INTO content_cleanup_history ' . $db->sql_build_array('INSERT', [
                    'table_name' => $selected_table,
                    'record_id' => (int) $preview_id,
                    'field_name' => $preview_field,
                    'title' => $title_value,
                    'old_value' => $old_value,
                    'new_value' => $new_value,
                    'changed_by_user_id' => (int) $user->data['user_id'],
                    'changed_by_name' => (string) $user->data['username'],
                    'changed_at' => date('Y-m-d H:i:s'),
                ]);

                $db->sql_query($sql);

                $sql = 'UPDATE `' . $safe_table . '`
                        SET `' . $safe_field . "` = '" . $db->sql_escape($new_value) . "'
                        WHERE `" . $pk . '` = ' . (int) $preview_id;

                $db->sql_query($sql);
            }

            redirect(
                '/admin/?page=content_cleanup&section=maintenance&table=' .
                rawurlencode($selected_table) .
                '&applied=1'
            );
        } else {
            $error = 'Selected record could not be found.';
        }
    }
}

/*
 * Preview cleanup
 */
if (
    $action === 'preview' &&
    isset($targets[$selected_table]) &&
    $preview_id > 0 &&
    in_array($preview_field, $targets[$selected_table]['fields'], true)
) {
    $target = $targets[$selected_table];

    $pk = str_replace('`', '', $target['pk']);
    $title_col = str_replace('`', '', $target['title']);
    $safe_table = str_replace('`', '', $selected_table);
    $safe_field = str_replace('`', '', $preview_field);

    $sql = "
        SELECT `{$pk}`, `{$title_col}`, `{$safe_field}`
        FROM `{$safe_table}`
        WHERE `{$pk}` = " . (int) $preview_id;

    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    if ($row) {
        $old_value = (string) $row[$preview_field];
        $new_value = content_cleanup_clean_html($old_value);

        $preview = [
            'id' => (int) $row[$pk],
            'title' => (string) $row[$title_col],
            'field' => $preview_field,
            'old_value' => $old_value,
            'new_value' => $new_value,
            'changed' => $old_value !== $new_value,
        ];
    }
}

/*
 * Scan selected table
 */
if (isset($targets[$selected_table])) {
    $target = $targets[$selected_table];

    $pk = $target['pk'];
    $title = $target['title'];
    $fields = $target['fields'];

    $columns = array_merge([$pk, $title], $fields);
    $safe_columns = array_map(
        static fn($col) => '`' . str_replace('`', '', $col) . '`',
        $columns
    );

    $safe_table = str_replace('`', '', $selected_table);
    $safe_title = str_replace('`', '', $title);

    $sql = "
        SELECT " . implode(', ', $safe_columns) . "
        FROM `{$safe_table}`
        ORDER BY `{$safe_title}` ASC
    ";

    $result = $db->sql_query($sql);

    while ($row = $db->sql_fetchrow($result)) {
        $matches = [];

        foreach ($fields as $field) {
            $value = (string) ($row[$field] ?? '');

            foreach ($bad_patterns as $pattern) {
                if (stripos($value, $pattern) !== false) {
                    $matches[$field][] = $pattern;
                }
            }
        }

        if (!empty($matches)) {
            $row['_matches'] = $matches;
            $rows[] = $row;
        }
    }

    $db->sql_freeresult($result);
}

/*
 * Recent cleanup history
 */
$sql = 'SELECT *
        FROM content_cleanup_history
        ORDER BY changed_at DESC, id DESC';

$result = $db->sql_query_limit($sql, 10);

while ($row = $db->sql_fetchrow($result)) {
    $history_rows[] = $row;
}

$db->sql_freeresult($result);
?>

<div class="container-fluid text-light">

    <div class="card bg-dark border-secondary my-4">
        <div class="card-header">
            <h2 class="h4 mb-0">Content Cleanup Scanner</h2>
        </div>

        <div class="card-body">

            <?php if ($message !== ''): ?>
                <div class="alert alert-success">
                    <?= cleanup_h($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="alert alert-danger">
                    <?= cleanup_h($error) ?>
                </div>
            <?php endif; ?>

            <form method="get" class="row g-2 mb-4">
                <input type="hidden" name="page" value="content_cleanup">
                <input type="hidden" name="section" value="maintenance">

                <div class="col-md-6">
                    <select name="table" class="form-select">
                        <option value="">Select Content Area</option>

                        <?php foreach ($targets as $table => $target): ?>
                            <option value="<?= cleanup_h($table) ?>" <?= $selected_table === $table ? 'selected' : '' ?>>
                                <?= cleanup_h($target['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-light w-100">
                        Scan
                    </button>
                </div>
            </form>

            <?php if ($preview !== null): ?>
                <div class="card bg-black border-warning mb-4">
                    <div class="card-header text-warning">
                        Preview Cleanup:
                        <?= cleanup_h($preview['title']) ?>
                        /
                        <?= cleanup_h($preview['field']) ?>
                    </div>

                    <div class="card-body">
                        <?php if (!$preview['changed']): ?>

                            <div class="alert alert-secondary">
                                Cleanup would not change this field.
                            </div>

                        <?php else: ?>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <h3 class="h6">Original</h3>
                                    <pre class="bg-dark text-light border rounded p-3 small"><?= cleanup_h($preview['old_value']) ?></pre>
                                </div>

                                <div class="col-md-6">
                                    <h3 class="h6">Cleaned Preview</h3>
                                    <pre class="bg-dark text-light border rounded p-3 small"><?= cleanup_h($preview['new_value']) ?></pre>
                                </div>
                            </div>

                            <form
                                method="post"
                                action="/admin/?page=content_cleanup&amp;section=maintenance&amp;table=<?= cleanup_h($selected_table) ?>&amp;action=apply&amp;id=<?= (int) $preview['id'] ?>&amp;field=<?= cleanup_h($preview['field']) ?>"
                                class="mt-3"
                                onsubmit="return confirm('Apply this cleanup? The original value will be saved to cleanup history.');"
                            >
                                <?= build_hidden_fields([
                                    'creation_time' => time(),
                                    'form_token' => sha1(time() . $user->data['user_form_salt'] . $form_key),
                                ]) ?>

                                <button type="submit" name="apply_cleanup" value="1" class="btn btn-warning">
                                    Apply Cleanup
                                </button>
                            </form>

                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($selected_table === ''): ?>

                <div class="alert alert-secondary">
                    Select a content area to scan for old layout HTML.
                </div>

            <?php elseif (!isset($targets[$selected_table])): ?>

                <div class="alert alert-danger">
                    Invalid content area selected.
                </div>

            <?php elseif (empty($rows)): ?>

                <div class="alert alert-success">
                    No cleanup issues found.
                </div>

            <?php else: ?>

                <div class="alert alert-warning">
                    Found <?= count($rows) ?> records with old layout markup.
                    Use Preview to review changes before applying cleanup.
                </div>

                <div class="table-responsive">
                    <table class="table table-dark table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th style="width:80px;">ID</th>
                                <th>Name</th>
                                <th>Field</th>
                                <th>Matched Markup</th>
                                <th style="width:120px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <?php foreach ($row['_matches'] as $field => $matches): ?>
                                    <tr>
                                        <td><?= cleanup_h((string) $row[$targets[$selected_table]['pk']]) ?></td>

                                        <td><?= cleanup_h((string) $row[$targets[$selected_table]['title']]) ?></td>

                                        <td>
                                            <code><?= cleanup_h($field) ?></code>
                                        </td>

                                        <td>
                                            <?php foreach ($matches as $match): ?>
                                                <span class="badge text-bg-warning me-1">
                                                    <?= cleanup_h($match) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </td>

                                        <td>
                                            <a
                                                class="btn btn-sm btn-outline-warning"
                                                href="/admin/?page=content_cleanup&amp;section=maintenance&amp;table=<?= cleanup_h($selected_table) ?>&amp;action=preview&amp;id=<?= (int) $row[$targets[$selected_table]['pk']] ?>&amp;field=<?= cleanup_h($field) ?>"
                                            >
                                                Preview
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <div class="card bg-dark border-secondary my-4">
        <div class="card-header">
            <h2 class="h5 mb-0">Recent Cleanup History</h2>
        </div>

        <div class="card-body">
            <?php if (empty($history_rows)): ?>

                <div class="alert alert-secondary mb-0">
                    No cleanup history has been recorded yet.
                </div>

            <?php else: ?>

                <div class="table-responsive">
                    <table class="table table-dark table-striped table-bordered align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Table</th>
                                <th>Record</th>
                                <th>Field</th>
                                <th>Changed By</th>
                                <th style="width:140px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($history_rows as $history): ?>
                                <tr>
                                    <td><?= cleanup_h((string) $history['changed_at']) ?></td>

                                    <td>
                                        <code><?= cleanup_h((string) $history['table_name']) ?></code>
                                    </td>

                                    <td>
                                        <?= cleanup_h((string) $history['title']) ?>
                                        <div class="small text-secondary">
                                            ID <?= (int) $history['record_id'] ?>
                                        </div>
                                    </td>

                                    <td>
                                        <code><?= cleanup_h((string) $history['field_name']) ?></code>
                                    </td>

                                    <td><?= cleanup_h((string) $history['changed_by_name']) ?></td>

                                    <td>
                                        <form
                                            method="post"
                                            action="/admin/?page=content_cleanup&amp;section=maintenance&amp;action=restore&amp;history_id=<?= (int) $history['id'] ?>"
                                            onsubmit="return confirm('Restore the original content from this cleanup history record?');"
                                        >
                                            <?= build_hidden_fields([
                                                'creation_time' => time(),
                                                'form_token' => sha1(time() . $user->data['user_form_salt'] . $restore_form_key),
                                            ]) ?>

                                            <button type="submit" name="restore_cleanup" value="1" class="btn btn-sm btn-outline-danger">
                                                Restore
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>
        </div>
    </div>

</div>
