<?php
declare(strict_types=1);


require_once dirname(__DIR__, 2) . '/layout/framework-helpers.php';
global $DB_HOST, $DB_NAME, $DB_PASS, $DB_USER;

if (!function_exists('ac_kos_h')) {
    function ac_kos_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$entries = [];

$link = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($link) {
    mysqli_set_charset($link, 'utf8mb4');

    $sql = "
        SELECT
            name,
            comment,
            submitter_name,
            created_at
        FROM ac_kos
        WHERE is_active = 1
        ORDER BY created_at DESC, id DESC
    ";

    $result = mysqli_query($link, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $entries[] = $row;
        }

        mysqli_free_result($result);
    }

    mysqli_close($link);
}
?>

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
                Kill on Sight
            </h2>
        </div>

        <div class="card-body">
            <p class="text-center text-secondary">
                Players listed here are considered Kill on Sight by The Regs.
            </p>

            <?php if (!$entries): ?>
                <div class="alert alert-secondary text-center mb-0">
                    No KoS entries are currently listed.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 18%;">Name</th>
                                <th>Reason</th>
                                <th style="width: 15%;">Added By</th>
                                <th style="width: 16%;">Date</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($entries as $entry): ?>
                                <tr>
                                    <td>
                                        <strong><?= ac_kos_h($entry['name']) ?></strong>
                                    </td>
                                    <td>
                                        <?= nl2br(ac_kos_h($entry['comment'])) ?>
                                    </td>
                                    <td>
                                        <?= ac_kos_h($entry['submitter_name']) ?>
                                    </td>
                                    <td>
                                        <?= ac_kos_h($entry['created_at']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php render_right_sidebar('ac'); ?>