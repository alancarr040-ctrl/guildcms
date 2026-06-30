<?php
declare(strict_types=1);

require_once __DIR__ . '/../layout/framework-helpers.php';

$error_code = $error_code ?? 404;
$error_title = $error_title ?? 'File Not Found';
$error_message = $error_message ?? 'Your file cannot be found!';
$error_detail = $error_detail ?? "It's all a conspiracy.<br>We're hiding everything from you...<br>Or maybe the file doesn't actually exist.<br>hmmm....";
?>

<aside class="col-md-2 d-none d-md-block sidebar-nav">
    <?php render_sidebar($section_key ?? null); ?>
</aside>

<main class="col-md-8 text-light text-center">
    <div class="card bg-dark border-secondary text-light my-4">
        <div class="card-header">
            <h2 class="h4 mb-0">Error <?= (int) $error_code ?> - <?= htmlspecialchars($error_title) ?></h2>
        </div>

        <div class="card-body">
            <img
                src="//cdn.theregs.org/images/404.webp"
                alt="Error <?= (int) $error_code ?>"
                class="img-fluid rounded mb-3"
                style="max-width:260px;"
            >

            <p><strong><?= htmlspecialchars($error_message) ?></strong></p>
            <p class="mb-0"><?= $error_detail ?></p>
        </div>
    </div>
</main>

<?php render_right_sidebar($section_key ?? null); ?>