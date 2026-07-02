<?php
/**
 * Guild CMS Development Roadmap
 * Updated for Package 4.4.0-10 Phase Roadmap Realignment
 */
require_once __DIR__ . '/../includes/phase_status.inc.php';
?>

<section class="dev-center-section">
    <h1>Guild CMS Roadmap</h1>
    <p class="lead">Phase 4.4 is active as the complete Installation & Bootstrap System lifecycle for Guild CMS.</p>

    <div class="card bg-dark text-light border-secondary mb-4">
        <div class="card-header">Current Focus</div>
        <div class="card-body">
            <h2 class="h5">Phase 4.4 - Installation &amp; Bootstrap System</h2>
            <p class="mb-0">Package 4.4.0-10 realigns the roadmap so configuration generation, requirements validation, database bootstrap, database initialization, administrator creation, first-run site configuration, plugin bootstrap responsibilities, hook/event bootstrap, and site bootstrap remain in Phase 4.4 before Phase 4.5 begins.</p>
        </div>
    </div>

    <div class="card bg-dark text-light border-secondary">
        <div class="list-group list-group-flush">
            <?php foreach ($guildcms_roadmap as $item): ?>
                <div class="list-group-item bg-dark text-light border-secondary d-flex justify-content-between align-items-center">
                    <span>
                        <?php if ($item['state'] === 'complete'): ?>✓<?php elseif ($item['state'] === 'current'): ?>►<?php else: ?>•<?php endif; ?>
                        <strong>Phase <?= htmlspecialchars($item['phase']) ?></strong> &mdash; <?= htmlspecialchars($item['title']) ?>
                    </span>
                    <?= guildcms_phase_badge($item['state']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
