<?php
declare(strict_types=1);

$section_key = 'ac';
?>
<aside class="col-md-2">
    <div class="card bg-dark text-white mb-3">
        <div class="card-header1">Asheron's Call Video</div>
        <?php include dirname(__DIR__, 2) . '/layout/extra/video.php'; ?>
    </div>

    <div class="card bg-dark text-white mb-3">
        <div class="card-header1">Random Image</div>
        <?php include dirname(__DIR__, 2) . '/layout/extra/random_image.php'; ?>
    </div>

    <div class="card bg-dark text-white mb-3">
        <div class="card-header1">Login</div>
        <?php include dirname(__DIR__, 2) . '/layout/extra/login.php'; ?>
    </div>

    <div class="card bg-dark text-light text-center mb-3">
        <div class="card-body">
            <div class="card-header1">
                <p class="mb-0">Hosted by:</p>
            </div>
            <a href="https://www.nocix.com/">
                <img src="/assets/img/nocix-hosting.webp" alt="NOCIX Hosting" class="img-fluid mt-2">
            </a>
        </div>
    </div>
</aside>
