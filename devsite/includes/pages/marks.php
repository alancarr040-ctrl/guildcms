<?php
declare(strict_types=1);


require_once __DIR__ . '/../layout/framework-helpers.php';
?>

<!-- Mobile Sidebar Button -->
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
    class="offcanvas offcanvas-start d-md-none text-bg-dark"
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
                Trademark / Copyright Notice
            </h2>
        </div>
        <div class="card-body lh-lg">
            <p>
                All text, photos, graphics, audio, video, and other materials
                on The Regs site are copyrighted and may not be published,
                broadcast, rewritten, or redistributed without prior written
                authorization.
            </p>
            <p>
                Any unauthorized use is strictly prohibited.
            </p>
            <hr>
            <p>
                All content and graphics on our site are protected by
                United States copyright laws, international treaties,
                and other applicable copyright laws.
            </p>
            <p>
                Content may not be copied without the express written
                permission of The Regs, which reserves all rights.
                Reuse of any content or graphics for any purpose without
                permission is strictly prohibited.
            </p>
            <hr>
            <p class="mb-0">
                If you have any questions regarding copyright or use of
                materials on this website, please feel free to contact us.
            </p>
        </div>
    </div>
</main>
<?php render_right_sidebar($section_key ?? null); ?>