<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

global $db, $request, $site_sections;

$message = '';
$error = '';

if (!function_exists('maint_h')) {
    function maint_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$section_key = $request->variable('section', 'site');

if (!isset($site_sections[$section_key])) {
    $section_key = 'site';
}

$section_name = $site_sections[$section_key]['name'];

$form_key = 'admin_link_checker';
add_form_key($form_key);

$action = $request->variable('action', '');
$id = $request->variable('id', 0);

if ($action === 'disable' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_link_checker_disable_' . $id)) {
        trigger_error('Invalid disable request.');
    }

    $sql = 'UPDATE site_links_new
            SET is_active = 0,
                updated_at = "' . $db->sql_escape(date('Y-m-d H:i:s')) . '"
            WHERE id = ' . (int) $id;

    $db->sql_query($sql);

    redirect('/admin/?page=link_checker&section=' . rawurlencode($section_key) . '&disabled=1');
}

if ($request->variable('disabled', 0) === 1) {
    $message = 'Link disabled.';
}

$links = [];

$sql = "SELECT *
        FROM site_links_new
        WHERE section_key = '" . $db->sql_escape($section_key) . "'
        ORDER BY is_active DESC, title ASC";

$result = $db->sql_query_limit($sql, 250);

while ($row = $db->sql_fetchrow($result)) {
    $url = trim((string) ($row['url'] ?? ''));

    $issue = '';

    if ($url === '') {
        $issue = 'Missing URL';
    } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
        $issue = 'Invalid URL format';
    } elseif (!preg_match('~^https?://~i', $url)) {
        $issue = 'Not HTTP/HTTPS';
    }

    if ($issue !== '') {
        $row['_issue'] = $issue;
        $links[] = $row;
    }
}

$db->sql_freeresult($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Link Checker</h1>
        <div class="text-secondary"><?= maint_h($section_name) ?></div>
    </div>

    <a class="btn btn-secondary" href="/admin/">Back to Dashboard</a>
</div>

<?php if ($message !== ''): ?>
    <div class="alert alert-success"><?= maint_h($message) ?></div>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <div class="alert alert-danger"><?= maint_h($error) ?></div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">Select Section</div>

    <div class="card-body">
        <form method="get" class="row g-2">
            <input type="hidden" name="page" value="link_checker">

            <div class="col-md-6">
                <select name="section" class="form-select">
                    <?php foreach ($site_sections as $key => $section): ?>
                        <?php if ($key === 'maintenance') { continue; } ?>
                        <option value="<?= maint_h($key) ?>" <?= $section_key === $key ? 'selected' : '' ?>>
                            <?= maint_h($section['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-light w-100">Scan Links</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        Link Issues
    </div>

    <div class="card-body">
        <div class="alert alert-info">
            This checker validates stored URL format and missing URLs. Live remote HTTP status checking can be added later, but should be done by cron so the admin page does not hang on slow external sites.
        </div>

        <?php if (empty($links)): ?>
            <div class="alert alert-success mb-0">
                No malformed links found for this section.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-dark table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>URL</th>
                            <th>Issue</th>
                            <th>Status</th>
                            <th style="width:180px;">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($links as $link): ?>
                            <?php
                            $link_id = (int) $link['id'];
                            $disable_hash = generate_link_hash('admin_link_checker_disable_' . $link_id);
                            ?>
                            <tr>
                                <td><?= maint_h((string) $link['title']) ?></td>

                                <td>
                                    <code><?= maint_h((string) $link['url']) ?></code>
                                </td>

                                <td>
                                    <span class="badge text-bg-warning"><?= maint_h((string) $link['_issue']) ?></span>
                                </td>

                                <td>
                                    <?= ((int) $link['is_active'] === 1) ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-secondary">Inactive</span>' ?>
                                </td>

                                <td>
                                    <a
                                        class="btn btn-sm btn-outline-primary"
                                        href="/admin/?page=links&amp;section=<?= maint_h((string) $link['section_key']) ?>&amp;action=edit&amp;id=<?= $link_id ?>"
                                    >
                                        Edit
                                    </a>

                                    <?php if ((int) $link['is_active'] === 1): ?>
                                        <a
                                            class="btn btn-sm btn-outline-danger"
                                            href="/admin/?page=link_checker&amp;section=<?= maint_h($section_key) ?>&amp;action=disable&amp;id=<?= $link_id ?>&amp;hash=<?= maint_h($disable_hash) ?>"
                                            onclick="return confirm('Disable this link?');"
                                        >
                                            Disable
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
