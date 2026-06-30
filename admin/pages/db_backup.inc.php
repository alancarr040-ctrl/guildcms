<?php
declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/config.inc.php';

if (!defined('IN_ADMIN')) {
    exit;
}

global $request;

$db_host = $GLOBALS['DB_HOST'] ?? '';
$db_name = $GLOBALS['DB_NAME'] ?? '';
$db_user = $GLOBALS['DB_USER'] ?? '';
$db_pass = $GLOBALS['DB_PASS'] ?? '';

$form_key = 'admin_db_backup';
add_form_key($form_key);

$backup_dir = '/home/theregs/backups/mysql';
$message = '';
$error = '';

if ($request->variable('started', 0) === 1) {
    $message = 'Backup started. Refresh this page in a few minutes.';
}
if ($request->variable('deleted', 0) === 1) {
    $message = 'Backup deleted.';
}
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0700, true);
}
/* Create Backup */
if ($request->is_set_post('create_backup')) {
    if (!check_form_key($form_key)) {
        $error = 'Invalid form submission.';
    } else {
        $running = glob($backup_dir . '/*.tmp') ?: [];
        $request_file = $backup_dir . '/backup.request';

        if (!empty($running)) {
            $error = 'A database backup is already running. Please wait for it to finish.';
        } elseif (is_file($request_file)) {
            $error = 'A database backup is already queued. Cron should start it shortly.';
        } else {
            file_put_contents(
                $request_file,
                json_encode([
                    'requested_at' => date('Y-m-d H:i:s'),
                    'requested_by' => (string) $user->data['username'],
                ], JSON_PRETTY_PRINT)
            );

            redirect('/admin/?page=db_backup&section=maintenance&started=1');
        }
    }
}
/* Delete Backup */
$delete = $request->variable('delete', '');

if ($delete !== '') {

    $file = basename($delete);
    $target = $backup_dir . '/' . $file;

if (is_file($target)) {
    unlink($target);

    header(
        'Location: /admin/?page=db_backup&section=maintenance&deleted=1'
    );
    exit;
}
}

/* List backups */
$backups = glob($backup_dir . '/*.sql.gz') ?: [];
rsort($backups);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">
            Database Backup
        </h1>
        <div class="text-secondary">
            Backup and restore protection
        </div>
    </div>
    <a href="/admin/" class="btn btn-secondary">
        Dashboard
    </a>
</div>

<?php if ($message): ?>
<div class="alert alert-success">
    <?= admin_h($message) ?>
</div>
<?php endif; ?>


<?php if ($error): ?>
<div class="alert alert-danger">
    <?= admin_h($error) ?>
</div>
<?php endif; ?>


<?php if (is_file($backup_dir . '/backup.request')): ?>
<div class="alert alert-info">
    <strong>Backup queued.</strong><br>
    Waiting for cron to start the database backup.
</div>
<?php endif; ?>


<?php if (!empty(glob($backup_dir . '/*.tmp'))): ?>
<div class="alert alert-warning">
    <strong>Backup running.</strong><br>
    Database dump is currently being created.
</div>
<?php endif; ?>


<div class="card mb-4">

<div class="card-header">
Create Backup
</div>

<div class="card-body">

<form method="post">

<?= build_hidden_fields([
    'creation_time' => time(),
    'form_token' => sha1(time() . $user->data['user_form_salt'] . $form_key),
]) ?>

<button 
    class="btn btn-primary"
    name="create_backup"
    value="1"
>
Create Database Backup
</button>
</form>
</div>
</div>
<div class="card">
<div class="card-header">
Existing Backups
</div>
<table class="table table-striped mb-0">
<thead>
<tr>
<th>File</th>
<th>Size</th>
<th>Date</th>
<th></th>
</tr>
</thead>
<tbody>
<?php foreach ($backups as $backup): ?>
<?php
$file = basename($backup);
?>
<tr>
<td>
<?= admin_h($file) ?>
</td>
<td>
<?= number_format(filesize($backup) / 1024 / 1024, 2) ?> MB
</td>
<td>
<?= date('Y-m-d H:i:s', filemtime($backup)) ?>
</td>
<td>
<a
class="btn btn-sm btn-danger"
onclick="return confirm('Delete backup?');"
href="/admin/?page=db_backup&amp;section=maintenance&amp;delete=<?= urlencode($file) ?>"
>
Delete
</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php
$log_file = $backup_dir . '/backup.log';
$log_lines = [];

if (is_file($log_file) && is_readable($log_file)) {
    $all_lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $log_lines = array_reverse(
    array_slice($all_lines ?: [], -10)
);
}
?>

<div class="card mt-4">

    <div class="card-header">
        Last Backup Activity
    </div>

    <div class="card-body">

        <?php if (empty($log_lines)): ?>

            <div class="text-secondary">
                No backup activity has been logged yet.
            </div>

        <?php else: ?>

            <pre class="bg-black text-light border rounded p-3 mb-0" style="white-space: pre-wrap;"><?=
                admin_h(implode("\n", $log_lines))
            ?></pre>

        <?php endif; ?>

    </div>

</div>