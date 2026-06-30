<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$page_title = 'Engineering Library';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

$publications = guildcms_engineering_publications();
$featured = guildcms_engineering_find('founders-note.php');
?>
<section class="guild-hero engineering-hero">
    <div class="container">
        <div class="guild-card p-4 p-lg-5 mb-4">
            <div class="engineering-kicker">Guild CMS</div>
            <h1 class="display-4 fw-bold mb-2">Engineering Library</h1>
            <div class="h4 guild-muted mb-4">Knowledge • Architecture • Standards</div>
            <p class="lead mb-0">The public home for Guild CMS architecture, engineering standards, governance, technical decisions, and long-term project documentation.</p>
        </div>

        <div class="row g-4 align-items-stretch mb-4">
            <div class="col-lg-8">
                <div class="guild-card p-4 h-100">
                    <h2 class="h4">Engineering is more than writing code.</h2>
                    <p class="guild-muted mb-3">The Engineering Library documents not only how Guild CMS is built, but why it is built that way. It provides a stable, public, reviewable reference for project direction, standards, and engineering decisions.</p>
                    <p class="guild-muted mb-0">Beginning with Phase 4.3, Engineering Library publications become first-class project artifacts alongside code, release packages, roadmap entries, and security reviews.</p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="guild-card p-4 h-100">
                    <div class="guild-muted small text-uppercase">Featured Publication</div>
                    <h2 class="h4 mt-2"><?= guildcms_h($featured['title'] ?? "Founder's Note") ?></h2>
                    <p class="guild-muted">An introduction to why the Engineering Library exists and how it supports long-term stewardship.</p>
                    <a href="/engineering/founders-note.php" class="btn btn-outline-light">Read Founder's Note</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="row g-4">
                    <?php foreach ($publications as $publication): ?>
                        <div class="col-md-6">
                            <a href="/engineering/<?= guildcms_h($publication['slug']) ?>" class="text-decoration-none">
                                <div class="guild-card p-4 h-100 engineering-publication-card">
                                    <div class="d-flex justify-content-between gap-2 align-items-start mb-2">
                                        <div>
                                            <div class="guild-muted small"><?= guildcms_h($publication['id']) ?></div>
                                            <div class="guild-muted small"><?= guildcms_h($publication['volume']) ?></div>
                                        </div>
                                        <?= guildcms_engineering_badge($publication['status']) ?>
                                    </div>
                                    <h3 class="h5 mb-2"><?= guildcms_h($publication['title']) ?></h3>
                                    <p class="guild-muted mb-0"><?= guildcms_h($publication['summary']) ?></p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <?php guildcms_engineering_sidebar('index'); ?>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
