<?php
declare(strict_types=1);


require_once __DIR__ . '/../layout/framework-helpers.php';
$site_name = $site_name ?? 'The Regs';

$forum_id = $forum_id ?? 0;
if (!function_exists('forums_h')) {
    function forums_h(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}


$forum_url = '/forums/';

if ($forum_id > 0) {
    $forum_url = '/forums/viewforum.php?f=' . (int)$forum_id;
}
?>


<!-- Sidebar Toggle Button -->
<button
    class="btn btn-outline-light d-md-none mb-3"
    type="button"
    data-bs-toggle="offcanvas"
    data-bs-target="#leftSidebar"
    aria-controls="leftSidebar"
>
    ☰ Menu
</button>


<!-- Mobile Sidebar -->
<div
    class="offcanvas offcanvas-start text-bg-dark d-md-none"
    tabindex="-1"
    id="leftSidebar"
>
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">
            <?= forums_h($site_name); ?>
        </h5>

        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas"
        ></button>
    </div>

    <div class="offcanvas-body">
        <?php render_sidebar($section_key ?? null); ?>
    </div>
</div>


<!-- Desktop Sidebar -->
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
                <?= forums_h($site_name); ?> Forums
            </h2>
        </div>


        <div class="card-body lh-lg text-center">

            <p>
                Access our <?= forums_h($site_name); ?> community discussions,
                guild announcements, guides, and archived posts.
            </p>

            <p>
                The Regs uses phpBB for a secure and familiar discussion format.
            </p>


            <a
                href="<?= forums_h($forum_url); ?>"
                class="btn btn-outline-primary"
            >
                Visit <?= forums_h($site_name); ?> Forums
            </a>

        </div>

    </div>

</main>


<?php render_right_sidebar($section_key ?? null); ?>