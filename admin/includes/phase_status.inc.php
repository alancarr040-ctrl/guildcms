<?php
/**
 * Guild CMS Development Center
 * Phase Status Definitions
 *
 * Package: 4.4.0-9 Installer Certification Milestone 1 Publication
 */

defined('GUILD_CMS_DEV_CENTER') || define('GUILD_CMS_DEV_CENTER', true);

$guildcms_current_phase = [
    'phase' => '4.4',
    'title' => 'Installation & Bootstrap System',
    'milestone' => '4.4.0-9',
    'milestone_title' => 'Installer Certification Milestone 1 Publication',
    'status' => 'Complete',
    'summary' => 'Phase 4.4 is complete. Guild CMS has published Installer Certification Milestone 1 for the foundation platform set and is prepared to begin Phase 4.5 Core Installation Experience.',
];

$guildcms_roadmap = [
    ['phase' => '4.1', 'title' => 'Security Foundation', 'state' => 'complete'],
    ['phase' => '4.2', 'title' => 'Security Hardening', 'state' => 'complete'],
    ['phase' => '4.3', 'title' => 'Engineering Foundation & Governance', 'state' => 'complete'],
    ['phase' => '4.4', 'title' => 'Installation & Bootstrap System', 'state' => 'complete'],
    ['phase' => '4.5', 'title' => 'Core Installation Experience', 'state' => 'current'],
    ['phase' => '4.6', 'title' => 'Upgrade & Migration Framework', 'state' => 'planned'],
    ['phase' => '5.0', 'title' => 'Plugin SDK & Extension Framework', 'state' => 'planned'],
    ['phase' => '5.1', 'title' => 'Theme Engine & Template System', 'state' => 'planned'],
    ['phase' => '5.2', 'title' => 'CLI & Developer Tools', 'state' => 'planned'],
    ['phase' => '5.3', 'title' => 'REST API & Developer Services', 'state' => 'planned'],
    ['phase' => '5.4', 'title' => 'Provider Framework Expansion', 'state' => 'planned'],
    ['phase' => '5.5', 'title' => 'Native Authentication System', 'state' => 'planned'],
    ['phase' => '6.0', 'title' => 'Enterprise & Multi-site Features', 'state' => 'planned'],
];

function guildcms_phase_badge(string $state): string
{
    switch ($state) {
        case 'complete':
            return '<span class="badge text-bg-success">Complete</span>';
        case 'current':
            return '<span class="badge text-bg-warning text-dark">Current</span>';
        default:
            return '<span class="badge text-bg-secondary">Planned</span>';
    }
}
