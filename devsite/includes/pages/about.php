<?php
declare(strict_types=1);


require_once __DIR__ . '/../layout/framework-helpers.php';
global $DB_HOST, $DB_NAME, $DB_PASS, $DB_USER;

if (!function_exists('about_h')) {
    function about_h(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$article = [
    'title' => 'About Us',
    'content' => 'No content available.',
    'created' => '',
    'author' => '',
];

$link = mysqli_connect(
    $DB_HOST,
    $DB_USER,
    $DB_PASS,
    $DB_NAME
);

if ($link) {
    mysqli_set_charset($link, 'utf8mb4');
    $sql = "
        SELECT *
        FROM articles
        WHERE type = '13'
        LIMIT 1
    ";
    $result = mysqli_query($link, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            /*
             * Current articles table:
             *
             * 0 id
             * 1 type
             * 2 title/name
             * 3 text
             * 4 date
             * 5 author
             */
            $values = array_values($row);
            $article = [
                'title'   => $values[2] ?? '',
                'content' => $values[3] ?? '',
                'created' => $values[4] ?? '',
                'author'  => $values[5] ?? '',
            ];
        }
        mysqli_free_result($result);
    }
    mysqli_close($link);
}
?>


<?php
        /*
         * Mobile menu support.
         * Desktop keeps the normal left sidebar.
         * Mobile uses Bootstrap offcanvas so these pages match the article/home layout.
         */
        ?>
        <button
            class="btn btn-outline-light d-md-none mb-3 w-100"
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
                <h5 class="offcanvas-title" id="leftSidebarLabel">The Regs</h5>
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
        <div class="card-header">
            <h3 class="mb-0 text-center">
                <?= about_h($article['title']) ?>
            </h3>
        </div>
        <div class="card-body">
            <?php if ($article['created'] || $article['author']): ?>
                <div class="small text-secondary mb-3">
                    <?php if ($article['created']): ?>
                        <div>
                            <strong>Date:</strong>
                            <?= about_h($article['created']) ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($article['author']): ?>
                        <div>
                            <strong>Author:</strong>
                            <?= about_h($article['author']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <hr>
            <?php endif; ?>
            <div class="lh-lg">
                <?= nl2br($article['content']) ?>
            </div>
        </div>
    </div>
</main>
<?php render_right_sidebar($section_key ?? null); ?>