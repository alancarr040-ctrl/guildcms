<?php
/**
 * Guild CMS Development Roadmap
 * Updated for Package 4.4.0-1 Installation Architecture
 */
require_once __DIR__ . '/../includes/phase_status.inc.php';
?>

<section class="dev-center-section">
    <h1>Guild CMS Roadmap</h1>
    <p class="lead">Phase 4.4 is now active and establishes the installation architecture and bootstrap framework for the Guild CMS product.</p>

    <div class="card bg-dark text-light border-secondary mb-4">
        <div class="card-header">Current Focus</div>
        <div class="card-body">
            <h2 class="h5">Phase 4.4 - Installation &amp; Bootstrap System</h2>
            <p class="mb-0">Package 4.4.0-1 defines the installer boundary, bootstrap stages, security rules, and package sequence before executable installer screens and database bootstrap actions are introduced.</p>
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
