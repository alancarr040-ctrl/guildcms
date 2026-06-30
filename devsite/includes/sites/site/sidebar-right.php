<?php declare(strict_types=1); ?>

<!-- Right Nav Bar -->
<aside class="col-md-2">
    <div class="card bg-dark text-light border-secondary mb-3">
        <div class="card-header text-center">Random Image</div>
        <div class="card-body text-center">
            <?php
            $section_key = 'all';
            include dirname(__DIR__, 2) . '/layout/extra/random_image.php';
            ?>
        </div>
    </div>
    <div class="card bg-dark text-light border-secondary mb-3">
        <div class="card-header text-center">Login</div>
        <div class="card-body">
            <?php include dirname(__DIR__, 2) . '/layout/extra/login.php'; ?>
        </div>
    </div>
    <div class="card bg-dark text-light border-secondary mb-3">
        <div class="card-header text-center">Hosted By</div>
        <div class="card-body text-center">
            <a href="https://www.nocix.com/" target="_blank" rel="noopener">
                <img
                    src="/assets/img/nocix-hosting.webp"
                    alt="NOCIX Hosting"
                    width="150"
                    height="50"
                    class="img-fluid"
                >
            </a>
        </div>
    </div>

</aside>
<!-- End Right Nav Bar -->
