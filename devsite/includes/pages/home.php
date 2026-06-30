<?php
declare(strict_types=1);

require_once __DIR__ . '/../layout/framework-helpers.php';
?>

<!-- Sidebar Toggle Button: visible on small screens only -->
<button
    class="btn btn-outline-light d-md-none mb-3"
    type="button"
    data-bs-toggle="offcanvas"
    data-bs-target="#leftSidebar"
    aria-controls="leftSidebar"
>
    ☰ Menu
</button>

<!-- Sidebar Offcanvas: hidden on desktop, slides in on mobile -->
<div
    class="offcanvas offcanvas-start d-md-none text-bg-dark"
    tabindex="-1"
    id="leftSidebar"
    aria-labelledby="leftSidebarLabel"
>
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="leftSidebarLabel">Navigation</h5>

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

<!-- Static Sidebar: visible on md+ screens -->
<aside class="col-md-2 d-none d-md-block sidebar-nav">
    <?php render_sidebar($section_key ?? null); ?>
</aside>

<main
    class="col-md-8 text-light"
    style="background:url('//cdn.theregs.org/images/tsw/pixel.webp') repeat;"
>
    <div class="text-center my-4">
        <img
            src="//cdn.theregs.org/assets/img/logo-placeholder.webp"
            alt="Darktide Regulators Banner"
            width="800"
            height="200"
            class="img-fluid rounded shadow"
        >
    </div>

    <div class="card bg-dark border-secondary text-light my-4">
        <div class="card-header text-center">
            <h2 class="h4 mb-0">Latest News</h2>
        </div>

        <div class="card-body">
                <?php
					$theregs_articles_embedded = true;
					$theregs_articles_show_sidebars = false;
					$theregs_articles_show_title = false;
					$theregs_articles_category = 'news';
					$theregs_articles_limit = 5;

					include __DIR__ . '/articles.php';
                ?>
        </div>
    </div>
</main>

<?php render_right_sidebar($section_key ?? null); ?>