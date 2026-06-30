<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$db = guildcms_db();
$schema_ready = $db instanceof mysqli && guildcms_schema_ready($db);
$log_rows = [];

if ($schema_ready) {
    $log_rows = guildcms_query_all(
        $db,
        "SELECT *
         FROM project_changelog_entries
         WHERE is_public = 1
         ORDER BY entry_date DESC, id DESC
         LIMIT 50"
    );
}

$page_title = 'Changelog';
$active_page = 'changelog';
require __DIR__ . '/includes/header.php';
?>
<section class="py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">Public Changelog</h1>
        <p class="lead guild-muted mb-4">Release-focused public updates for The Guild CMS.</p>

        <div class="guild-card p-4 mb-4">
            <h2 class="h4">Changelog policy</h2>
            <p class="guild-muted mb-0">The changelog should stay focused on concrete release/package changes. Broader background and pre-Phase 4.1 history now live on the <a href="/timeline.php" class="link-light">Development Timeline</a> and <a href="/release-history.php" class="link-light">Release History</a> pages.</p>
        </div>

        <div class="guild-card p-4">
            <?php if (!$log_rows): ?>
                <p class="guild-muted mb-0">No public changelog entries yet.</p>
            <?php endif; ?>

            <?php foreach ($log_rows as $entry): ?>
                <div class="timeline-item mb-4">
                    <div class="timeline-dot"></div>
                    <div class="guild-muted small"><?= guildcms_h($entry['entry_date']) ?> · <?= guildcms_h($entry['phase_key']) ?></div>
                    <h2 class="h5 mb-1"><?= guildcms_h($entry['title']) ?></h2>
                    <div><?= nl2br(guildcms_h($entry['body'])) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
