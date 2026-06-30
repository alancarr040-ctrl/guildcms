<?php
declare(strict_types=1);

if (!function_exists('guildcms_public_h')) {
    function guildcms_public_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$release_rows = [
    ['version' => 'v0.1', 'title' => 'Foundation Prototype', 'status' => 'Historical', 'summary' => 'Initial PHP/Bootstrap modernization and proof-of-concept structure for a reusable guild/community CMS.'],
    ['version' => 'v0.2', 'title' => 'Section Framework', 'status' => 'Historical', 'summary' => 'Multi-section layout model for game/community areas, shared navigation, sidebars, and content wrappers.'],
    ['version' => 'v0.3', 'title' => 'Content Model Expansion', 'status' => 'Historical', 'summary' => 'Reusable content patterns for links, articles, galleries, videos, and section-specific modules.'],
    ['version' => 'v0.4', 'title' => 'Admin Foundation', 'status' => 'Historical', 'summary' => 'Admin Center foundation, authentication integration, and first content-management workflows.'],
    ['version' => 'v0.5', 'title' => 'Admin Modules', 'status' => 'Historical', 'summary' => 'Expanded admin modules for managing site links, videos, galleries, diplomacy, KOS records, world data, factions, races, and related content.'],
    ['version' => 'v0.6', 'title' => 'Development Center', 'status' => 'Historical', 'summary' => 'Introduced the Development Center as the engineering record for roadmap, backlog, changelog, sessions, security notes, architecture, and vision.'],
    ['version' => 'v0.7', 'title' => 'Navigation & Publishing', 'status' => 'Historical', 'summary' => 'Improved navigation-management workflow, publishing planning, migration tracking, and package-based delivery discipline.'],
    ['version' => 'v0.8', 'title' => 'Security Foundation', 'status' => 'Complete', 'summary' => 'Started formal Phase 4 security hardening with codebase audits, security-header planning, and public milestone tracking.'],
    ['version' => 'v0.9', 'title' => 'Engineering Foundation & Governance', 'status' => 'Complete', 'summary' => 'Completed the Engineering Library Volume I, governance framework, publication tracking, and roadmap realignment for Phase 4.3.'],
    ['version' => 'v0.10', 'title' => 'Installation Architecture', 'status' => 'Current', 'summary' => 'Opened Phase 4.4 with the installation architecture, bootstrap stage model, and installer security boundary for Guild CMS.'],
];
?>

<div class="container py-4 text-light guildcms-public-page">
    <div class="p-4 mb-4 bg-dark border border-secondary rounded-3">
        <div class="small text-uppercase text-info fw-semibold mb-2">Guild CMS Releases</div>
        <h1 class="display-6 mb-3">Release History</h1>
        <p class="lead mb-0">A high-level public summary of Guild CMS version milestones. Detailed engineering notes remain in the Development Center.</p>
    </div>

    <div class="card bg-dark border-secondary text-light">
        <div class="table-responsive">
            <table class="table table-dark table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Version</th>
                        <th scope="col">Milestone</th>
                        <th scope="col">Status</th>
                        <th scope="col">Summary</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($release_rows as $release): ?>
                        <tr>
                            <td><strong><?= guildcms_public_h($release['version']) ?></strong></td>
                            <td><?= guildcms_public_h($release['title']) ?></td>
                            <td><?= guildcms_public_h($release['status']) ?></td>
                            <td><?= guildcms_public_h($release['summary']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
