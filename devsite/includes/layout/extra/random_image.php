<?php
declare(strict_types=1);
$section_key = $section_key ?? 'all';
global $DB_HOST, $DB_NAME, $DB_PASS, $DB_USER;
if (!function_exists('gallery_block_h')) {
    function gallery_block_h(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
$image = null;
$con = mysqli_connect(
    $DB_HOST,
    $DB_USER,
    $DB_PASS,
    $DB_NAME
);

if ($con) {
    mysqli_set_charset($con, 'utf8mb4');
    $section_key = $section_key ?? 'all';

    /*
     Root site:
     Pull from every gallery
     */
    if ($section_key === 'all') {
        $sql = "
            SELECT
                title,
                image_path,
                thumbnail_path
            FROM site_gallery
            WHERE is_active = 1
            AND is_approved = 1
            ORDER BY RAND()
            LIMIT 1
        ";

        $result = mysqli_query($con, $sql);

        if ($result) {
            $image = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
        }

    }

    /*
      Individual site:
      AC, AO, WoW, etc.
     */
    else {

        $stmt = mysqli_prepare(
            $con,
            "
            SELECT
                title,
                image_path,
                thumbnail_path
            FROM site_gallery
            WHERE section_key = ?
            AND is_active = 1
            AND is_approved = 1
            ORDER BY RAND()
            LIMIT 1
            "
        );


        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                's',
                $section_key
            );


            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                $image = mysqli_fetch_assoc($result);
                mysqli_free_result($result);
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($con);
}

if (!$image) {
    return;
}
$title = $image['title'] ?: 'Gallery Image';
$imageUrl = $image['image_path'];

$thumbUrl =
    !empty($image['thumbnail_path'])
        ? $image['thumbnail_path']
        : $imageUrl;
?>

<div class="text-center">
    <!-- Trigger image -->
    <img
        class="img-fluid rounded shadow"
        src="<?= gallery_block_h($thumbUrl) ?>"
        alt="<?= gallery_block_h($title) ?>"
        style="
            width:150px;
            max-height:150px;
            cursor:pointer;
            object-fit:cover;
        "
        loading="lazy"
        data-bs-toggle="modal"
        data-bs-target="#randomGalleryModal"
    >

    <div class="small text-secondary mt-2">
        <?= gallery_block_h($title) ?>
    </div>
</div>

<!-- Modal -->
<div
    class="modal fade"
    id="randomGalleryModal"
    tabindex="-1"
    aria-labelledby="randomGalleryModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark text-light">
            <div class="modal-header">
                <h5
                    class="modal-title"
                    id="randomGalleryModalLabel"
                >
                    <?= gallery_block_h($title) ?>
                </h5>
                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>
            <div class="modal-body text-center">
                <img
                    src="<?= gallery_block_h($imageUrl) ?>"
                    alt="<?= gallery_block_h($title) ?>"
                    class="img-fluid rounded"
                    loading="lazy"
                >
            </div>
        </div>
    </div>
</div>