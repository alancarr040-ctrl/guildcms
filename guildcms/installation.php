<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Installation';
$active_page = 'installation';
require __DIR__ . '/includes/header.php';
?>
<section class="py-5">
    <div class="container">
        <div class="guild-card p-4 mb-4">
            <div class="small text-uppercase text-info fw-semibold mb-2">Phase 4.4 · Package 4.4.0-5</div>
            <h1 class="display-5 fw-bold mb-3">System Readiness Check</h1>
            <p class="lead guild-muted mb-0">Guild CMS now performs the first real installer validation step inside the installable <strong>devsite</strong> product tree: a clear System Readiness check that separates required server capabilities from recommended improvements.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="guild-card p-4 h-100">
                    <h2 class="h4">Installation is an introduction</h2>
                    <p class="guild-muted">The Guild CMS installer is designed to explain before it asks. A new site should not fail with a PHP error because configuration is missing. It should explain what is missing, why it matters, and how to continue.</p>
                    <p class="guild-muted mb-0">Package 4.4.0-5 makes the System Readiness and Recommended Features steps testable. Required checks explain what blocks installation and how to correct it; recommended checks explain helpful improvements without preventing a valid install.</p>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="guild-card p-4 h-100">
                    <h2 class="h4">Installer experience goals</h2>
                    <ul class="guild-muted mb-0">
                        <li>Educational</li>
                        <li>Professional</li>
                        <li>Modern</li>
                        <li>Accessible</li>
                        <li>Safe to go back, save, cancel, refresh, and resume</li>
                        <li>Required checks block only when Guild CMS cannot run</li>
                        <li>Recommended checks educate without stopping installation</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="guild-card p-4 mt-4">
            <h2 class="h4">Environment roles</h2>
            <p class="guild-muted mb-0"><strong>Development Center</strong> tracks engineering progress, <strong>guildcms</strong> documents the public project, and <strong>devsite</strong> is the installable Guild CMS product tree. The executable installer belongs only to the installable product. The public site documents what the installer does and why each step exists.</p>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
