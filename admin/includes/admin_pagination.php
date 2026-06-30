<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

function admin_get_pagination(object $request, object $db, string $count_sql): array
{
    $per_page = $request->variable('per_page', 25);
    $page_num = max(1, $request->variable('p', 1));

    $allowed_per_page = [25, 50, 100, 250];

    if (!in_array($per_page, $allowed_per_page, true)) {
        $per_page = 25;
    }

    $result = $db->sql_query($count_sql);
    $total = (int) $db->sql_fetchfield('total');
    $db->sql_freeresult($result);

    $total_pages = max(1, (int) ceil($total / $per_page));

    if ($page_num > $total_pages) {
        $page_num = $total_pages;
    }

    return [
        'per_page' => $per_page,
        'page_num' => $page_num,
        'offset' => ($page_num - 1) * $per_page,
        'total' => $total,
        'total_pages' => $total_pages,
        'allowed_per_page' => $allowed_per_page,
    ];
}

function admin_per_page_selector(string $page, string $section_key, array $pagination): void
{
    ?>
    <form method="get" action="/admin/" class="d-flex gap-2 align-items-center">
        <input type="hidden" name="page" value="<?= admin_h($page) ?>">
        <input type="hidden" name="section" value="<?= admin_h($section_key) ?>">

        <label class="small text-secondary" for="per_page_<?= admin_h($page) ?>">Per page</label>

        <select
            class="form-select form-select-sm"
            id="per_page_<?= admin_h($page) ?>"
            name="per_page"
            onchange="this.form.submit()"
        >
            <?php foreach ($pagination['allowed_per_page'] as $option): ?>
                <option value="<?= (int) $option ?>" <?= ((int) $pagination['per_page'] === (int) $option) ? 'selected' : '' ?>>
                    <?= (int) $option ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
    <?php
}

function admin_pagination_controls(string $page, string $section_key, array $pagination): void
{
    $page_num = (int) $pagination['page_num'];
    $total_pages = (int) $pagination['total_pages'];
    $per_page = (int) $pagination['per_page'];

    if ($total_pages <= 1) {
        return;
    }

    $start_page = max(1, $page_num - 3);
    $end_page = min($total_pages, $page_num + 3);
    ?>
    <div class="card-footer">
        <nav>
            <ul class="pagination pagination-sm mb-0 flex-wrap">
                <?php if ($page_num > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="/admin/?page=<?= admin_h($page) ?>&amp;section=<?= admin_h($section_key) ?>&amp;p=<?= $page_num - 1 ?>&amp;per_page=<?= $per_page ?>">Previous</a>
                    </li>
                <?php endif; ?>

                <?php if ($start_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="/admin/?page=<?= admin_h($page) ?>&amp;section=<?= admin_h($section_key) ?>&amp;p=1&amp;per_page=<?= $per_page ?>">1</a>
                    </li>
                    <?php if ($start_page > 2): ?>
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?= ($i === $page_num) ? 'active' : '' ?>">
                        <a class="page-link" href="/admin/?page=<?= admin_h($page) ?>&amp;section=<?= admin_h($section_key) ?>&amp;p=<?= $i ?>&amp;per_page=<?= $per_page ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">…</span></li>
                    <?php endif; ?>
                    <li class="page-item">
                        <a class="page-link" href="/admin/?page=<?= admin_h($page) ?>&amp;section=<?= admin_h($section_key) ?>&amp;p=<?= $total_pages ?>&amp;per_page=<?= $per_page ?>"><?= $total_pages ?></a>
                    </li>
                <?php endif; ?>

                <?php if ($page_num < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="/admin/?page=<?= admin_h($page) ?>&amp;section=<?= admin_h($section_key) ?>&amp;p=<?= $page_num + 1 ?>&amp;per_page=<?= $per_page ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php
}