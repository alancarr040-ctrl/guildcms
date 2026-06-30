<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$db = guildcms_db();
$schema_ready = $db instanceof mysqli && guildcms_schema_ready($db);

$phase_rows = [];
$log_rows = [];
$metric_rows = [];
$current_phase = null;
$next_phase = null;
$overall_progress = 0;

if ($schema_ready) {
    $phase_rows = guildcms_query_all(
        $db,
        "SELECT *
         FROM project_roadmap_phases
         WHERE COALESCE(is_public, 1) = 1
         ORDER BY sort_order ASC, id ASC"
    );

    $log_rows = guildcms_query_all(
        $db,
        "SELECT *
         FROM project_changelog_entries
         WHERE is_public = 1
         ORDER BY entry_date DESC, id DESC
         LIMIT 5"
    );

    $metric_rows = guildcms_query_all(
        $db,
        "SELECT *
         FROM project_metrics
         ORDER BY metric_group ASC, sort_order ASC, id ASC
         LIMIT 8"
    );

    $total_progress = 0;

    foreach ($phase_rows as $phase) {
        $total_progress += (int) $phase['progress'];

        if (!$current_phase && ($phase['status'] ?? '') === 'in_progress') {
            $current_phase = $phase;
        }

        if (!$next_phase && ($phase['status'] ?? '') === 'planned') {
            $next_phase = $phase;
        }
    }

    $overall_progress = count($phase_rows) > 0 ? (int) round($total_progress / count($phase_rows)) : 0;

    if (!$current_phase) {
        $current_phase = $next_phase;
    }
}

$page_title = 'Home';
$active_page = 'home';
require __DIR__ . '/includes/header.php';
?>
<section class="guild-hero">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-7">
                <div class="badge text-bg-primary mb-3">Developer Preview</div>
                <h1 class="display-3 fw-bold mb-3">
                    <span class="guild-gradient-text">The Guild CMS</span>
                </h1>
                <p class="lead guild-muted mb-4"><?= guildcms_h(GUILD_CMS_SITE_TAGLINE) ?></p>
                <p class="mb-4">
                    The Guild CMS began as the modernization of TheRegs.org and is evolving into a modular CMS for gaming communities, guilds, clans, alliances, and long-running online groups.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="/roadmap.php" class="btn btn-primary btn-lg">View Roadmap</a>
                    <a href="/timeline.php" class="btn btn-outline-light btn-lg">Development Timeline</a>
                    <a href="/vision.php" class="btn btn-outline-light btn-lg">Project Vision</a>
                    <a href="/engineering/" class="btn btn-outline-light btn-lg">Engineering Library</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="guild-card p-4">
                    <div class="small guild-muted">Current Phase</div>
                    <h2 class="h4"><?= guildcms_h($current_phase['title'] ?? 'Development Center') ?></h2>
                    <div class="mb-3"><?= guildcms_badge((string) ($current_phase['status'] ?? 'in_progress')) ?></div>
                    <div class="small guild-muted">Overall Roadmap Progress</div>
                    <?= guildcms_progress($overall_progress) ?>
                    <hr>
                    <div class="small guild-muted">Next Phase</div>
                    <div class="fw-bold"><?= guildcms_h($next_phase['title'] ?? 'Phase 4.4 - Installation & Bootstrap System') ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-4">
    <div class="container">
        <?php if (!$schema_ready): ?>
            <div class="alert alert-warning">
                Public roadmap database is not ready. Check <code>includes/config.php</code> and run the public visibility SQL.
            </div>
        <?php endif; ?>

        <div class="row g-3 mb-4">
            <?php foreach ($metric_rows as $metric): ?>
                <div class="col-md-3">
                    <div class="guild-card-soft p-3 h-100">
                        <div class="guild-muted small"><?= guildcms_h($metric['metric_label']) ?></div>
                        <div class="display-6"><?= guildcms_h($metric['metric_value']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="guild-card p-4 mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-lg-8">
                    <h2 class="h4 mb-2">Project History Before Phase 4.1</h2>
                    <p class="guild-muted mb-lg-0">Guild CMS did not begin at Phase 4.1. The public history now includes the original foundation work, CMS engine development, administration platform, and deployment preparation that led into the current security and installer phases.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="/timeline.php" class="btn btn-outline-light me-2 mb-2 mb-lg-0">Timeline</a>
                    <a href="/release-history.php" class="btn btn-outline-light">Release History</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="guild-card p-4 h-100">
                    <h2 class="h4 mb-3">Roadmap Snapshot</h2>
                    <?php foreach ($phase_rows as $phase): ?>
                        <div class="phase-line <?= guildcms_h((string) $phase['status']) ?> mb-3">
                            <div class="d-flex justify-content-between gap-3 mb-1">
                                <div>
                                    <strong><?= guildcms_h($phase['title']) ?></strong>
                                    <?= guildcms_badge((string) $phase['status']) ?>
                                </div>
                                <div class="guild-muted"><?= (int) $phase['progress'] ?>%</div>
                            </div>
                            <?= guildcms_progress((int) $phase['progress'], false) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="guild-card p-4 h-100">
                    <h2 class="h4 mb-3">Latest Updates</h2>
                    <?php if (!$log_rows): ?>
                        <p class="guild-muted mb-0">No public changelog entries yet.</p>
                    <?php endif; ?>
                    <?php foreach ($log_rows as $entry): ?>
                        <div class="mb-3 pb-3 border-bottom border-secondary">
                            <div class="guild-muted small"><?= guildcms_h($entry['entry_date']) ?> · <?= guildcms_h($entry['phase_key']) ?></div>
                            <strong><?= guildcms_h($entry['title']) ?></strong>
                            <div class="small mt-1"><?= nl2br(guildcms_h($entry['body'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                    <a href="/changelog.php" class="btn btn-outline-light btn-sm">Full Changelog</a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
