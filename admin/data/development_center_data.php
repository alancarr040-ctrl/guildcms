<?php
/**
 * The Guild CMS - Development Center data
 *
 * Static v0.3 preview data source for the Development Center dashboard.
 * No database calls. No superglobals. phpBB-safe.
 */

if (!defined('IN_PHPBB')) {
    exit;
}

$guildcmsDevCenter = [
    'version' => 'v0.4.5 Phase 4.4',
    'current_phase' => 'Phase 4.4 - Installation & Bootstrap System',
    'next_phase' => 'Phase 4.5 - Data Normalization & Governance',
    'platform_direction' => 'Installable Product Bootstrap',
];

$guildcmsRoadmap = [
    ['title' => 'Phase 1 - Foundation', 'status' => 'Complete', 'progress' => 100],
    ['title' => 'Phase 2 - Administration Framework', 'status' => 'Complete', 'progress' => 100],
    ['title' => 'Phase 3A - Shared Layout', 'status' => 'Complete', 'progress' => 100],
    ['title' => 'Phase 3B - Navigation Manager', 'status' => 'Complete', 'progress' => 100],
    ['title' => 'Phase 3C - Content Rendering Engine', 'status' => 'Complete', 'progress' => 100],
    ['title' => 'Phase 4.1 - Security Foundation', 'status' => 'Complete', 'progress' => 100],
    ['title' => 'Phase 4.2 - Security Hardening', 'status' => 'Complete', 'progress' => 100],
    ['title' => 'Phase 4.3 - Engineering Foundation & Governance', 'status' => 'Complete', 'progress' => 100],
    ['title' => 'Phase 4.4 - Installation & Bootstrap System', 'status' => 'In Progress', 'progress' => 45],
    ['title' => 'Phase 4.5 - Data Normalization & Governance', 'status' => 'Planned', 'progress' => 0],
    ['title' => 'Phase 4.6 - Upgrade & Migration Framework', 'status' => 'Planned', 'progress' => 0],
    ['title' => 'Phase 5.0 - Plugin SDK & Extension Framework', 'status' => 'Planned', 'progress' => 0],
    ['title' => 'Phase 5.1 - Theme Engine & Template System', 'status' => 'Planned', 'progress' => 0],
    ['title' => 'Phase 5.2 - CLI & Developer Tools', 'status' => 'Planned', 'progress' => 0],
    ['title' => 'Phase 5.3 - REST API & Developer Services', 'status' => 'Planned', 'progress' => 0],
    ['title' => 'Phase 5.4 - Provider Framework Expansion', 'status' => 'Planned', 'progress' => 0],
    ['title' => 'Phase 5.5 - Native Authentication System', 'status' => 'Planned', 'progress' => 0],
    ['title' => 'Phase 6.0 - Enterprise & Multi-site Features', 'status' => 'Planned', 'progress' => 0],
];

$guildcmsSecurityStatus = [
    'Headers' => [
        ['label' => 'Shared security headers include', 'done' => true],
        ['label' => 'X-Content-Type-Options', 'done' => true],
        ['label' => 'Referrer-Policy', 'done' => true],
        ['label' => 'Permissions-Policy', 'done' => true],
        ['label' => 'X-Frame-Options / frame policy', 'done' => true],
        ['label' => 'Content-Security-Policy', 'done' => false],
    ],
    'Sessions & Cookies' => [
        ['label' => 'Cookie hardening review', 'done' => false],
        ['label' => 'Session configuration review', 'done' => false],
        ['label' => 'SameSite/Secure/HttpOnly review', 'done' => false],
    ],
    'Forms & Requests' => [
        ['label' => 'CSRF audit', 'done' => false],
        ['label' => 'Input validation audit', 'done' => false],
        ['label' => 'Output escaping audit', 'done' => false],
    ],
    'Files & Uploads' => [
        ['label' => 'Upload validation review', 'done' => false],
        ['label' => 'MIME/type verification review', 'done' => false],
        ['label' => 'File permissions review', 'done' => false],
    ],
];

$guildcmsBacklog = [
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
        'Plugin dependency manager',
        'Plugin version checking',
        'Plugin marketplace',
        'Digital plugin signatures',
        'Automatic plugin updates',
        'phpBB bridge as optional plugin',
    ],
    'Themes' => [
        'Parent themes',
        'Child themes',
        'Theme configuration',
        'Theme preview',
        'Theme export/import',
        'Theme marketplace',
    ],
    'Administration' => [
        'Separate Platform Dashboard',
        'Separate Installation Dashboard',
        'Global Search',
        'Notification Center',
        'Bulk Operations',
        'Better Filtering',
        'Admin Favorites',
        'Dashboard Widgets',
    ],
    'Development Center' => [
        'Architecture Documentation',
        'Database Browser',
        'API Documentation',
        'Hook Documentation',
        'Plugin SDK Documentation',
        'Theme Development Guide',
        'Coding Standards',
        'Release Notes',
        'Upgrade Guide',
        'Idea origin/provenance tracking',
        'Completed milestone archive',
    ],
    'Engineering Library' => [
        'Constitution',
        'Vision & Mission',
        'Developer Handbook',
        'Contribution Guide',
        'Architecture Standards',
        'Engineering Principles',
        'Architecture Decision Records',
        'Coding Standards',
        'Security Standards',
        "Founder's Note",
    ],
    'Future Ideas' => [
        'Multi-site Support',
        'Docker Development Environment',
        'Composer Package Distribution',
        'GitHub Actions / CI',
        'Unit Testing',
        'Performance Profiler',
        'Localization',
        'Automatic Installer',
        'Automatic Upgrader',
        'Package Manager',
    ],
];


$guildcmsEngineeringPublicationsBaseUrl = 'https://guildcms.theregs.org/engineering/';
$guildcmsEngineeringPublications = [
    ['id' => 'GCMS-ENG-000', 'publication' => 'Publication 0', 'volume' => 'Founder\'s Note', 'title' => 'Founder\'s Note', 'status' => 'Published', 'version' => '1.0', 'phase' => '4.3.0-3', 'url' => $guildcmsEngineeringPublicationsBaseUrl . 'founders-note.php'],
    ['id' => 'GCMS-ENG-001', 'publication' => 'Publication 1', 'volume' => 'Volume I', 'title' => 'Guild CMS Constitution', 'status' => 'Published', 'version' => '1.0', 'phase' => '4.3.0-4', 'url' => $guildcmsEngineeringPublicationsBaseUrl . 'constitution.php'],
    ['id' => 'GCMS-ENG-002', 'publication' => 'Publication 2', 'volume' => 'Volume II', 'title' => 'Vision & Mission', 'status' => 'Published', 'version' => '1.0', 'phase' => '4.3.0-4', 'url' => $guildcmsEngineeringPublicationsBaseUrl . 'vision-mission.php'],
    ['id' => 'GCMS-ENG-003', 'publication' => 'Publication 3', 'volume' => 'Volume III', 'title' => 'Engineering Principles', 'status' => 'Published', 'version' => '1.0', 'phase' => '4.3.0-5', 'url' => $guildcmsEngineeringPublicationsBaseUrl . 'principles.php'],
    ['id' => 'GCMS-ENG-004', 'publication' => 'Publication 4', 'volume' => 'Volume IV', 'title' => 'Architecture Standards', 'status' => 'Published', 'version' => '1.0', 'phase' => '4.3.0-6', 'url' => $guildcmsEngineeringPublicationsBaseUrl . 'architecture-standards.php'],
    ['id' => 'GCMS-ENG-005', 'publication' => 'Publication 5', 'volume' => 'Volume V', 'title' => 'Developer Handbook', 'status' => 'Published', 'version' => '1.0', 'phase' => '4.3.0-7', 'url' => $guildcmsEngineeringPublicationsBaseUrl . 'developer-handbook.php'],
    ['id' => 'GCMS-ENG-006', 'publication' => 'Publication 6', 'volume' => 'Volume VI', 'title' => 'Contribution Guide', 'status' => 'Published', 'version' => '1.0', 'phase' => '4.3.0-8', 'url' => $guildcmsEngineeringPublicationsBaseUrl . 'contribution-guide.php'],
    ['id' => 'GCMS-ENG-007', 'publication' => 'Publication 7', 'volume' => 'Volume VII', 'title' => 'Coding Standards', 'status' => 'Published', 'version' => '1.0', 'phase' => '4.3.0-8', 'url' => $guildcmsEngineeringPublicationsBaseUrl . 'coding-standards.php'],
    ['id' => 'GCMS-ENG-008', 'publication' => 'Publication 8', 'volume' => 'Volume VIII', 'title' => 'Security Standards', 'status' => 'Published', 'version' => '1.0', 'phase' => '4.3.0-9', 'url' => $guildcmsEngineeringPublicationsBaseUrl . 'security-standards.php'],
    ['id' => 'GCMS-ENG-009', 'publication' => 'Publication 9', 'volume' => 'Volume IX', 'title' => 'Architecture Decision Records', 'status' => 'Published', 'version' => '1.0', 'phase' => '4.3.0-10', 'url' => $guildcmsEngineeringPublicationsBaseUrl . 'adr.php'],
    ['id' => 'GCMS-ENG-010', 'publication' => 'Publication 10', 'volume' => 'Volume X', 'title' => 'Engineering Roadmap & Publication Framework', 'status' => 'Published', 'version' => '1.0', 'phase' => '4.3.0-11', 'url' => $guildcmsEngineeringPublicationsBaseUrl . 'future.php'],
    ['id' => 'GCMS-ENG-011', 'publication' => 'Publication 11', 'volume' => 'Volume XI', 'title' => 'User Experience & Educational Design Principles', 'status' => 'Published', 'version' => '1.0', 'phase' => '4.4.0-3', 'url' => $guildcmsEngineeringPublicationsBaseUrl . 'user-experience.php'],
];

function guildcms_count_backlog_items(array $backlog): int
{
    $total = 0;
    foreach ($backlog as $items) {
        $total += count($items);
    }
    return $total;
}

function guildcms_count_security_items(array $security): array
{
    $done = 0;
    $total = 0;

    foreach ($security as $items) {
        foreach ($items as $item) {
            $total++;
            if (!empty($item['done'])) {
                $done++;
            }
        }
    }

    return ['done' => $done, 'total' => $total];
}

function guildcms_count_completed_phases(array $roadmap): array
{
    $done = 0;
    $total = count($roadmap);

    foreach ($roadmap as $phase) {
        if (($phase['status'] ?? '') === 'Complete') {
            $done++;
        }
    }

    return ['done' => $done, 'total' => $total];
}
