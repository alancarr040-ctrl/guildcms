<?php
declare(strict_types=1);


require_once dirname(__DIR__, 2) . '/layout/framework-helpers.php';
global $DB_HOST, $DB_NAME, $DB_PASS, $DB_USER;

if (!function_exists('ac_diplomacy_h')) {
    function ac_diplomacy_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

function ac_diplomacy_status_label(string $status): string
{
    return match (strtolower($status)) {
        'rhino', 'allied' => 'Allied',
        'friend' => 'Friendly',
        'neutral' => 'Neutral',
        'wary' => 'Wary',
        'war' => 'War / RPK',
        default => ucfirst($status),
    };
}

$groups = [
    'rhino' => [
        'title' => 'Allies',
        'note' => '',
    ],
    'allied' => [
        'title' => 'Allied Guilds',
        'note' => 'We treat allies as if they were our own members. If you see them in trouble, help them out no matter what. Level with them. Raid with them. Check out their website.',
    ],
    'friend' => [
        'title' => 'Friendly Guilds',
        'note' => 'If you see them in trouble, help them out unless they are fighting with another friendly guild. If they are attacking an allied guild, always help the allied guild member and inform leadership immediately.',
    ],
    'neutral' => [
        'title' => 'Neutral Guilds',
        'note' => 'Neutral guilds are guilds that we have had good relations with or are considered friendly by trusted guilds. Help them if you see them in trouble or fighting with known PK guilds.',
    ],
    'wary' => [
        'title' => 'Wary Guilds',
        'note' => 'Guilds on wary status are those that we have not had good experiences with, have PK’d our members, or are considered possibly hostile by trusted guilds. Do not attack unless attacked. Keep your distance.',
    ],
    'war' => [
        'title' => 'RPK / War Guilds',
        'note' => 'PK status denotes these guilds as being RPK. Some individuals have a sense of honor, some are borderline trash. They will all try to kill you. End of story.',
    ],
];

$entries = [];

foreach (array_keys($groups) as $status) {
    $entries[$status] = [];
}

$link = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($link) {
    mysqli_set_charset($link, 'utf8mb4');

    $sql = "
        SELECT name, tag, monarch, status, url, type
        FROM ac_diplomacy
        WHERE is_active = 1
        ORDER BY sort_order ASC, name ASC
    ";

    $result = mysqli_query($link, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $status = strtolower((string) $row['status']);

            if (!isset($entries[$status])) {
                $entries[$status] = [];
                $groups[$status] = [
                    'title' => ac_diplomacy_status_label($status),
                    'note' => '',
                ];
            }

            $entries[$status][] = $row;
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
        <h5 class="offcanvas-title" id="leftSidebarLabel">Asheron's Call</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        <?php render_sidebar('ac'); ?>
    </div>
</div>

<aside class="col-md-2 d-none d-md-block sidebar-nav">
    <?php render_sidebar('ac'); ?>
</aside>

<main class="col-md-8 text-light" style="background:url('//cdn.theregs.org/images/tsw/pixel.webp') repeat;">
    <div class="card bg-dark border-secondary text-light my-4">
        <div class="card-header text-center">
            <h2 class="h4 mb-0">Diplomacy</h2>
        </div>

        <div class="card-body">
            <p class="text-center">
                This is our Diplomacy Page. Check this site regularly for changes in guild status.
            </p>

            <hr>

            <?php foreach ($groups as $status_key => $group): ?>
                <?php if (empty($entries[$status_key])): ?>
                    <?php continue; ?>
                <?php endif; ?>

                <section class="mb-4">
                    <h3 class="h5 text-center text-decoration-underline mb-3">
                        <?= ac_diplomacy_h($group['title']) ?>
                    </h3>

                    <?php if ($status_key === 'rhino'): ?>
                        <div class="text-center mb-3">
                            <a href="http://rhinoparty.simplejustice.net/" target="_blank" rel="noopener">
                                The Darktide Rhino Party
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Guild Name</th>
                                    <th>Tag</th>
                                    <th>Monarch</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($entries[$status_key] as $entry): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($entry['url'])): ?>
                                                <a
                                                    href="<?= ac_diplomacy_h($entry['url']) ?>"
                                                    target="_blank"
                                                    rel="noopener"
                                                >
                                                    <?= ac_diplomacy_h($entry['name']) ?>
                                                </a>
                                            <?php else: ?>
                                                <?= ac_diplomacy_h($entry['name']) ?>
                                            <?php endif; ?>
                                        </td>

                                        <td><?= ac_diplomacy_h($entry['tag']) ?></td>
                                        <td><?= ac_diplomacy_h($entry['monarch']) ?></td>
                                        <td>
											<span class="badge diplomacy-status-<?= ac_diplomacy_h($entry['status']) ?>">
												<?= ac_diplomacy_h(ac_diplomacy_status_label((string)$entry['status'])) ?>
											</span>
										</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

<?php if (!empty($group['note'])): ?>

    <div class="card bg-dark border-secondary mt-2 mb-4">
        <div class="card-body diplomacy-note">
            <?= ac_diplomacy_h($group['note']) ?>
        </div>
    </div>

<?php endif; ?>
                </section>
            <?php endforeach; ?>

            <?php if (!array_filter($entries)): ?>
                <div class="alert alert-secondary text-center mb-0">
                    No diplomacy entries are currently available.
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php render_right_sidebar('ac'); ?>