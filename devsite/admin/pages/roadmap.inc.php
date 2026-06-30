<?php
/**
 * Guild CMS Development Roadmap
 * Updated for Package 4.3.0-1
 */
require_once __DIR__ . '/../includes/phase_status.inc.php';
?>

<section class="dev-center-section">
    <h1>Guild CMS Roadmap</h1>
    <p class="lead">The official development roadmap has been revised to introduce Phase 4.3 as Engineering Foundation &amp; Governance.</p>

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
