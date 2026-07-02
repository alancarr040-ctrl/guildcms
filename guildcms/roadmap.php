<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$db = guildcms_db();
$schema_ready = $db instanceof mysqli && guildcms_schema_ready($db);

$phase_rows = [];
$item_rows = [];
$items_by_phase = [];

if ($schema_ready) {
    $phase_rows = guildcms_query_all(
        $db,
        "SELECT *
         FROM project_roadmap_phases
         WHERE COALESCE(is_public, 1) = 1
         ORDER BY sort_order ASC, id ASC"
    );

    $item_rows = guildcms_query_all(
        $db,
        "SELECT i.*, p.phase_key
         FROM project_roadmap_items i
         INNER JOIN project_roadmap_phases p ON p.id = i.phase_id
         WHERE COALESCE(p.is_public, 1) = 1
           AND COALESCE(i.is_public, 1) = 1
         ORDER BY p.sort_order ASC, i.sort_order ASC, i.id ASC"
    );

    foreach ($item_rows as $item) {
        $items_by_phase[(int) $item['phase_id']][] = $item;
    }
}

$page_title = 'Roadmap';
$active_page = 'roadmap';
require __DIR__ . '/includes/header.php';
?>
<section class="py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">Product Roadmap</h1>
        <p class="lead guild-muted mb-4">Track the public development direction of The Guild CMS.</p>

        <div class="guild-card p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <div class="guild-muted small">Current Roadmap Status</div>
                    <h2 class="h4 mb-1">Phase 4.4 — Installation & Bootstrap System Complete</h2>
                    <p class="guild-muted mb-0">Installer Certification Milestone 1 is complete. Guild CMS Installer 4.4.0-8a is certified across five foundation platform scenarios and the project is ready to move toward Phase 4.5 Core Installation Experience.</p>
                </div>
                <div style="min-width: 240px;">
                    <div class="small guild-muted mb-1">Phase 4.4 Progress</div>
                    <?= guildcms_progress(100) ?>
                </div>
            </div>
        </div>

        <?php foreach ($phase_rows as $phase): ?>
            <div class="guild-card p-4 mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                    <div>
                        <h2 class="h4 mb-1"><?= guildcms_h($phase['title']) ?></h2>
                        <?= guildcms_badge((string) $phase['status']) ?>
                    </div>
                    <div style="min-width: 220px;"><?= guildcms_progress((int) $phase['progress']) ?></div>
                </div>

                <?php if (!empty($phase['description'])): ?>
                    <p class="guild-muted"><?= nl2br(guildcms_h($phase['description'])) ?></p>
                <?php endif; ?>

                <?php $items = $items_by_phase[(int) $phase['id']] ?? []; ?>
                <?php if ($items): ?>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Milestone</th>
                                    <th>Status</th>
                                    <th style="width: 220px;">Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td>
                                            <strong><?= guildcms_h($item['title']) ?></strong>
                                            <?php if (!empty($item['description'])): ?>
                                                <div class="small guild-muted"><?= guildcms_h($item['description']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= guildcms_badge((string) $item['status']) ?></td>
                                        <td><?= guildcms_progress((int) $item['progress']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
