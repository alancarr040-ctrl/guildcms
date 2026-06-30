<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

/*
 * Extract YouTube video ID from common URL formats
 */
function admin_extract_youtube_id(string $url): string
{
    $url = trim($url);

    $patterns = [
        '~youtube\.com/watch\?v=([a-zA-Z0-9_-]{11})~',
        '~youtube\.com/embed/([a-zA-Z0-9_-]{11})~',
        '~youtube\.com/shorts/([a-zA-Z0-9_-]{11})~',
        '~youtu\.be/([a-zA-Z0-9_-]{11})~',
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }

    return '';
}

/*
 * Build thumbnail URL automatically
 */
function admin_youtube_thumbnail(string $url): string
{
    $video_id = admin_extract_youtube_id($url);

    if ($video_id === '') {
        return '';
    }

    return 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg';
}

global $db, $request, $user, $site_sections;

$section_key = $request->variable('section', 'site');

if (!isset($site_sections[$section_key])) {
    $section_key = 'site';
}

if (!in_array('videos', $site_sections[$section_key]['modules'], true)) {
    trigger_error('Videos are not enabled for this section.');
}

$section_name = $site_sections[$section_key]['name'];

$form_key = 'admin_videos';
add_form_key($form_key);

$message = '';
$error = '';

$action = $request->variable('action', '');
$id = $request->variable('id', 0);

function admin_redirect_videos(string $section_key): void
{
    redirect('/admin/?page=videos&section=' . rawurlencode($section_key));
}

if ($request->is_set_post('save')) {
    if (!check_form_key($form_key)) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $id = $request->variable('id', 0);
        $title = trim($request->variable('title', '', true));
        $video_url = trim($request->variable('video_url', '', true));
        $thumbnail_url = trim($request->variable('thumbnail_url', '', true));
        $sort_order = $request->variable('sort_order', 0);
        $is_active = $request->variable('is_active', 1);

        if ($thumbnail_url === '') {
            $thumbnail_url = admin_youtube_thumbnail($video_url);
        }

        if ($title === '') {
            $error = 'Video title is required.';
        } elseif ($video_url === '') {
            $error = 'Video URL is required.';
        } elseif (!filter_var($video_url, FILTER_VALIDATE_URL)) {
            $error = 'Please enter a valid video URL including http:// or https://.';
        } elseif ($thumbnail_url !== '' && !filter_var($thumbnail_url, FILTER_VALIDATE_URL)) {
            $error = 'Please enter a valid thumbnail URL including http:// or https://.';
        } else {
            if ($id > 0) {
                $sql = 'UPDATE site_videos SET ' . $db->sql_build_array('UPDATE', [
                    'title' => $title,
                    'video_url' => $video_url,
                    'thumbnail_url' => $thumbnail_url,
                    'sort_order' => $sort_order,
                    'is_active' => $is_active ? 1 : 0,
                    'updated_by_user_id' => (int) $user->data['user_id'],
                    'updated_by_name' => (string) $user->data['username'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]) . '
                WHERE id = ' . (int) $id . "
                AND section_key = '" . $db->sql_escape($section_key) . "'";

                $db->sql_query($sql);
                $message = 'Video updated.';
            } else {
                $sql = 'INSERT INTO site_videos ' . $db->sql_build_array('INSERT', [
                    'section_key' => $section_key,
                    'title' => $title,
                    'video_url' => $video_url,
                    'thumbnail_url' => $thumbnail_url,
                    'sort_order' => $sort_order,
                    'is_active' => $is_active ? 1 : 0,
                    'created_by_user_id' => (int) $user->data['user_id'],
                    'created_by_name' => (string) $user->data['username'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $db->sql_query($sql);
                $message = 'Video added.';
            }
        }
    }
}

if ($action === 'delete' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_videos_delete_' . $id)) {
        trigger_error('Invalid disable request.');
    }

    $sql = 'UPDATE site_videos
            SET is_active = 0,
                updated_by_user_id = ' . (int) $user->data['user_id'] . ",
                updated_by_name = '" . $db->sql_escape((string) $user->data['username']) . "',
                updated_at = '" . $db->sql_escape(date('Y-m-d H:i:s')) . "'
            WHERE id = " . (int) $id . "
            AND section_key = '" . $db->sql_escape($section_key) . "'";

    $db->sql_query($sql);
    admin_redirect_videos($section_key);
}

if ($action === 'restore' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_videos_restore_' . $id)) {
        trigger_error('Invalid restore request.');
    }

    $sql = 'UPDATE site_videos
            SET is_active = 1,
                updated_by_user_id = ' . (int) $user->data['user_id'] . ",
                updated_by_name = '" . $db->sql_escape((string) $user->data['username']) . "',
                updated_at = '" . $db->sql_escape(date('Y-m-d H:i:s')) . "'
            WHERE id = " . (int) $id . "
            AND section_key = '" . $db->sql_escape($section_key) . "'";

    $db->sql_query($sql);
    admin_redirect_videos($section_key);
}

$edit_video = [
    'id' => 0,
    'title' => '',
    'video_url' => '',
    'thumbnail_url' => '',
    'sort_order' => 0,
    'is_active' => 1,
];

if ($action === 'edit' && $id > 0) {
    $sql = 'SELECT *
            FROM site_videos
            WHERE id = ' . (int) $id . "
            AND section_key = '" . $db->sql_escape($section_key) . "'";

    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    if ($row) {
        $edit_video = $row;
    }
}

$pagination = admin_get_pagination(
    $request,
    $db,
    "SELECT COUNT(*) AS total
     FROM site_videos
     WHERE section_key = '" . $db->sql_escape($section_key) . "'"
);

$sql = "SELECT *
        FROM site_videos
        WHERE section_key = '" . $db->sql_escape($section_key) . "'
        ORDER BY is_active DESC, sort_order ASC, title ASC";

$result = $db->sql_query_limit($sql, $pagination['per_page'], $pagination['offset']);

$videos = [];

while ($row = $db->sql_fetchrow($result)) {
    $videos[] = $row;
}

$db->sql_freeresult($result);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Manage Videos</h1>
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
        <?= ((int) $edit_video['id'] > 0) ? 'Edit Video' : 'Add Video' ?>
    </div>

    <div class="card-body">
        <form method="post" action="/admin/?page=videos&amp;section=<?= admin_h($section_key) ?>">
<?= build_hidden_fields([
    'id' => (int) $edit_video['id'],
]) ?>

<?= admin_form_token($form_key) ?>


            <div class="mb-3">
                <label class="form-label" for="title">Video Title</label>
                <input
                    type="text"
                    class="form-control"
                    id="title"
                    name="title"
                    value="<?= admin_h($edit_video['title']) ?>"
                    maxlength="255"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label" for="video_url">Video URL</label>
                <input
                    type="url"
                    class="form-control"
                    id="video_url"
                    name="video_url"
                    value="<?= admin_h($edit_video['video_url']) ?>"
                    maxlength="500"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label" for="thumbnail_url">Thumbnail URL</label>
                <input
                    type="url"
                    class="form-control"
                    id="thumbnail_url"
                    name="thumbnail_url"
                    value="<?= admin_h($edit_video['thumbnail_url']) ?>"
                    maxlength="500"
                >
                <div class="form-text">
					Leave blank to automatically generate a YouTube thumbnail.
				</div>
            </div>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label" for="sort_order">Sort Order</label>
                    <input
                        type="number"
                        class="form-control"
                        id="sort_order"
                        name="sort_order"
                        value="<?= (int) $edit_video['sort_order'] ?>"
                    >
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label" for="is_active">Status</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="1" <?= ((int) $edit_video['is_active'] === 1) ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ((int) $edit_video['is_active'] === 0) ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="save" value="1" class="btn btn-primary">
                Save Video
            </button>

            <?php if ((int) $edit_video['id'] > 0): ?>
                <a class="btn btn-outline-secondary" href="/admin/?page=videos&amp;section=<?= admin_h($section_key) ?>">
                    Cancel Edit
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">

        <div>
        Existing Videos
		
            <span class="text-secondary small">
                <?= number_format($pagination['total']) ?> total
            </span>
    </div>
        <?php
        admin_per_page_selector(
            'videos',
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
                    <th style="width: 90px;">Image</th>
                    <th>Title</th>
                    <th>Video URL</th>
                    <th>Status</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!$videos): ?>
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-4">
                            No videos have been added for this section yet.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($videos as $video): ?>
                    <?php
                    $video_id = (int) $video['id'];
                    $delete_hash = generate_link_hash('admin_videos_delete_' . $video_id);
                    $restore_hash = generate_link_hash('admin_videos_restore_' . $video_id);
                    ?>
                    <tr class="<?= ((int) $video['is_active'] === 0) ? 'table-secondary opacity-75' : '' ?>">
                        <td><?= (int) $video['sort_order'] ?></td>

                        <td>
                            <?php if (!empty($video['thumbnail_url'])): ?>
                                <img
                                    src="<?= admin_h($video['thumbnail_url']) ?>"
                                    alt=""
                                    style="max-width: 80px; height: auto;"
                                >
                            <?php else: ?>
                                <span class="text-secondary small">No image</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <strong><?= admin_h($video['title']) ?></strong>
                        </td>

                        <td>
                            <a href="<?= admin_h($video['video_url']) ?>" target="_blank" rel="noopener">
                                <?= admin_h($video['video_url']) ?>
                            </a>
                        </td>

                        <td>
                            <?php if ((int) $video['is_active'] === 1): ?>
                                <span class="badge text-bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a
                                class="btn btn-sm btn-outline-primary"
                                href="/admin/?page=videos&amp;section=<?= admin_h($section_key) ?>&amp;action=edit&amp;id=<?= $video_id ?>"
                            >
                                Edit
                            </a>

                            <?php if ((int) $video['is_active'] === 1): ?>
                                <a
                                    class="btn btn-sm btn-outline-danger"
                                    href="/admin/?page=videos&amp;section=<?= admin_h($section_key) ?>&amp;action=delete&amp;id=<?= $video_id ?>&amp;hash=<?= admin_h($delete_hash) ?>"
                                    onclick="return confirm('Disable this video?');"
                                >
                                    Disable
                                </a>
                            <?php else: ?>
                                <a
                                    class="btn btn-sm btn-outline-success"
                                    href="/admin/?page=videos&amp;section=<?= admin_h($section_key) ?>&amp;action=restore&amp;id=<?= $video_id ?>&amp;hash=<?= admin_h($restore_hash) ?>"
                                >
                                    Restore
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
<?php admin_pagination_controls('videos', $section_key, $pagination); ?>
    </div>
</div>