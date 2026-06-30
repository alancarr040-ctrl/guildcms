<?php
declare(strict_types=1);

if (!function_exists('guildcms_public_h')) {
    function guildcms_public_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$timeline_rows = [
    [
        'label' => 'Project Start',
        'status' => 'Complete',
        'badge' => 'bg-success',
        'title' => 'Guild CMS Vision & Architecture',
        'summary' => 'The project began as a modernization effort for a long-running gaming community site and grew into a reusable CMS designed around guilds, gaming communities, modular content, and long-term maintainability.',
        'items' => [
            'Defined the Guild CMS vision and platform direction.',
            'Established the multi-section site model used by TheRegs.org.',
            'Set the long-term goal of separating reusable CMS features from site-specific content.',
        ],
    ],
    [
        'label' => 'Phase 1',
        'status' => 'Complete',
        'badge' => 'bg-success',
        'title' => 'Core Foundation',
        'summary' => 'The foundation phase established the shared PHP layout, routing pattern, section-aware structure, Bootstrap-based presentation, and compatibility with the existing phpBB-backed community site.',
        'items' => [
            'Created the shared layout framework and site-section structure.',
            'Modernized core public pages into PHP-driven templates.',
            'Preserved compatibility with existing content and legacy community areas.',
        ],
    ],
    [
        'label' => 'Phase 2',
        'status' => 'Complete',
        'badge' => 'bg-success',
        'title' => 'CMS Content Engine',
        'summary' => 'Phase 2 focused on turning the site rebuild into an actual content platform, with reusable page patterns, database-backed content areas, and section-specific content handling.',
        'items' => [
            'Added reusable public content pages for links, articles, galleries, and section modules.',
            'Improved database-backed content rendering.',
            'Started formalizing the reusable Guild CMS content model.',
        ],
    ],
    [
        'label' => 'Phase 3',
        'status' => 'Complete',
        'badge' => 'bg-success',
        'title' => 'Administration Platform',
        'summary' => 'Phase 3 introduced the administrative foundation needed to manage Guild CMS content without editing files directly.',
        'items' => [
            'Built the Admin Center foundation.',
            'Added managers for links, videos, gallery content, world data, factions, races, diplomacy, and KOS-style records.',
            'Introduced audit logging, navigation publishing work, and admin workflow improvements.',
        ],
    ],
    [
        'label' => 'Phase 3B',
        'status' => 'Complete',
        'badge' => 'bg-success',
        'title' => 'Navigation & Publishing Improvements',
        'summary' => 'Phase 3B improved navigation management, publishing workflows, rollback planning, and public/admin synchronization.',
        'items' => [
            'Expanded Navigation Manager functionality.',
            'Added publishing and recovery workflow planning.',
            'Prepared the system for stronger package-based milestone delivery.',
        ],
    ],
    [
        'label' => 'Phase 4.1',
        'status' => 'Complete',
        'badge' => 'bg-success',
        'title' => 'Security Header Foundation',
        'summary' => 'Phase 4.1 began the security-hardening milestone by moving toward centralized security headers and a formal codebase audit process.',
        'items' => [
            'Audited direct superglobal usage and legacy include patterns.',
            'Prepared centralized security-header handling.',
            'Started recording security work as a formal engineering phase.',
        ],
    ],
    [
        'label' => 'Phase 4.2',
        'status' => 'Complete',
        'badge' => 'bg-success',
        'title' => 'Security Hardening',
        'summary' => 'Phase 4.2 completed a major security pass covering headers, CSP ownership, CSRF checks, upload handling, filesystem permissions, and Development Center v0.9.',
        'items' => [
            'Centralized application-layer security headers.',
            'Completed CSP inventory and Report-Only rollout.',
            'Verified cookie/session ownership under the current phpBB-backed installation.',
            'Completed CSRF review for known state-changing forms.',
            'Hardened gallery uploads and corrected legacy filesystem permissions.',
        ],
    ],
    [
        'label' => 'Phase 4.3',
        'status' => 'Complete',
        'badge' => 'bg-success',
        'title' => 'Engineering Foundation & Governance',
        'summary' => 'Phase 4.3 establishes the Engineering Library, governance model, publication framework, and long-term documentation discipline for Guild CMS.',
        'items' => [
            'Realigned the roadmap around the Engineering Foundation & Governance phase.',
            'Established the Engineering Library as the public home for engineering publications.',
            'Added Development Center publication tracking that links to canonical public documents.',
            'Published GCMS-ENG-001 — The Guild CMS Constitution as the first official constitution publication.',
        ],
    ],

    [
        'label' => 'Phase 4.4',
        'status' => 'Active',
        'badge' => 'bg-info text-dark',
        'title' => 'Installation & Bootstrap System',
        'summary' => 'Phase 4.4 begins with an architecture-first installer package that defines the Guild CMS installation boundary, bootstrap stages, and security model.',
        'items' => [
            'Defined the installer as a Guild CMS product installer, not an installer for the public information site.',
            'Established the bootstrap stage model: preflight, environment validation, configuration, database, site identity, authentication, installer lock, and post-install review.',
            'Synchronized roadmap metadata so Phase 4.3 is complete and Phase 4.4 is active.',
        ],
    ],
];
?>

<div class="container py-4 text-light guildcms-public-page">
    <div class="p-4 mb-4 bg-dark border border-secondary rounded-3">
        <div class="small text-uppercase text-info fw-semibold mb-2">Guild CMS Development</div>
        <h1 class="display-6 mb-3">Development Timeline</h1>
        <p class="lead mb-0">A public overview of the engineering path from the original modernization work through the current Guild CMS engineering governance milestone.</p>
    </div>

    <div class="row g-3">
        <?php foreach ($timeline_rows as $row): ?>
            <div class="col-12">
                <div class="card bg-dark border-secondary text-light shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                            <span class="badge <?= guildcms_public_h($row['badge']) ?>"><?= guildcms_public_h($row['status']) ?></span>
                            <span class="text-secondary"><?= guildcms_public_h($row['label']) ?></span>
                        </div>
                        <h2 class="h4 mb-2"><?= guildcms_public_h($row['title']) ?></h2>
                        <p class="mb-3"><?= guildcms_public_h($row['summary']) ?></p>
                        <ul class="mb-0">
                            <?php foreach ($row['items'] as $item): ?>
                                <li><?= guildcms_public_h($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
