<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Docs';
$active_page = 'docs';
require __DIR__ . '/includes/header.php';
?>
<section class="py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">Documentation</h1>
        <p class="lead guild-muted mb-4">Public documentation for the project roadmap, engineering standards, and release path.</p>

        <div class="guild-card p-4 mb-4">
            <div class="row g-4 align-items-center">
                <div class="col-lg-8">
                    <div class="guild-muted small text-uppercase">New in Phase 4.3</div>
                    <h2 class="h4 mb-2">Engineering Library</h2>
                    <p class="guild-muted mb-0">The Engineering Library is now the canonical public home for architecture, standards, engineering principles, governance documents, and future Architecture Decision Records.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="/engineering/" class="btn btn-primary">Open Engineering Library</a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <?php
            $cards = [
                ['Platform Architecture', 'How The Guild CMS is structured and why it is modular.', '/engineering/architecture-standards.php'],
                ['Engineering Principles', 'The engineering values that guide implementation, review, documentation, and maintenance.', '/engineering/principles.php'],
                ['Security Baseline', 'Phase 4.2 security hardening is complete. Security Standards are reserved in the Engineering Library.', '/engineering/security-standards.php'],
                ['Developer Handbook', 'Future tooling and onboarding guidance for developers working on Guild CMS core and extensions.', '/engineering/developer-handbook.php'],
            ];
            ?>
            <?php foreach ($cards as $card): ?>
                <div class="col-md-6">
                    <a href="<?= guildcms_h($card[2]) ?>" class="text-decoration-none">
                        <div class="guild-card p-4 h-100">
                            <h2 class="h4"><?= guildcms_h($card[0]) ?></h2>
                            <p class="guild-muted mb-0"><?= guildcms_h($card[1]) ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
