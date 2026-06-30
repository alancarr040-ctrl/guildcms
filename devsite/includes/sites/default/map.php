<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/layout/framework-helpers.php';
?>

<!-- Sidebar Toggle Button: visible only on small screens -->
<button
    class="btn btn-outline-light d-md-none mb-3"
    type="button"
    data-bs-toggle="offcanvas"
    data-bs-target="#leftSidebar"
    aria-controls="leftSidebar"
>
    ☰ Menu
</button>

<!-- Sidebar Offcanvas (mobile) -->
<div
    class="offcanvas offcanvas-start text-bg-dark d-md-none"
    tabindex="-1"
    id="leftSidebar"
    aria-labelledby="leftSidebarLabel"
>
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="leftSidebarLabel">
            Asheron's Call
        </h5>

        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas"
            aria-label="Close"
        ></button>
    </div>

    <div class="offcanvas-body">
        <?php render_sidebar('ac'); ?>
    </div>
</div>


<!-- Static Sidebar (desktop) -->
<aside class="col-md-2 d-none d-md-block sidebar-nav">
    <?php render_sidebar('ac'); ?>
</aside>


<main
    class="col-md-8 text-light"
    style="background:url('//cdn.theregs.org/images/tsw/pixel.webp') repeat;"
>

    <div class="card bg-dark border-secondary text-light my-4">

        <div class="card-header text-center">
            <h2 class="h4 mb-0">
                Map of Dereth
            </h2>
        </div>

        <div class="card-body text-center">

            <p class="text-secondary mb-4">
                Explore the lands of Dereth from Asheron's Call.
            </p>


            <div class="dereth-map-wrapper">

                <div
                    class="map-of-dereth mx-auto"
                    title="Map of Dereth"
                ></div>

            </div>

        </div>

    </div>

</main>


<?php render_right_sidebar('ac'); ?>