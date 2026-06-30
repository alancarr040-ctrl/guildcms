<?php
declare(strict_types=1);

if (!function_exists('guildcms_public_h')) {
    function guildcms_public_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$changelog_rows = [
    [
        'version' => 'v0.14',
        'date' => '2026-06-30',
        'title' => 'Phase 4.4 Roadmap Refinement',
        'changes' => [
            'Refined the Phase 4.4 installer milestone sequence around the devsite installable product tree.',
            'Confirmed setup detection and product separation preparation as the next implementation step.',
            'Preserved Phase 4.5 for Data Normalization & Governance and Phase 4.6 for Upgrade & Migration Framework.',
        ],
    ],
    [
        'version' => 'v0.9.13',
        'date' => '2026-06-30',
        'title' => 'Phase 4.3 Roadmap Realignment Final',
        'changes' => [
            'Marked Phase 4.3 Engineering Foundation & Governance as complete.',
            'Moved installer/bootstrap milestones out of Phase 4.3 and into Phase 4.4.',
            'Synchronized Development Center and public roadmap expectations before Phase 4.4 begins.',
        ],
    ],
    [
        'version' => 'v0.9.4',
        'date' => '2026-06-30',
        'title' => 'GCMS-ENG-001 Published',
        'changes' => [
            'Published GCMS-ENG-001 — The Guild CMS Constitution.',
            'Marked Volume I of the Engineering Library as Published.',
            'Added a formal publication metadata header, revision history, table of contents, numbered sections, glossary, references, and publication certification.',
            'Synchronized Development Center publication tracking with the public Engineering Library.',
        ],
    ],
    [
        'version' => 'v0.9',
        'date' => '2026-06-29',
        'title' => 'Phase 4.2 Complete / Phase 4.3 Active',
        'changes' => [
            'Completed Phase 4.2 Security Hardening.',
            'Updated the Development Center to v0.9.',
            'Marked Phase 4.3 Engineering Foundation & Governance as the active engineering phase.',
            'Added public project history, release history, and roadmap context.',
        ],
    ],
    [
        'version' => 'v0.8',
        'date' => '2026-06-28',
        'title' => 'Security Hardening Foundation',
        'changes' => [
            'Began formal Phase 4 security-hardening milestone tracking.',
            'Started public security-progress reporting.',
            'Added codebase audit and remediation tracking to the engineering workflow.',
        ],
    ],
    [
        'version' => 'v0.7 and earlier',
        'date' => 'Historical',
        'title' => 'Foundation, CMS Engine, Admin Center, and Navigation Work',
        'changes' => [
            'Established the core Guild CMS platform foundation.',
            'Built reusable public content modules and administration tools.',
            'Introduced Development Center tracking and package-based milestone discipline.',
        ],
    ],
];
?>

<div class="container py-4 text-light guildcms-public-page">
    <div class="p-4 mb-4 bg-dark border border-secondary rounded-3">
        <div class="small text-uppercase text-info fw-semibold mb-2">Guild CMS Changes</div>
        <h1 class="display-6 mb-3">Changelog</h1>
        <p class="lead mb-0">Release-focused changes only. Detailed design notes, audits, and work sessions belong in the Development Center.</p>
    </div>

    <div class="row g-3">
        <?php foreach ($changelog_rows as $entry): ?>
            <div class="col-12">
                <div class="card bg-dark border-secondary text-light">
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                            <span class="badge bg-primary"><?= guildcms_public_h($entry['version']) ?></span>
                            <span class="text-secondary"><?= guildcms_public_h($entry['date']) ?></span>
                        </div>
                        <h2 class="h4"><?= guildcms_public_h($entry['title']) ?></h2>
                        <ul class="mb-0">
                            <?php foreach ($entry['changes'] as $change): ?>
                                <li><?= guildcms_public_h($change) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
