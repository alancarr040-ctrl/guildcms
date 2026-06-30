<?php
declare(strict_types=1);

if (!function_exists('guildcms_public_h')) {
    function guildcms_public_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$roadmap_rows = [
    ['phase' => 'Phase 4.1', 'status' => 'Complete', 'class' => 'bg-success', 'title' => 'Security Foundation', 'summary' => 'Established the initial security foundation and formal security tracking for Guild CMS.'],
    ['phase' => 'Phase 4.2', 'status' => 'Complete', 'class' => 'bg-success', 'title' => 'Security Hardening', 'summary' => 'Security headers, CSP Report-Only, CSRF review, upload hardening, and filesystem permission cleanup.'],
    ['phase' => 'Phase 4.3', 'status' => 'Complete', 'class' => 'bg-success', 'title' => 'Engineering Foundation & Governance', 'summary' => 'Engineering Library Volume I, project governance, public engineering publications, documentation standards, and publication lifecycle tracking.'],
    ['phase' => 'Phase 4.4', 'status' => 'In Progress', 'class' => 'bg-info', 'title' => 'Installation & Bootstrap System', 'summary' => 'Builds the installable Guild CMS product from the devsite tree, beginning with installer architecture, framework structure, setup detection, product separation, requirements validation, database bootstrap, installation completion, and installer security review.'],
    ['phase' => 'Package 4.4.0-1', 'status' => 'Published', 'class' => 'bg-success', 'title' => 'Installation Architecture', 'summary' => 'Defined the installer boundary, bootstrap model, installable devsite role, and phase objectives for the Guild CMS installation system.'],
    ['phase' => 'Package 4.4.0-2', 'status' => 'Published', 'class' => 'bg-success', 'title' => 'Installer Framework', 'summary' => 'Introduced the initial installer framework skeleton in devsite and separated installer code from the Guild CMS public information site.'],
    ['phase' => 'Package 4.4.0-3', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Setup Detection & Product Separation Preparation', 'summary' => 'Prepare devsite to fail gracefully when configuration is missing, add config sample guidance, and begin documenting remaining TheRegs assumptions.'],
    ['phase' => 'Package 4.4.0-4', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Product Separation Audit', 'summary' => 'Audit and remove hardcoded TheRegs assumptions from the installable product tree so Guild CMS can become a clean distributable CMS.'],
    ['phase' => 'Package 4.4.0-5', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Installer Framework Expansion', 'summary' => 'Expand the installer framework so it can begin generating configuration and collecting site setup values.'],
    ['phase' => 'Package 4.4.0-6', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Requirements & Validation', 'summary' => 'Implement environment validation for PHP, required extensions, writable paths, sessions, filesystem behavior, and database readiness.'],
    ['phase' => 'Package 4.4.0-7', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Database Bootstrap', 'summary' => 'Install schema, seed default data, prepare administrator creation, and validate database bootstrap safety.'],
    ['phase' => 'Package 4.4.0-8', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Installation Completion', 'summary' => 'Finalize installation, lock the installer, provide first-login guidance, and present a clear completion path.'],
    ['phase' => 'Package 4.4.0-9', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Installer Security Review', 'summary' => 'Review installer attack surface, lockout behavior, configuration handling, permissions, and recovery paths before the next phase.'],
    ['phase' => 'Phase 4.5', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Data Normalization & Governance', 'summary' => 'Normalize project data, document database schemas and enum values, establish database governance rules, and prepare reliable migrations.'],
    ['phase' => 'Phase 4.6', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Upgrade & Migration Framework', 'summary' => 'Upgrade scripts, migration lifecycle, version tracking, rollback guidance, and release-to-release compatibility handling.'],
    ['phase' => 'Phase 5.0', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Plugin SDK & Extension Framework', 'summary' => 'Plugin manifest format, extension lifecycle, hooks, package handling, and SDK documentation.'],
    ['phase' => 'Phase 5.1', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Theme Engine & Template System', 'summary' => 'Theme structure, templates, parent/child themes, preview workflows, and reusable presentation components.'],
    ['phase' => 'Phase 5.2', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'CLI & Developer Tools', 'summary' => 'Command-line tools, developer utilities, diagnostics, maintenance commands, and automation support.'],
    ['phase' => 'Phase 5.3', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'REST API & Developer Services', 'summary' => 'API endpoints, developer services, integration documentation, authentication boundaries, and service contracts.'],
    ['phase' => 'Phase 5.4', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Provider Framework Expansion', 'summary' => 'Provider interfaces and replaceable service implementations for platform subsystems.'],
    ['phase' => 'Phase 5.5', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Native Authentication System', 'summary' => 'Native auth capabilities, user/session architecture, migration planning, and optional provider bridges.'],
    ['phase' => 'Phase 5.6', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Release Readiness & Final Security Review', 'summary' => 'Final release candidate validation, documentation review, security review, installer validation, and readiness certification.'],
    ['phase' => 'Phase 6.0', 'status' => 'Planned', 'class' => 'bg-secondary', 'title' => 'Public Release', 'summary' => 'Guild CMS 1.0 public release, download publication, public documentation, and initial support window.'],
];
?>

<div class="container py-4 text-light guildcms-public-page">
    <div class="p-4 mb-4 bg-dark border border-secondary rounded-3">
        <div class="small text-uppercase text-info fw-semibold mb-2">Guild CMS Roadmap</div>
        <h1 class="display-6 mb-3">Roadmap</h1>
        <p class="lead mb-0">Current and upcoming public milestones for Guild CMS.</p>
    </div>

    <div class="row g-3">
        <?php foreach ($roadmap_rows as $row): ?>
            <div class="col-md-6">
                <div class="card h-100 bg-dark border-secondary text-light">
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                            <span class="badge <?= guildcms_public_h($row['class']) ?>"><?= guildcms_public_h($row['status']) ?></span>
                            <span class="text-secondary"><?= guildcms_public_h($row['phase']) ?></span>
                        </div>
                        <h2 class="h5"><?= guildcms_public_h($row['title']) ?></h2>
                        <p class="mb-0"><?= guildcms_public_h($row['summary']) ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
