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

<!-- Sidebar Offcanvas -->
<div
    class="offcanvas offcanvas-start text-bg-dark d-md-none"
    tabindex="-1"
    id="leftSidebar"
>
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">
            Asheron's Call
        </h5>
        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas"
        ></button>
    </div>
    <div class="offcanvas-body">
        <?php render_sidebar('ac'); ?>
    </div>
</div>

<!-- Static Sidebar -->
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
                Asheron's Call
            </h2>
        </div>
        <div class="card-body">
            <!-- Mansion Image -->
            <div class="text-center mb-4">
                <a
                    href="#"
                    data-bs-toggle="modal"
                    data-bs-target="#imgModal"
                    data-img="//cdn.theregs.org/albums/userpics/10002/Image1~0.webp"
                    data-title="The Regs Mansion"
                >
                    <img
                        src="//cdn.theregs.org/albums/userpics/10002/normal_Image1~0.webp"
                        class="img-fluid rounded border ac-home-image"
                        alt="The Regs Mansion"
                    >
                </a>
                <div class="mt-2 fw-bold">
                    The Regs Mansion
                </div>
            </div>
            <hr>
            <!-- Forum News -->
            <div id="home_content">
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
    </div>
</main>

<!-- Image Modal -->
<div
    class="modal fade"
    id="imgModal"
    tabindex="-1"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header">
                <h5 class="modal-title">
                    Image Preview
                </h5>
                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                ></button>
            </div>
            <div class="modal-body text-center">
                <img
                    id="modalImage"
                    src=""
                    alt=""
                    class="img-fluid rounded"
                >
            </div>
        </div>
    </div>
</div>

<?php render_right_sidebar('ac'); ?>