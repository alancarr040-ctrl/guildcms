<?php
/**
 * The Guild CMS - Development Center
 * Backlog Page
 *
 * Lightweight v0.2 backlog page.
 * No database dependency. No CRUD. This is intentionally static for now.
 */

defined('IN_GUILD_CMS') || defined('IN_PHPBB') || true;

if (!function_exists('guildcms_h')) {
    function guildcms_h(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$backlog_sections = [
    'Core Platform' => [
        'Core Identity (Version, Schema Version, Plugin API Version, Theme API Version)',
        'Service Container',
        'Event Dispatcher',
        'Hook System',
        'Asset Manager',
        'Cache Manager',
        'Scheduler / Cron Framework',
        'CLI Utility',
        'REST API',
        'Logging Framework',
        'Platform metrics separated from installation metrics',
    ],
    'Security' => [
        'CSP Manager',
        'Permissions-Policy header support',
        'Referrer-Policy header support',
        'Cookie hardening',
        'Session hardening',
        'Security audit tools',
        'Upload security review',
        'Optional ModSecurity integration',
        'Optional DNSSEC documentation',
    ],
    'Plugins' => [
        'Plugin manifest format',
        'Plugin installer',
        'Plugin enable / disable lifecycle',
        'Plugin dependency manager',
        'Plugin version compatibility checks',
        'Plugin marketplace',
        'Digital plugin signatures',
        'Automatic plugin updates',
    ],
    'Themes' => [
        'Parent themes',
        'Child themes',
        'Theme configuration',
        'Theme preview',
        'Theme export / import',
        'Theme marketplace',
    ],
    'Administration' => [
        'Separate Platform Dashboard',
        'Separate Installation Dashboard',
        'Global search',
        'Notification center',
        'Bulk operations',
        'Better filtering',
        'Admin favorites',
        'Dashboard widgets',
    ],
    'Development Center' => [
        'Architecture documentation',
        'Database browser',
        'API documentation',
        'Hook documentation',
        'Plugin SDK documentation',
        'Theme development guide',
        'Coding standards',
        'Release notes',
        'Upgrade guide',
    ],
    'Future Ideas' => [
        'Multi-site support',
        'Docker development environment',
        'Composer package distribution',
        'GitHub Actions / CI',
        'Unit testing',
        'Performance profiler',
        'Localization',
        'Automatic installer',
        'Automatic upgrader',
    ],
];

$ideas_under_consideration = [
    'Developer mode',
    'Built-in diagnostics',
    'Package manager',
    'Extension repository',
    'Marketplace ratings',
    'Verified publishers',
    'Site cloning',
    'Configuration profiles',
    'Public demo installation',
    'Contributor onboarding guide',
];

$total_items = 0;
foreach ($backlog_sections as $items) {
    $total_items += count($items);
}
?>

<section class="dev-center-page dev-backlog-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="mb-1">Backlog</h1>
            <p class="text-muted mb-0">
                Parking lot for future Guild CMS ideas that should not derail the current phase.
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <span class="badge text-bg-primary">Development Center v0.6</span>
            <span class="badge text-bg-secondary"><?= (int) $total_items ?> backlog items</span>
            <span class="badge text-bg-info">Static page</span>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm" role="alert">
        <strong>Purpose:</strong>
        Capture ideas without turning every idea into immediate scope. During phase planning,
        review this list and promote selected items into the roadmap.
    </div>

    <div class="row g-4">
        <?php foreach ($backlog_sections as $section_title => $items): ?>
            <div class="col-12 col-xl-6">
                <div class="card h-100 shadow-sm dev-backlog-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h5 mb-0"><?= guildcms_h($section_title) ?></h2>
                        <span class="badge text-bg-dark"><?= count($items) ?></span>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush dev-backlog-list">
                            <?php foreach ($items as $item): ?>
                                <li class="list-group-item d-flex gap-2 align-items-start px-0">
                                    <span class="text-muted" aria-hidden="true">☐</span>
                                    <span><?= guildcms_h($item) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h2 class="h5 mb-0">Ideas Under Consideration</h2>
        </div>
        <div class="card-body">
            <p class="text-muted">
                These are not commitments yet. They are concepts worth preserving until they are promoted,
                merged into another item, or removed.
            </p>

            <div class="row g-2">
                <?php foreach ($ideas_under_consideration as $idea): ?>
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="border rounded p-2 h-100 bg-body-tertiary">
                            <?= guildcms_h($idea) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="card mt-4 shadow-sm">
        <div class="card-header">
            <h2 class="h5 mb-0">Backlog Rules</h2>
        </div>
        <div class="card-body">
            <ol class="mb-0">
                <li>If an idea does not belong in the current phase, add it here.</li>
                <li>If an idea becomes committed work, promote it to the roadmap.</li>
                <li>If an idea is no longer useful, remove or archive it during phase review.</li>
                <li>Review this page at the end of each phase.</li>
            </ol>
        </div>
    </div>
</section>
