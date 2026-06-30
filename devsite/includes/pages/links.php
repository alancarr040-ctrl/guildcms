<?php
declare(strict_types=1);


require_once __DIR__ . '/../layout/framework-helpers.php';
global $DB_HOST, $DB_NAME, $DB_PASS, $DB_USER;

if (!function_exists('site_links_h')) {
    function site_links_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$section_key = $section_key ?? theregs_get_section();
$site_name     = $site_name ?? 'The Regs';
$section_titles = [
    'site' => 'Information / Other Sites',
    'ac'   => "Asheron's Call Sites",
    'ao'   => 'Anarchy Online Sites',
    'tsw'  => 'The Secret World Sites',
    'wow'  => 'World of Warcraft Sites',
    'cod'  => 'Call of Duty Sites',
    'coh'  => 'City of Heroes Sites',
    'eve'  => 'Eve Online Sites',
    'fo76' => 'Fallout 76 Sites',
];

$page_title = $section_titles[$section_key] ?? ($site_name . ' Links');

$links = [];

$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($con) {
    mysqli_set_charset($con, 'utf8mb4');

    $stmt = mysqli_prepare(
        $con,
        "
        SELECT section_key, title, url, description
        FROM site_links_new
        WHERE is_active = 1
        AND section_key = ?
        ORDER BY sort_order ASC, title ASC
        "
    );

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $section_key);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $links[] = $row;
            }

            mysqli_free_result($result);
        }

        mysqli_stmt_close($stmt);
    }

    mysqli_close($con);
}
?>

<div class="container-fluid">
    <div class="row">
        <button
            class="btn btn-outline-light d-md-none mb-3"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#leftSidebar"
            aria-controls="leftSidebar"
        >
            ☰ Menu
        </button>

        <div
            class="offcanvas offcanvas-start text-bg-dark d-md-none"
            tabindex="-1"
            id="leftSidebar"
            aria-labelledby="leftSidebarLabel"
        >
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="leftSidebarLabel">
                    <?= site_links_h($site_name) ?>
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
                        <?= site_links_h($page_title) ?>
                    </h2>
                </div>

                <div class="card-body">
                    <?php if (!$links): ?>
                        <div class="alert alert-secondary text-center mb-0">
                            No links are currently available for <?= site_links_h($site_name) ?>.
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($links as $site_link): ?>
                                <a
                                    class="list-group-item list-group-item-action bg-dark text-light border-secondary"
                                    href="<?= site_links_h($site_link['url']) ?>"
                                    target="_blank"
                                    rel="noopener"
                                >
                                    <strong><?= site_links_h($site_link['title']) ?></strong>

                                    <?php if (!empty($site_link['description'])): ?>
                                        <div class="small text-secondary">
                                            <?= site_links_h($site_link['description']) ?>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <?php render_right_sidebar($section_key ?? null); ?>
    </div>
</div>