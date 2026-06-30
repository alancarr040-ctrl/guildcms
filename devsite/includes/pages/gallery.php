<?php
declare(strict_types=1);

require_once __DIR__ . '/../layout/framework-helpers.php';
global $request, $DB_HOST, $DB_NAME, $DB_PASS, $DB_USER;

$section_key = $section_key ?? theregs_get_section();
if (!function_exists('site_gallery_h')) {
    function site_gallery_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$pageno = max(1, $request->variable('pageno', 1));
$rowsPerPage = 12;
$offset = ($pageno - 1) * $rowsPerPage;

$totalRows = 0;
$images = [];

$link = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($link) {
    mysqli_set_charset($link, 'utf8mb4');

    /*
     * Count images
     */
    if ($section_key === 'all') {
        $count_sql = "
            SELECT COUNT(*) AS total
            FROM site_gallery
            WHERE is_active = 1
            AND is_approved = 1
        ";

        $count_result = mysqli_query($link, $count_sql);

        if ($count_result) {
            $count_row = mysqli_fetch_assoc($count_result);
            $totalRows = (int) ($count_row['total'] ?? 0);
            mysqli_free_result($count_result);
        }
    } else {
        $stmt = mysqli_prepare(
            $link,
            "
            SELECT COUNT(*) AS total
            FROM site_gallery
            WHERE section_key = ?
            AND is_active = 1
            AND is_approved = 1
            "
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $section_key);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result) {
                $count_row = mysqli_fetch_assoc($result);
                $totalRows = (int) ($count_row['total'] ?? 0);
                mysqli_free_result($result);
            }

            mysqli_stmt_close($stmt);
        }
    }

    /*
     * Load images
     */
    if ($section_key === 'all') {
        $stmt = mysqli_prepare(
            $link,
            "
            SELECT title, caption, image_path, thumbnail_path
            FROM site_gallery
            WHERE is_active = 1
            AND is_approved = 1
            ORDER BY sort_order ASC, created_at DESC, id DESC
            LIMIT ?, ?
            "
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ii', $offset, $rowsPerPage);
        }
    } else {
        $stmt = mysqli_prepare(
            $link,
            "
            SELECT title, caption, image_path, thumbnail_path
            FROM site_gallery
            WHERE section_key = ?
            AND is_active = 1
            AND is_approved = 1
            ORDER BY sort_order ASC, created_at DESC, id DESC
            LIMIT ?, ?
            "
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sii', $section_key, $offset, $rowsPerPage);
        }
    }

    if ($stmt) {
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $images[] = $row;
            }

            mysqli_free_result($result);
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
}

$lastPage = max(1, (int) ceil($totalRows / $rowsPerPage));

$page_base = $section_key === 'all'
    ? '/gallery'
    : '/' . rawurlencode($section_key) . '/gallery';

$site_name = $site_name ?? ucfirst($section_key);

$page_title = $section_key === 'all'
    ? 'Gallery'
    : $site_name . ' Gallery';
?>

<button
    class="btn btn-outline-light d-md-none mb-3"
    type="button"
    data-bs-toggle="offcanvas"
    data-bs-target="#leftSidebar"
    aria-controls="leftSidebar"
>
    ☰ Menu
</button>

<div
    class="offcanvas offcanvas-start text-bg-dark d-md-none"
    tabindex="-1"
    id="leftSidebar"
    aria-labelledby="leftSidebarLabel"
>
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="leftSidebarLabel">
            Navigation
        </h5>

        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas"
            aria-label="Close"
        ></button>
    </div>

    <div class="offcanvas-body">
        <?php render_sidebar($section_key ?? null); ?>
    </div>
</div>

<aside class="col-md-2 d-none d-md-block sidebar-nav">
    <?php render_sidebar($section_key ?? null); ?>
</aside>

<main
    class="col-md-8 text-light"
    style="background:url('//cdn.theregs.org/images/tsw/pixel.webp') repeat;"
>
    <div class="card bg-dark border-secondary text-light my-4">
        <div class="card-header text-center">
            <h2 class="h4 mb-0">
                <?= site_gallery_h($page_title) ?>
            </h2>
        </div>

        <div class="card-body">

            <?php if (!$images): ?>
                <div class="alert alert-secondary text-center mb-0">
                    No images are currently available.
                </div>
            <?php else: ?>

                <nav aria-label="Gallery pagination" class="mb-4">
                    <ul class="pagination justify-content-center flex-wrap">
                        <li class="page-item<?= $pageno <= 1 ? ' disabled' : '' ?>">
                            <a class="page-link" href="<?= site_gallery_h($page_base) ?>/1">First</a>
                        </li>

                        <li class="page-item<?= $pageno <= 1 ? ' disabled' : '' ?>">
                            <a class="page-link" href="<?= site_gallery_h($page_base) ?>/<?= max(1, $pageno - 1) ?>">Prev</a>
                        </li>

                        <li class="page-item disabled">
                            <span class="page-link"><?= (int) $pageno ?> / <?= (int) $lastPage ?></span>
                        </li>

                        <li class="page-item<?= $pageno >= $lastPage ? ' disabled' : '' ?>">
                            <a class="page-link" href="<?= site_gallery_h($page_base) ?>/<?= min($lastPage, $pageno + 1) ?>">Next</a>
                        </li>

                        <li class="page-item<?= $pageno >= $lastPage ? ' disabled' : '' ?>">
                            <a class="page-link" href="<?= site_gallery_h($page_base) ?>/<?= (int) $lastPage ?>">Last</a>
                        </li>
                    </ul>
                </nav>

                <div class="row g-4">
                    <?php foreach ($images as $image): ?>
                        <?php
                        $title = $image['title'] ?: 'Screenshot';
                        $thumb = $image['thumbnail_path'] ?: $image['image_path'];
                        ?>
                        <div class="col-md-4">
                            <div class="card gallery-card h-100">
                                <a
                                    href="#"
                                    data-bs-toggle="modal"
                                    data-bs-target="#galleryModal"
                                    data-img="<?= site_gallery_h($image['image_path']) ?>"
                                    data-title="<?= site_gallery_h($title) ?>"
                                >
                                    <img
                                        src="<?= site_gallery_h($thumb) ?>"
                                        class="card-img-top gallery-thumb"
                                        alt="<?= site_gallery_h($title) ?>"
                                        loading="lazy"
                                    >
                                </a>

                                <div class="card-body gallery-caption">
                                    <p class="card-text text-center mb-0">
                                        <?= site_gallery_h($title) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <nav aria-label="Gallery pagination" class="mt-4">
                    <ul class="pagination justify-content-center flex-wrap">
                        <li class="page-item<?= $pageno <= 1 ? ' disabled' : '' ?>">
                            <a class="page-link" href="<?= site_gallery_h($page_base) ?>/1">First</a>
                        </li>

                        <li class="page-item<?= $pageno <= 1 ? ' disabled' : '' ?>">
                            <a class="page-link" href="<?= site_gallery_h($page_base) ?>/<?= max(1, $pageno - 1) ?>">Prev</a>
                        </li>

                        <li class="page-item disabled">
                            <span class="page-link"><?= (int) $pageno ?> / <?= (int) $lastPage ?></span>
                        </li>

                        <li class="page-item<?= $pageno >= $lastPage ? ' disabled' : '' ?>">
                            <a class="page-link" href="<?= site_gallery_h($page_base) ?>/<?= min($lastPage, $pageno + 1) ?>">Next</a>
                        </li>

                        <li class="page-item<?= $pageno >= $lastPage ? ' disabled' : '' ?>">
                            <a class="page-link" href="<?= site_gallery_h($page_base) ?>/<?= (int) $lastPage ?>">Last</a>
                        </li>
                    </ul>
                </nav>

            <?php endif; ?>

        </div>
    </div>

    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header">
                    <h5 class="modal-title" id="galleryModalLabel">Image Preview</h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>

                <div class="modal-body text-center">
                    <img
                        id="galleryModalImage"
                        src=""
                        alt=""
                        class="img-fluid rounded"
                    >
                </div>
            </div>
        </div>
    </div>
</main>

<?php render_right_sidebar($section_key ?? null); ?>

<script>
document.addEventListener('show.bs.modal', function (event) {
    const trigger = event.relatedTarget;

    if (!trigger || !trigger.dataset.img) {
        return;
    }

    const modal = event.target;
    const modalImage = modal.querySelector('#galleryModalImage');
    const modalTitle = modal.querySelector('#galleryModalLabel');

    modalImage.src = trigger.dataset.img;
    modalImage.alt = trigger.dataset.title || 'Image Preview';
    modalTitle.textContent = trigger.dataset.title || 'Image Preview';
});

document.addEventListener('hidden.bs.modal', function (event) {
    const modalImage = event.target.querySelector('#galleryModalImage');

    if (modalImage) {
        modalImage.src = '';
        modalImage.alt = '';
    }
});
</script>
