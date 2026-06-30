<?php
declare(strict_types=1);

require_once __DIR__ . '/../layout/framework-helpers.php';

global $request, $DB_HOST, $DB_NAME, $DB_PASS, $DB_USER;

$section_key = $section_key ?? theregs_get_section();
if (!function_exists('site_video_h')) {
    function site_video_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('site_video_id')) {
    function site_video_id(string $url): string
    {
        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]+)~', $url, $match)) {
            return $match[1];
        }

        return '';
    }
}

if (!function_exists('site_video_thumb')) {
    function site_video_thumb(string $url): string
    {
        $id = site_video_id($url);

        return $id !== ''
            ? 'https://img.youtube.com/vi/' . $id . '/hqdefault.jpg'
            : '';
    }
}

if (!function_exists('site_video_embed_url')) {
    function site_video_embed_url(string $url): string
    {
        $id = site_video_id($url);

        return $id !== ''
            ? 'https://www.youtube.com/embed/' . $id
            : $url;
    }
}

$pageno = max(1, $request->variable('pageno', 1));
$rowsPerPage = 12;
$offset = ($pageno - 1) * $rowsPerPage;

$totalRows = 0;
$videos = [];

$link = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($link) {
    mysqli_set_charset($link, 'utf8mb4');

    /*
     * Count videos
     */
    if ($section_key === 'all') {
        $countSql = "
            SELECT COUNT(*) AS total
            FROM site_videos
            WHERE is_active = 1
        ";

        $countResult = mysqli_query($link, $countSql);

        if ($countResult) {
            $countRow = mysqli_fetch_assoc($countResult);
            $totalRows = (int) ($countRow['total'] ?? 0);
            mysqli_free_result($countResult);
        }
    } else {
        $stmt = mysqli_prepare(
            $link,
            "
            SELECT COUNT(*) AS total
            FROM site_videos
            WHERE section_key = ?
            AND is_active = 1
            "
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $section_key);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            if ($result) {
                $countRow = mysqli_fetch_assoc($result);
                $totalRows = (int) ($countRow['total'] ?? 0);
                mysqli_free_result($result);
            }

            mysqli_stmt_close($stmt);
        }
    }

    /*
     * Load videos
     */
    if ($section_key === 'all') {
        $stmt = mysqli_prepare(
            $link,
            "
            SELECT title, video_url, thumbnail_url
            FROM site_videos
            WHERE is_active = 1
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
            SELECT title, video_url, thumbnail_url
            FROM site_videos
            WHERE section_key = ?
            AND is_active = 1
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
                $videos[] = $row;
            }

            mysqli_free_result($result);
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
}

$lastPage = max(1, (int) ceil($totalRows / $rowsPerPage));

$page_base = $section_key === 'all'
    ? '/videos'
    : '/' . rawurlencode($section_key) . '/videos';

$site_name = $site_name ?? ucfirst($section_key);

$page_title = $section_key === 'all'
    ? 'Videos'
    : $site_name . ' Videos';
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
        <h5 class="offcanvas-title" id="leftSidebarLabel">Navigation</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        <?php render_sidebar($section_key ?? null); ?>
    </div>
</div>

<aside class="col-md-2 d-none d-md-block sidebar-nav">
    <?php render_sidebar($section_key ?? null); ?>
</aside>

<main class="col-md-8 text-light" style="background:url('//cdn.theregs.org/images/tsw/pixel.webp') repeat;">
    <div class="card bg-dark border-secondary text-light my-4">
        <div class="card-header text-center">
            <h2 class="h4 mb-0">
                <?= site_video_h($page_title) ?>
            </h2>
        </div>

        <div class="card-body">
            <?php if (!$videos): ?>
                <div class="alert alert-secondary text-center mb-0">
                    No videos are currently available.
                </div>
            <?php else: ?>

                <?php if ($lastPage > 1): ?>
                    <nav aria-label="Videos pagination" class="mb-4">
                        <ul class="pagination justify-content-center flex-wrap">
                            <li class="page-item<?= $pageno <= 1 ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= site_video_h($page_base) ?>/1">First</a>
                            </li>

                            <li class="page-item<?= $pageno <= 1 ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= site_video_h($page_base) ?>/<?= max(1, $pageno - 1) ?>">Prev</a>
                            </li>

                            <li class="page-item disabled">
                                <span class="page-link"><?= (int) $pageno ?> / <?= (int) $lastPage ?></span>
                            </li>

                            <li class="page-item<?= $pageno >= $lastPage ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= site_video_h($page_base) ?>/<?= min($lastPage, $pageno + 1) ?>">Next</a>
                            </li>

                            <li class="page-item<?= $pageno >= $lastPage ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= site_video_h($page_base) ?>/<?= (int) $lastPage ?>">Last</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

                <div class="row g-4">
                    <?php foreach ($videos as $video): ?>
                        <?php
                        $title = $video['title'] ?: 'Video';
                        $thumb = site_video_thumb($video['video_url']);

                        if ($thumb === '' && !empty($video['thumbnail_url'])) {
                            $thumb = $video['thumbnail_url'];
                        }

                        $embed = site_video_embed_url($video['video_url']);
                        ?>
                        <div class="col-md-4">
                            <div class="card video-card h-100">
                                <a
                                    href="#"
                                    data-bs-toggle="modal"
                                    data-bs-target="#videoModal"
                                    data-video="<?= site_video_h($embed) ?>"
                                    data-title="<?= site_video_h($title) ?>"
                                >
                                    <img
                                        src="<?= site_video_h($thumb) ?>"
                                        class="card-img-top video-thumb"
                                        alt="<?= site_video_h($title) ?>"
                                        loading="lazy"
                                    >
                                </a>

                                <div class="card-body video-caption">
                                    <p class="card-text text-center mb-0">
                                        <strong><?= site_video_h($title) ?></strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($lastPage > 1): ?>
                    <nav aria-label="Videos pagination" class="mt-4">
                        <ul class="pagination justify-content-center flex-wrap">
                            <li class="page-item<?= $pageno <= 1 ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= site_video_h($page_base) ?>/1">First</a>
                            </li>

                            <li class="page-item<?= $pageno <= 1 ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= site_video_h($page_base) ?>/<?= max(1, $pageno - 1) ?>">Prev</a>
                            </li>

                            <li class="page-item disabled">
                                <span class="page-link"><?= (int) $pageno ?> / <?= (int) $lastPage ?></span>
                            </li>

                            <li class="page-item<?= $pageno >= $lastPage ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= site_video_h($page_base) ?>/<?= min($lastPage, $pageno + 1) ?>">Next</a>
                            </li>

                            <li class="page-item<?= $pageno >= $lastPage ? ' disabled' : '' ?>">
                                <a class="page-link" href="<?= site_video_h($page_base) ?>/<?= (int) $lastPage ?>">Last</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header">
                    <h5 class="modal-title" id="videoModalTitle">Video</h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>

                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <iframe
                            id="videoFrame"
                            src=""
                            title="Video"
                            allow="autoplay; encrypted-media"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php render_right_sidebar($section_key ?? null); ?>

<script>
document.addEventListener('show.bs.modal', function (event) {
    if (event.target.id !== 'videoModal') {
        return;
    }

    const trigger = event.relatedTarget;

    if (!trigger || !trigger.dataset.video) {
        return;
    }

    const frame = document.getElementById('videoFrame');
    const title = document.getElementById('videoModalTitle');

    frame.src = trigger.dataset.video + '?autoplay=1';
    title.textContent = trigger.dataset.title || 'Video';
});

document.addEventListener('hidden.bs.modal', function (event) {
    if (event.target.id !== 'videoModal') {
        return;
    }

    const frame = document.getElementById('videoFrame');
    frame.src = '';
});
</script>
