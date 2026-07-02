<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Development Timeline';
$active_page = 'timeline';
require __DIR__ . '/includes/header.php';

$timeline = [
    [
        'label' => 'Project Start',
        'status' => 'complete',
        'title' => 'Guild CMS Vision and Platform Direction',
        'body' => 'The project began as a modernization effort for TheRegs.org and evolved into a broader modular CMS concept for guilds, clans, alliances, and long-running gaming communities.',
        'points' => [
            'Defined the purpose of Guild CMS as a community-first platform.',
            'Established TheRegs.org as the flagship live installation.',
            'Set the long-term direction around reusable modules, public pages, administration tools, and project documentation.',
        ],
    ],
    [
        'label' => 'Phase 1',
        'status' => 'complete',
        'title' => 'Core Foundation',
        'body' => 'The first phase focused on the structural foundation needed to support a modular PHP-based CMS and a modernized public site.',
        'points' => [
            'Created the shared PHP page structure and reusable include pattern.',
            'Established section-aware layout conventions for public site areas.',
            'Started modern Bootstrap-based presentation work.',
            'Began separating content, layout, configuration, and section behavior.',
        ],
    ],
    [
        'label' => 'Phase 2',
        'status' => 'complete',
        'title' => 'CMS Engine and Content Model',
        'body' => 'The second phase expanded the site from static modernization into a CMS-style content platform with reusable database-driven areas.',
        'points' => [
            'Introduced database-backed public content areas.',
            'Developed shared handling for links, videos, gallery content, articles, and section-specific modules.',
            'Improved compatibility with the existing TheRegs.org content and phpBB-backed login environment.',
            'Prepared the platform for administrative management instead of direct file-only updates.',
        ],
    ],
    [
        'label' => 'Phase 3',
        'status' => 'complete',
        'title' => 'Administration Platform and Module Management',
        'body' => 'The third phase focused on giving Guild CMS a stronger administrative backbone and practical management tools.',
        'points' => [
            'Built the Administration Center foundation.',
            'Added management screens for links, videos, gallery records, diplomacy, KOS records, and WoW content areas.',
            'Added audit-log and settings direction for safer administrative changes.',
            'Established the Development Center as the engineering record for roadmap, changelog, notes, and implementation tracking.',
        ],
    ],
    [
        'label' => 'Phase 4.0',
        'status' => 'complete',
        'title' => 'Public Project Site and Release Preparation',
        'body' => 'Before Phase 4.1, the project began gaining a dedicated public-facing Guild CMS site so progress could be presented outside the internal Development Center.',
        'points' => [
            'Created the public Guild CMS site shell.',
            'Connected the public site to selected Development Center data.',
            'Prepared public roadmap, vision, documentation, and changelog pages.',
            'Started separating public project communication from internal engineering records.',
        ],
    ],
    [
        'label' => 'Phase 4.1',
        'status' => 'complete',
        'title' => 'Public Visibility and Security Preparation',
        'body' => 'Phase 4.1 brought the public Guild CMS project site online as a more complete project presence and prepared the codebase for deeper hardening.',
        'points' => [
            'Published public roadmap and project visibility pages.',
            'Aligned public messaging with the Development Center roadmap.',
            'Prepared the project for the formal Phase 4.2 security hardening pass.',
        ],
    ],
    [
        'label' => 'Phase 4.2',
        'status' => 'complete',
        'title' => 'Security Hardening',
        'body' => 'Phase 4.2 focused on production-facing hardening and browser-level security posture.',
        'points' => [
            'Completed security header and policy review.',
            'Improved hardening documentation and verification flow.',
            'Updated the Development Center to v0.9.',
            'Prepared the project to move into installer and bootstrap work.',
        ],
    ],
    [
        'label' => 'Phase 4.3',
        'status' => 'complete',
        'title' => 'Engineering Foundation & Governance',
        'body' => 'The completed phase established the public engineering foundation for Guild CMS, including governance, standards, and the Engineering Library publication framework.',
        'points' => [
            'Realigned the roadmap after completion of Phase 4.2 security hardening.',
            'Introduced the Engineering Library as a public top-level project section.',
            'Published the Founder\'s Note as Publication 0.',
            'Published GCMS-ENG-001, GCMS-ENG-002, and GCMS-ENG-003 as the first foundational Engineering Library publications.',
            'Published GCMS-ENG-004 Architecture Standards as the first formal architecture standard in the Engineering Library.',
            'Published GCMS-ENG-005 Developer Handbook as the practical onboarding and daily workflow guide for Guild CMS developers.',
            'Published GCMS-ENG-006 Contribution Guide as the contributor-facing process reference for proposals, review, documentation, and release packages.',
            'Completed GCMS-ENG-007 through GCMS-ENG-010 and the Engineering Library Volume I completion audit.',
            'Finalized the roadmap by moving installer/bootstrap milestones to Phase 4.4 before implementation begins.',
        ],
    ],
    [
        'label' => 'Phase 4.4',
        'status' => 'current',
        'title' => 'Installation & Bootstrap System',
        'body' => 'Phase 4.4 now represents the complete Guild CMS installer lifecycle, including certification foundation work plus the remaining bootstrap responsibilities that were incorrectly assigned to Phase 4.5.',
        'points' => [
            'Confirmed devsite as the installable Guild CMS product tree.',
            'Published GCMS-ENG-011, GCMS-ENG-012, and GCMS-ENG-013 to formalize installer experience, testing, certification, development, and release standards.',
            'Implemented environment detection and platform intelligence for operating system, package manager, PHP runtime, database support, document root, HTTPS, SELinux, and AppArmor visibility.',
            'Completed Installer Certification Milestone 1 for Rocky Linux 9.8 + Virtualmin, Rocky Linux 9.8 Minimal, AlmaLinux 9.8 Minimal, Ubuntu 24 Minimal, and Debian 12 Minimal.',
            'Realigned configuration generation, requirements validation, database bootstrap, database initialization, administrator account creation, first-run site configuration, plugin manifest format, plugin discovery, hook/event system, and site bootstrap into Phase 4.4.',
            'Restored Phase 4.5 as Data Normalization & Governance before Phase 4.6 Upgrade & Migration Framework.',
        ],
    ],
];
?>
<section class="py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">Development Timeline</h1>
        <p class="lead guild-muted mb-4">A public overview of how Guild CMS evolved before the roadmap data currently shown from Phase 4.1 onward.</p>

        <div class="guild-card p-4 mb-4">
            <h2 class="h4">Why this page exists</h2>
            <p class="guild-muted mb-0">The public site originally began showing detailed data around Phase 4.1, but the platform had already gone through foundation, CMS engine, administration, and release-preparation work. This page fills that gap without turning the public changelog into a full engineering journal.</p>
        </div>

        <?php foreach ($timeline as $entry): ?>
            <div class="guild-card p-4 mb-4">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="guild-muted small mb-1"><?= guildcms_h($entry['label']) ?> · <?= guildcms_badge($entry['status']) ?></div>
                    <h2 class="h4 mb-2"><?= guildcms_h($entry['title']) ?></h2>
                    <p class="guild-muted"><?= guildcms_h($entry['body']) ?></p>
                    <ul class="mb-0">
                        <?php foreach ($entry['points'] as $point): ?>
                            <li><?= guildcms_h($point) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
