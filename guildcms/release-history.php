<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Release History';
$active_page = 'release_history';
require __DIR__ . '/includes/header.php';

$releases = [
    ['v0.1', 'Foundation Prototype', 'Initial modular site direction, shared layout conventions, and early public-site modernization work.'],
    ['v0.2', 'Content Structure', 'Early CMS content handling, reusable sections, and database-backed public content direction.'],
    ['v0.3', 'Section Modules', 'Expansion of section-specific public modules for gaming-community content areas.'],
    ['v0.4', 'Administration Foundation', 'Initial Administration Center direction and management workflow planning.'],
    ['v0.5', 'Admin Tools Expansion', 'Management pages for links, media, section content, and community data began taking shape.'],
    ['v0.6', 'Development Center', 'Engineering record, roadmap tracking, changelog entries, and implementation notes became part of the project workflow.'],
    ['v0.7', 'Public Project Site', 'Dedicated Guild CMS public site, public roadmap, vision, docs, and project presentation were introduced.'],
    ['v0.8', 'Security Preparation', 'Public visibility and hardening preparation leading into Phase 4.2.'],
    ['v0.9', 'Security Hardening Complete', 'Phase 4.2 completed, security posture documented, and Phase 4.3 activated for engineering foundation work.'],
    ['v0.10', 'Engineering Library Foundation', 'Public Engineering Library publication framework, Founder\'s Note, and reserved engineering volumes introduced.'],
    ['v0.11', 'Engineering Foundation Complete', 'Engineering Library Volume I completed and Phase 4.3 roadmap realigned so installation and bootstrap work begins cleanly in Phase 4.4.'],
    ['v0.13', 'Development & Release Standard', 'GCMS-ENG-013 established the official package workflow, SQL migration standard, release documentation requirements, validation gates, and Git baseline policy for Guild CMS development.'],
    ['v0.12', 'Installer Experience Foundation', 'GCMS-ENG-011 established user experience and educational design principles for the installer, Administration Center, errors, documentation, and future user-facing workflows.'],
    ['v1.0', 'Planned First Release', 'Target release line for installer-supported deployment, bootstrap setup, and release-ready documentation.'],
];
?>
<section class="py-5">
    <div class="container">
        <h1 class="display-5 fw-bold mb-2">Release History</h1>
        <p class="lead guild-muted mb-4">Version-level milestones for Guild CMS.</p>

        <div class="guild-card p-4 mb-4">
            <h2 class="h4">How this differs from the changelog</h2>
            <p class="guild-muted mb-0">Release History is intentionally high-level. It explains what each version represented. The changelog should stay focused on concrete public updates and package-level changes.</p>
        </div>

        <div class="guild-card p-4">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Version</th>
                            <th>Milestone</th>
                            <th>Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($releases as $release): ?>
                            <tr>
                                <td><strong><?= guildcms_h($release[0]) ?></strong></td>
                                <td><?= guildcms_h($release[1]) ?></td>
                                <td class="guild-muted"><?= guildcms_h($release[2]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
