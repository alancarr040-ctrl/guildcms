<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$db = guildcms_db();
$schema_ready = $db instanceof mysqli && guildcms_schema_ready($db);
$vision_rows = [];

if ($schema_ready) {
    $vision_rows = guildcms_query_all(
        $db,
        "SELECT *
         FROM project_vision_notes
         WHERE status = 'active'
           AND COALESCE(is_public, 1) = 1
         ORDER BY sort_order ASC, id ASC"
    );
}

$page_title = 'Vision';
$active_page = 'vision';
require __DIR__ . '/includes/header.php';
?>
<section class="py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">Project Vision</h1>
        <p class="lead guild-muted mb-4">The principles and long-term goals behind The Guild CMS.</p>

        <div class="guild-card p-4 mb-4">
            <div class="guild-muted small">Engineering Principle</div>
            <h2 class="h4">Security-First Development</h2>
            <p>The Guild CMS now treats security review as a phase gate. Each development phase must conclude with documented review, remediation, and Development Center updates before being marked complete.</p>
            <div class="row g-3">
                <div class="col-md-6"><div class="guild-mini-check">Application-layer security belongs to The Guild CMS.</div></div>
                <div class="col-md-6"><div class="guild-mini-check">Infrastructure security remains the server's responsibility.</div></div>
                <div class="col-md-6"><div class="guild-mini-check">Upload validation should be centralized.</div></div>
                <div class="col-md-6"><div class="guild-mini-check">Legacy cleanup is part of every audit.</div></div>
            </div>
        </div>

        <?php foreach ($vision_rows as $note): ?>
            <div class="guild-card p-4 mb-4">
                <div class="guild-muted small"><?= guildcms_h($note['category']) ?></div>
                <h2 class="h4"><?= guildcms_h($note['title']) ?></h2>
                <div><?= nl2br(guildcms_h($note['body'])) ?></div>
            </div>
        <?php endforeach; ?>

        <div class="guild-card p-4">
            <h2 class="h4">Flagship Installation</h2>
            <p>TheRegs.org is the flagship live installation of The Guild CMS and the project that inspired the platform.</p>
            <a href="<?= guildcms_h(GUILD_CMS_FLAGSHIP_SITE) ?>" class="btn btn-outline-light">Visit TheRegs.org</a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
