<?php
declare(strict_types=1);

global $DB_HOST, $DB_NAME, $DB_PASS, $DB_USER;

if (!function_exists('video_block_h')) {
    function video_block_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('video_block_embed_url')) {
    function video_block_embed_url(string $url): string
    {
        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]+)~', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1] . '?autoplay=1';
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true) ? $url : '';
    }
}

$section_key = $section_key ?? 'ac';
$videos = [];

$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($con) {
    mysqli_set_charset($con, 'utf8mb4');

    $stmt = mysqli_prepare(
        $con,
        "SELECT title, video_url, thumbnail_url
         FROM site_videos
         WHERE section_key = ?
         AND is_active = 1
         ORDER BY sort_order ASC, title ASC
		 LIMIT 1"
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $section_key);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['embed_url'] = video_block_embed_url($row['video_url']);
                $videos[] = $row;
            }

            mysqli_free_result($result);
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($con);
}

if (!$videos) {
    return;
}
?>

<div class="videos">
    <?php foreach ($videos as $index => $video): ?>
        <?php
        $modal_id = 'videoModal_' . preg_replace('/[^a-zA-Z0-9_]/', '', $section_key) . '_' . (int) $index;
        $thumb = $video['thumbnail_url'] ?: 'https://img.youtube.com/vi/default.jpg';
        ?>
<div class="text-center">
    <a
        href="#"
        data-bs-toggle="modal"
        data-bs-target="#<?= video_block_h($modal_id) ?>"
        title="<?= video_block_h($video['title']) ?>"
    >
        <img
            class="img-fluid rounded shadow"
            src="<?= video_block_h($thumb) ?>"
            alt="<?= video_block_h($video['title']) ?>"
            style="
                width:150px;
                max-height:150px;
                cursor:pointer;
                object-fit:cover;
            "
            loading="lazy"
        >
    </a>
    <div class="small text-secondary mt-2">
        <?= video_block_h($video['title']) ?>
    </div>
</div>
        <div class="modal fade" id="<?= video_block_h($modal_id) ?>" tabindex="-1" aria-labelledby="<?= video_block_h($modal_id) ?>Label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-dark text-white">
                    <div class="modal-header">
                        <h5 class="modal-title" id="<?= video_block_h($modal_id) ?>Label">
                            <?= video_block_h($video['title']) ?>
                        </h5>
                        <button type="button" class="btn-close btn-close-white video-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-0">
                        <div class="ratio ratio-16x9">
                            <iframe
                                class="video-frame"
                                data-src="<?= video_block_h($video['embed_url']) ?>"
                                src=""
                                title="<?= video_block_h($video['title']) ?>"
                                allow="autoplay; encrypted-media"
                                allowfullscreen
                            ></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
document.addEventListener('shown.bs.modal', function (event) {
    const iframe = event.target.querySelector('.video-frame');
    if (iframe && iframe.dataset.src) {
        iframe.src = iframe.dataset.src;
    }
});

document.addEventListener('hidden.bs.modal', function (event) {
    const iframe = event.target.querySelector('.video-frame');
    if (iframe) {
        iframe.src = '';
    }
});
</script>