<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

global $db, $request;

if (!function_exists('maint_h')) {
    function maint_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$targets = [
    'site_videos' => [
        'label' => 'Video Thumbnails',
        'pk' => 'id',
        'title' => 'title',
        'fields' => ['thumbnail_url'],
        'edit_page' => 'videos',
        'section_field' => 'section_key',
    ],
    'wow_zone' => [
        'label' => 'WoW Zone Images',
        'pk' => 'id',
        'title' => 'name',
        'fields' => ['zone_image', 'background'],
        'edit_page' => 'world',
        'section' => 'wow',
    ],
    'wow_races' => [
        'label' => 'WoW Race Images',
        'pk' => 'id',
        'title' => 'name',
        'fields' => [
            'race_back',
            'char_image',
            'leader_image',
            'traits1_img',
            'traits2_img',
            'traits3_img',
            'trait4_img',
        ],
        'edit_page' => 'races',
        'section' => 'wow',
    ],
];

$selected_target = $request->variable('target', '');
$issues = [];

function maint_is_external_url(string $path): bool
{
    return preg_match('~^https?://~i', $path) === 1 || str_starts_with($path, '//');
}

function maint_public_path_to_file(string $path): string
{
    $path = trim($path);

    if ($path === '') {
        return '';
    }

    if (maint_is_external_url($path)) {
        return '';
    }

    $path = parse_url($path, PHP_URL_PATH) ?: $path;
    $path = '/' . ltrim($path, '/');

    return realpath(__DIR__ . '/../../') . $path;
}

if (isset($targets[$selected_target])) {
    $target = $targets[$selected_target];

    $columns = array_merge([$target['pk'], $target['title']], $target['fields']);

    if (isset($target['section_field'])) {
        $columns[] = $target['section_field'];
    }

    $safe_columns = array_map(
        static fn($col) => '`' . str_replace('`', '', $col) . '`',
        array_unique($columns)
    );

    $safe_table = str_replace('`', '', $selected_target);
    $safe_title = str_replace('`', '', $target['title']);

    $sql = "
        SELECT " . implode(', ', $safe_columns) . "
        FROM `{$safe_table}`
        ORDER BY `{$safe_title}` ASC
    ";

    $result = $db->sql_query($sql);

    while ($row = $db->sql_fetchrow($result)) {
        foreach ($target['fields'] as $field) {
            $value = trim((string) ($row[$field] ?? ''));

            if ($value === '') {
                continue;
            }

            if (maint_is_external_url($value)) {
                continue;
            }

            $file_path = maint_public_path_to_file($value);

            if ($file_path === '' || !is_file($file_path)) {
                $issues[] = [
                    'id' => (int) $row[$target['pk']],
                    'title' => (string) $row[$target['title']],
                    'field' => $field,
                    'value' => $value,
                    'issue' => 'Missing local file',
                    'section' => $row[$target['section_field']] ?? ($target['section'] ?? 'site'),
                    'edit_page' => $target['edit_page'],
                ];
            }
        }
    }

    $db->sql_freeresult($result);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Image Checker</h1>
        <div class="text-secondary">Find missing locally referenced images</div>
    </div>

    <a class="btn btn-secondary" href="/admin/">Back to Dashboard</a>
</div>

<div class="card mb-4">
    <div class="card-header">Select Image Area</div>

    <div class="card-body">
        <form method="get" class="row g-2">
            <input type="hidden" name="page" value="image_checker">
            <input type="hidden" name="section" value="maintenance">

            <div class="col-md-6">
                <select name="target" class="form-select">
                    <option value="">Select Image Area</option>

                    <?php foreach ($targets as $key => $target): ?>
                        <option value="<?= maint_h($key) ?>" <?= $selected_target === $key ? 'selected' : '' ?>>
                            <?= maint_h($target['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-light w-100">Scan Images</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">Image Issues</div>

    <div class="card-body">
        <?php if ($selected_target === ''): ?>
            <div class="alert alert-secondary mb-0">
                Select an image area to scan.
            </div>
        <?php elseif (!isset($targets[$selected_target])): ?>
            <div class="alert alert-danger mb-0">
                Invalid image area selected.
            </div>
        <?php elseif (empty($issues)): ?>
            <div class="alert alert-success mb-0">
                No missing local images found.
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                Found <?= count($issues) ?> image references that do not exist locally.
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Record</th>
                            <th>Field</th>
                            <th>Path</th>
                            <th>Issue</th>
                            <th style="width:100px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($issues as $issue): ?>
                            <tr>
                                <td>
                                    <?= maint_h($issue['title']) ?>
                                    <div class="small text-secondary">
                                        ID <?= (int) $issue['id'] ?>
                                    </div>
                                </td>

                                <td>
                                    <code><?= maint_h($issue['field']) ?></code>
                                </td>

                                <td>
                                    <code><?= maint_h($issue['value']) ?></code>
                                </td>

                                <td>
                                    <span class="badge text-bg-warning"><?= maint_h($issue['issue']) ?></span>
                                </td>

                                <td>
                                    <a
                                        class="btn btn-sm btn-outline-primary"
                                        href="/admin/?page=<?= maint_h($issue['edit_page']) ?>&amp;section=<?= maint_h($issue['section']) ?>&amp;action=edit&amp;id=<?= (int) $issue['id'] ?>"
                                    >
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
