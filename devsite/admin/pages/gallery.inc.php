<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

global $db, $request, $user, $site_sections;

$section_key = $request->variable('section', 'site');

if (!isset($site_sections[$section_key])) {
    $section_key = 'site';
}

if (!in_array('gallery', $site_sections[$section_key]['modules'], true)) {
    trigger_error('Gallery is not enabled for this section.');
}

$section_name = $site_sections[$section_key]['name'];

$form_key = 'admin_gallery';
add_form_key($form_key);

$message = '';
$error = '';

$action = $request->variable('action', '');
$id      = $request->variable('id', 0);


/* Upload paths */
$upload_root = '/home/theregs/public_html/images/gallery/';
$upload_web_root = '/images/gallery/';

if (!is_dir($upload_root)) {
    mkdir($upload_root, 0755, true);
}

$section_upload_dir = $upload_root . $section_key . '/';
$section_web_dir    = $upload_web_root . $section_key . '/';

if (!is_dir($section_upload_dir)) {
    mkdir($section_upload_dir, 0755, true);
}

/* Pagination */
$per_page = $request->variable('per_page', 25);
$page_num = max(1, $request->variable('p', 1));

$allowed_per_page = [
    25,
    50,
    100,
    250,
];

if (!in_array($per_page, $allowed_per_page, true)) {
    $per_page = 25;
}

$offset = ($page_num - 1) * $per_page;

$sql = "SELECT COUNT(*) AS total
        FROM site_gallery
        WHERE section_key = '" . $db->sql_escape($section_key) . "'";

$result = $db->sql_query($sql);

$total_images = (int)$db->sql_fetchfield('total');

$db->sql_freeresult($result);

$total_pages = max(
    1,
    (int)ceil($total_images / $per_page)
);


/* Redirect helper */

function admin_redirect_gallery(string $section_key): void
{
    redirect(
        '/admin/?page=gallery&section=' .
        rawurlencode($section_key)
    );
}

/* Upload hardening helpers */

function admin_gallery_upload_error_message(int $error_code): string
{
    switch ($error_code) {
        case UPLOAD_ERR_OK:
            return '';
        case UPLOAD_ERR_INI_SIZE:
            return 'Upload failed: File exceeds server upload_max_filesize.';
        case UPLOAD_ERR_FORM_SIZE:
            return 'Upload failed: File exceeds form size limit.';
        case UPLOAD_ERR_PARTIAL:
            return 'Upload failed: File was only partially uploaded.';
        case UPLOAD_ERR_NO_FILE:
            return 'Upload failed: No file was selected.';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Upload failed: Missing PHP temporary upload directory.';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Upload failed: Cannot write uploaded file to disk.';
        case UPLOAD_ERR_EXTENSION:
            return 'Upload failed: A PHP extension blocked the upload.';
        default:
            return 'Upload failed: Unknown error #' . $error_code;
    }
}

function admin_gallery_validate_image_upload(array $file, array $allowed_extensions, array $allowed_mime_types, ?int &$width, ?int &$height, ?int &$filesize): string
{
    $width = null;
    $height = null;
    $filesize = null;

    $upload_error = admin_gallery_upload_error_message((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE));
    if ($upload_error !== '') {
        return $upload_error;
    }

    $tmp_name = (string) ($file['tmp_name'] ?? '');
    $original_name = (string) ($file['name'] ?? '');

    if ($tmp_name === '' || !is_uploaded_file($tmp_name)) {
        return 'Upload failed: Invalid uploaded file.';
    }

    $ext = strtolower((string) pathinfo($original_name, PATHINFO_EXTENSION));
    if ($ext === '' || !in_array($ext, $allowed_extensions, true)) {
        return 'Invalid image type. Allowed types: JPG, PNG, GIF, and WEBP.';
    }

    // Defense in depth: explicitly reject known executable/script extensions even if double-extension tricks are attempted.
    $dangerous_extensions = ['php', 'phtml', 'phar', 'cgi', 'pl', 'py', 'sh', 'asp', 'aspx', 'jsp'];
    $name_parts = array_map('strtolower', explode('.', $original_name));
    foreach ($name_parts as $part) {
        if (in_array($part, $dangerous_extensions, true)) {
            return 'Invalid image filename.';
        }
    }

    $actual_size = @filesize($tmp_name);
    if ($actual_size === false || $actual_size <= 0) {
        return 'Upload failed: Uploaded file is empty or unreadable.';
    }

    $filesize = (int) $actual_size;

    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($tmp_name);

        if (!isset($allowed_mime_types[$ext]) || !in_array($mime, $allowed_mime_types[$ext], true)) {
            return 'Invalid image content type.';
        }
    }

    $image_info = @getimagesize($tmp_name);
    if ($image_info === false || empty($image_info[0]) || empty($image_info[1])) {
        return 'Invalid image file.';
    }

    $width = (int) $image_info[0];
    $height = (int) $image_info[1];

    $expected_image_type = [
        'jpg' => IMAGETYPE_JPEG,
        'jpeg' => IMAGETYPE_JPEG,
        'png' => IMAGETYPE_PNG,
        'gif' => IMAGETYPE_GIF,
        'webp' => IMAGETYPE_WEBP,
    ];

    if (isset($expected_image_type[$ext]) && (int) ($image_info[2] ?? 0) !== $expected_image_type[$ext]) {
        return 'Image extension does not match image content.';
    }

    // Optional deeper decode check when GD support exists for the uploaded type.
    $decode_ok = true;
    if (($ext === 'jpg' || $ext === 'jpeg') && function_exists('imagecreatefromjpeg')) {
        $img = @imagecreatefromjpeg($tmp_name);
        $decode_ok = ($img !== false);
    } elseif ($ext === 'png' && function_exists('imagecreatefrompng')) {
        $img = @imagecreatefrompng($tmp_name);
        $decode_ok = ($img !== false);
    } elseif ($ext === 'gif' && function_exists('imagecreatefromgif')) {
        $img = @imagecreatefromgif($tmp_name);
        $decode_ok = ($img !== false);
    } elseif ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
        $img = @imagecreatefromwebp($tmp_name);
        $decode_ok = ($img !== false);
    } else {
        $img = null;
    }

    if (isset($img) && $img instanceof GdImage) {
        imagedestroy($img);
    } elseif (isset($img) && is_resource($img)) {
        imagedestroy($img);
    }

    if (!$decode_ok) {
        return 'Invalid or malformed image file.';
    }

    return '';
}

/* Save Image */
if ($request->is_set_post('save')) {

    if (!check_form_key($form_key)) {

        $error = 'Invalid form submission.';

    } else {

        $id = $request->variable('id', 0);
        $title = trim($request->variable('title', '', true));
        $caption = trim($request->variable('caption', '', true));
        $sort_order = $request->variable('sort_order', 0);
        $is_active = $request->variable('is_active', 1);
        $is_approved = $request->variable('is_approved', 1);

        if ($title === '') {
            $error = 'Image title is required.';
        } else {
            $image_path = '';
            $width = null;
            $height = null;
            $filesize = null;

            /* New Upload */
            $file = $request->file('image_file');
            if (!empty($file['name'])) {
                $allowed = [
                    'jpg',
                    'jpeg',
                    'png',
                    'gif',
                    'webp',
                ];

                $allowed_mime_types = [
                    'jpg' => ['image/jpeg', 'image/pjpeg'],
                    'jpeg' => ['image/jpeg', 'image/pjpeg'],
                    'png' => ['image/png', 'image/x-png'],
                    'gif' => ['image/gif'],
                    'webp' => ['image/webp'],
                ];

                $ext = strtolower((string) pathinfo((string) $file['name'], PATHINFO_EXTENSION));
                $validation_error = admin_gallery_validate_image_upload($file, $allowed, $allowed_mime_types, $width, $height, $filesize);

                if ($validation_error !== '') {
                    $error = $validation_error;
                } else {
                    $new_name = bin2hex(random_bytes(16)) . '.' . $ext;
                    $target = $section_upload_dir . $new_name;

                    if (!is_dir($section_upload_dir)) {
                        mkdir($section_upload_dir, 0755, true);
                    }

                    if (move_uploaded_file((string) $file['tmp_name'], $target)) {
                        @chmod($target, 0644);
                        $image_path = $section_web_dir . $new_name;
                        $stored_size = @filesize($target);
                        if ($stored_size !== false) {
                            $filesize = (int) $stored_size;
                        }
                    } else {
                        $error = 'Could not save upload.';
                    }
                }
            }

            /* Database Save */
            if ($error === '') {
                if ($id > 0) {
                    $data = [
                        'title' => $title,
                        'caption' => $caption,
                        'sort_order' => $sort_order,
                        'is_active' => $is_active ? 1 : 0,
                        'is_approved' => $is_approved ? 1 : 0,
                        'updated_by_user_id' => (int)$user->data['user_id'],
                        'updated_by_name' => $user->data['username'],
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];

                    if ($image_path !== '') {
                        $data['image_path'] = $image_path;
                        $data['thumbnail_path'] = $image_path;
                        $data['width'] = $width;
                        $data['height'] = $height;
                        $data['filesize'] = $filesize;
                    }
                    $sql =
                        'UPDATE site_gallery SET ' .
                        $db->sql_build_array(
                            'UPDATE',
                            $data
                        ) .
                        '
                        WHERE id=' .
                        (int)$id .
                        "
                        AND section_key='" .
                        $db->sql_escape($section_key) .
                        "'";
                    $db->sql_query($sql);
                    $message =
                        'Image updated.';

                } else {
                    if ($image_path === '') {
                        $error =
                            'Select an image first.';
                    } else {
                        $sql =
                            'INSERT INTO site_gallery ' .
                            $db->sql_build_array(
                                'INSERT',
                                [
                                    'section_key' => $section_key,
                                    'title' => $title,
                                    'caption' => $caption,
                                    'image_path' => $image_path,
                                    'thumbnail_path' => $image_path,
                                    'width' => $width,
                                    'height' => $height,
                                    'filesize' => $filesize,
                                    'created_by_user_id' => (int)$user->data['user_id'],
                                    'created_by_name' => $user->data['username'],
                                    'created_at' => date('Y-m-d H:i:s'),
                                ]
                            );
                        $db->sql_query($sql);
                        $message =
                            'Image added.';
                    }
                }
            }
        }
    }
}

/* Disable / Restore */
if ($action === 'delete' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_gallery_delete_' . $id)) {
        trigger_error('Invalid disable request.');
    }

    $sql = 'UPDATE site_gallery SET ' . $db->sql_build_array('UPDATE', [
        'is_active' => 0,
        'updated_by_user_id' => (int) $user->data['user_id'],
        'updated_by_name' => (string) $user->data['username'],
        'updated_at' => date('Y-m-d H:i:s'),
    ]) . '
    WHERE id = ' . (int) $id . "
    AND section_key = '" . $db->sql_escape($section_key) . "'";

    $db->sql_query($sql);
    admin_redirect_gallery($section_key);
}

if ($action === 'restore' && $id > 0) {
    if (!check_link_hash($request->variable('hash', ''), 'admin_gallery_restore_' . $id)) {
        trigger_error('Invalid restore request.');
    }

    $sql = 'UPDATE site_gallery SET ' . $db->sql_build_array('UPDATE', [
        'is_active' => 1,
        'updated_by_user_id' => (int) $user->data['user_id'],
        'updated_by_name' => (string) $user->data['username'],
        'updated_at' => date('Y-m-d H:i:s'),
    ]) . '
    WHERE id = ' . (int) $id . "
    AND section_key = '" . $db->sql_escape($section_key) . "'";

    $db->sql_query($sql);
    admin_redirect_gallery($section_key);
}

/* Load Edit Image */
$edit_image = [
    'id' => 0,
    'title' => '',
    'caption' => '',
    'image_path' => '',
    'thumbnail_path' => '',
    'sort_order' => 0,
    'is_active' => 1,
    'is_approved' => 1,
];

if ($action === 'edit' && $id > 0) {
    $sql = 'SELECT *
            FROM site_gallery
            WHERE id = ' . (int) $id . "
            AND section_key = '" . $db->sql_escape($section_key) . "'";

    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    if ($row) {
        $edit_image = $row;
    }
}

/* Load Paginated Images */
$sql = "SELECT *
        FROM site_gallery
        WHERE section_key = '" . $db->sql_escape($section_key) . "'
        ORDER BY is_active DESC, sort_order ASC, created_at DESC";

$result = $db->sql_query_limit($sql, $per_page, $offset);

$images = [];

while ($row = $db->sql_fetchrow($result)) {
    $images[] = $row;
}

$db->sql_freeresult($result);

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Manage Gallery</h1>
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
        <?= ((int) $edit_image['id'] > 0) ? 'Edit Gallery Image' : 'Add Gallery Image' ?>
    </div>

    <div class="card-body">
        <form method="post" enctype="multipart/form-data" action="/admin/?page=gallery&amp;section=<?= admin_h($section_key) ?>">
<?= build_hidden_fields([
    'id' => (int) $edit_image['id'],
]) ?>
<?= admin_form_token($form_key) ?>

            <?php if (!empty($edit_image['image_path'])): ?>
                <div class="mb-3">
                    <img
                        src="<?= admin_h($edit_image['thumbnail_path'] ?: $edit_image['image_path']) ?>"
                        alt=""
                        style="max-width: 220px; height: auto;"
                        class="img-thumbnail"
                    >
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label" for="title">Image Title</label>
                <input
                    type="text"
                    class="form-control"
                    id="title"
                    name="title"
                    value="<?= admin_h($edit_image['title']) ?>"
                    maxlength="255"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label" for="caption">Caption</label>
                <textarea class="form-control" id="caption" name="caption" rows="3"><?= admin_h($edit_image['caption']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label" for="image_file">Image File</label>
                <input
                    type="file"
                    class="form-control"
                    id="image_file"
                    name="image_file"
                    accept=".jpg,.jpeg,.png,.gif,.webp"
                    <?= ((int) $edit_image['id'] === 0) ? 'required' : '' ?>
                >
                <div class="form-text">
                    JPG, PNG, GIF, or WEBP. Uploading a new file while editing replaces the image.
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
                        value="<?= (int) $edit_image['sort_order'] ?>"
                    >
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label" for="is_approved">Approval</label>
                    <select class="form-select" id="is_approved" name="is_approved">
                        <option value="1" <?= ((int) $edit_image['is_approved'] === 1) ? 'selected' : '' ?>>Approved</option>
                        <option value="0" <?= ((int) $edit_image['is_approved'] === 0) ? 'selected' : '' ?>>Pending</option>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label" for="is_active">Status</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="1" <?= ((int) $edit_image['is_active'] === 1) ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= ((int) $edit_image['is_active'] === 0) ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="save" value="1" class="btn btn-primary">
                Save Image
            </button>

            <?php if ((int) $edit_image['id'] > 0): ?>
                <a class="btn btn-outline-secondary" href="/admin/?page=gallery&amp;section=<?= admin_h($section_key) ?>">
                    Cancel Edit
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            Existing Gallery Images
            <span class="text-secondary small">
                <?= number_format($total_images) ?> total
            </span>
        </div>

        <form method="get" action="/admin/" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="page" value="gallery">
            <input type="hidden" name="section" value="<?= admin_h($section_key) ?>">

            <label class="small text-secondary" for="per_page_top">Per page</label>
            <select class="form-select form-select-sm" id="per_page_top" name="per_page" onchange="this.form.submit()">
                <?php foreach ([25, 50, 100, 250] as $option): ?>
                    <option value="<?= $option ?>" <?= ($per_page === $option) ? 'selected' : '' ?>>
                        <?= $option ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="px-3 py-2 border-bottom text-secondary small">
        Showing page <?= (int) $page_num ?> of <?= (int) $total_pages ?>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 70px;">Sort</th>
                    <th style="width: 120px;">Image</th>
                    <th>Title</th>
                    <th>Size</th>
                    <th>Approval</th>
                    <th>Status</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!$images): ?>
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-4">
                            No gallery images have been added for this section yet.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($images as $image): ?>
                    <?php
                    $image_id = (int) $image['id'];
                    $delete_hash = generate_link_hash('admin_gallery_delete_' . $image_id);
                    $restore_hash = generate_link_hash('admin_gallery_restore_' . $image_id);
                    $display_image = $image['thumbnail_path'] ?: $image['image_path'];
                    ?>

                    <tr class="<?= ((int) $image['is_active'] === 0) ? 'table-secondary opacity-75' : '' ?>">
                        <td><?= (int) $image['sort_order'] ?></td>

                        <td>
                            <?php if (!empty($display_image)): ?>
                                <a href="<?= admin_h($image['image_path']) ?>" target="_blank" rel="noopener">
                                    <img
                                        src="<?= admin_h($display_image) ?>"
                                        alt=""
                                        style="max-width: 100px; height: auto;"
                                        class="img-thumbnail"
                                        loading="lazy"
                                    >
                                </a>
                            <?php else: ?>
                                <span class="text-secondary small">No image</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <strong><?= admin_h($image['title']) ?></strong>

                            <?php if (!empty($image['caption'])): ?>
                                <div class="small text-secondary">
                                    <?= admin_h($image['caption']) ?>
                                </div>
                            <?php endif; ?>

                            <div class="small text-secondary">
                                <?= admin_h($image['image_path']) ?>
                            </div>
                        </td>

                        <td>
                            <?php if (!empty($image['width']) && !empty($image['height'])): ?>
                                <?= (int) $image['width'] ?> × <?= (int) $image['height'] ?><br>
                            <?php endif; ?>

                            <?php if (!empty($image['filesize'])): ?>
                                <span class="small text-secondary">
                                    <?= number_format((int) $image['filesize'] / 1024, 1) ?> KB
                                </span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ((int) $image['is_approved'] === 1): ?>
                                <span class="badge text-bg-success">Approved</span>
                            <?php else: ?>
                                <span class="badge text-bg-warning">Pending</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ((int) $image['is_active'] === 1): ?>
                                <span class="badge text-bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a
                                class="btn btn-sm btn-outline-primary"
                                href="/admin/?page=gallery&amp;section=<?= admin_h($section_key) ?>&amp;action=edit&amp;id=<?= $image_id ?>&amp;p=<?= (int) $page_num ?>&amp;per_page=<?= (int) $per_page ?>"
                            >
                                Edit
                            </a>

                            <?php if ((int) $image['is_active'] === 1): ?>
                                <a
                                    class="btn btn-sm btn-outline-danger"
                                    href="/admin/?page=gallery&amp;section=<?= admin_h($section_key) ?>&amp;action=delete&amp;id=<?= $image_id ?>&amp;hash=<?= admin_h($delete_hash) ?>&amp;p=<?= (int) $page_num ?>&amp;per_page=<?= (int) $per_page ?>"
                                    onclick="return confirm('Disable this image?');"
                                >
                                    Disable
                                </a>
                            <?php else: ?>
                                <a
                                    class="btn btn-sm btn-outline-success"
                                    href="/admin/?page=gallery&amp;section=<?= admin_h($section_key) ?>&amp;action=restore&amp;id=<?= $image_id ?>&amp;hash=<?= admin_h($restore_hash) ?>&amp;p=<?= (int) $page_num ?>&amp;per_page=<?= (int) $per_page ?>"
                                >
                                    Restore
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="card-footer">
            <nav>
                <ul class="pagination pagination-sm mb-0 flex-wrap">
                    <?php if ($page_num > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="/admin/?page=gallery&amp;section=<?= admin_h($section_key) ?>&amp;p=<?= $page_num - 1 ?>&amp;per_page=<?= $per_page ?>">
                                Previous
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php
                    $start_page = max(1, $page_num - 3);
                    $end_page = min($total_pages, $page_num + 3);
                    ?>

                    <?php if ($start_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="/admin/?page=gallery&amp;section=<?= admin_h($section_key) ?>&amp;p=1&amp;per_page=<?= $per_page ?>">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?= ($i === $page_num) ? 'active' : '' ?>">
                            <a class="page-link" href="/admin/?page=gallery&amp;section=<?= admin_h($section_key) ?>&amp;p=<?= $i ?>&amp;per_page=<?= $per_page ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">…</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="/admin/?page=gallery&amp;section=<?= admin_h($section_key) ?>&amp;p=<?= $total_pages ?>&amp;per_page=<?= $per_page ?>">
                                <?= $total_pages ?>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($page_num < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="/admin/?page=gallery&amp;section=<?= admin_h($section_key) ?>&amp;p=<?= $page_num + 1 ?>&amp;per_page=<?= $per_page ?>">
                                Next
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>