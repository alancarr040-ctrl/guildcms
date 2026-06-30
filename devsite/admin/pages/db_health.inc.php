<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

global $db;

if (!function_exists('maint_h')) {
    function maint_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$tables = [];
$total_size = 0;

$sql = "
    SELECT
        table_name,
        table_rows,
        data_length,
        index_length,
        engine,
        table_collation
    FROM information_schema.tables
    WHERE table_schema = DATABASE()
    ORDER BY (data_length + index_length) DESC
";

$result = $db->sql_query($sql);

while ($row = $db->sql_fetchrow($result)) {
    $size = (int) $row['data_length'] + (int) $row['index_length'];
    $total_size += $size;

    $tables[] = [
        'name' => (string) $row['table_name'],
        'rows' => (int) $row['table_rows'],
        'size' => $size,
        'engine' => (string) $row['engine'],
        'collation' => (string) $row['table_collation'],
    ];
}

$db->sql_freeresult($result);

function maint_format_bytes(int $bytes): string
{
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    }

    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    }

    if ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }

    return $bytes . ' B';
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Database Health</h1>
        <div class="text-secondary">Database size and table overview</div>
    </div>

    <a class="btn btn-secondary" href="/admin/">Back to Dashboard</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">Total Database Size</div>
            <div class="card-body">
                <div class="display-6"><?= maint_h(maint_format_bytes($total_size)) ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">Tables</div>
            <div class="card-body">
                <div class="display-6"><?= count($tables) ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">Largest Table</div>
            <div class="card-body">
                <?php if (!empty($tables)): ?>
                    <div class="h5"><?= maint_h($tables[0]['name']) ?></div>
                    <div class="text-secondary"><?= maint_h(maint_format_bytes($tables[0]['size'])) ?></div>
                <?php else: ?>
                    <div class="text-secondary">No tables found.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Tables by Size</div>

    <div class="table-responsive">
        <table class="table table-dark table-striped table-bordered align-middle mb-0">
            <thead>
                <tr>
                    <th>Table</th>
                    <th>Rows</th>
                    <th>Size</th>
                    <th>Engine</th>
                    <th>Collation</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($tables as $table): ?>
                    <tr>
                        <td><code><?= maint_h($table['name']) ?></code></td>
                        <td><?= number_format($table['rows']) ?></td>
                        <td><?= maint_h(maint_format_bytes($table['size'])) ?></td>
                        <td><?= maint_h($table['engine']) ?></td>
                        <td><?= maint_h($table['collation']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
