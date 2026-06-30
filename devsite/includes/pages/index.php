<?php
declare(strict_types=1);

require_once __DIR__ . '/../layout/framework-helpers.php';
?>

<!-- Sidebar Toggle Button: mobile -->
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



<!-- Desktop Sidebar -->
<aside class="col-md-2 d-none d-md-block sidebar-nav">

    <?php render_sidebar('ac'); ?>

</aside>




<main
    class="col-md-8 text-light"
    style="background:url('//cdn.theregs.org/images/tsw/pixel.webp') repeat;"
>


    <!-- Banner -->

    <div class="text-center my-4">

        <img
            src="//cdn.theregs.org/images/ac/banner.webp"
            alt="Asheron's Call"
            class="img-fluid rounded shadow"
        >

    </div>




    <!-- Welcome -->


    <div class="card bg-dark border-secondary text-light mb-4">


        <div class="card-header text-center">

            <h2 class="h4 mb-0">
                Asheron's Call
            </h2>

        </div>


        <div class="card-body lh-lg">


            <p>
                Welcome to The Darktide Regulators Asheron's Call section.
            </p>


            <p>
                Here you will find guild information, diplomacy,
                KOS listings, screenshots, videos, and memories from
                the world of Darktide.
            </p>


        </div>


    </div>




    <!-- News -->


    <div class="card bg-dark border-secondary text-light mb-4">


        <div class="card-header text-center">

            <h3 class="h5 mb-0">
                Latest News
            </h3>

        </div>


        <div class="card-body">


            <?php

            /*
             * Existing phpBB news bridge
             */

            $news_config =
                '/home/theregs/public_html/forums/mods/news/topic_config.php';


            $news_bottom =
                '/home/theregs/public_html/forums/mods/news/topic_bottom.php';



            if (is_file($news_config)) {

                include $news_config;

            }



            if (is_file($news_bottom)) {

                include $news_bottom;

            } else {


                echo '
                <div class="alert alert-secondary text-center mb-0">
                    No news currently available.
                </div>';

            }

            ?>


        </div>


    </div>


</main>



<?php render_right_sidebar('ac'); ?>