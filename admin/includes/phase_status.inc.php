<?php
/**
 * Guild CMS Development Center
 * Phase Status Definitions
 *
 * Package: 4.4.0-10 Phase Roadmap Realignment
 */

defined('GUILD_CMS_DEV_CENTER') || define('GUILD_CMS_DEV_CENTER', true);

$guildcms_current_phase = [
    'phase' => '4.4',
    'title' => 'Installation & Bootstrap System',
    'milestone' => '4.4.0-10',
    'milestone_title' => 'Phase Roadmap Realignment',
    'status' => 'In Progress',
    'summary' => 'Phase 4.4 has been reopened as the complete installer lifecycle. Configuration generation, requirements validation, database bootstrap, database initialization, administrator creation, first-run site configuration, plugin bootstrap responsibilities, and site bootstrap now belong to Phase 4.4 before Phase 4.5 begins.',
];

$guildcms_roadmap = [
    ['phase' => '4.1', 'title' => 'Security Foundation', 'state' => 'complete'],
    ['phase' => '4.2', 'title' => 'Security Hardening', 'state' => 'complete'],
    ['phase' => '4.3', 'title' => 'Engineering Foundation & Governance', 'state' => 'complete'],
    ['phase' => '4.4', 'title' => 'Installation & Bootstrap System', 'state' => 'current'],
    ['phase' => '4.5', 'title' => 'Data Normalization & Governance', 'state' => 'planned'],
    ['phase' => '4.6', 'title' => 'Upgrade & Migration Framework', 'state' => 'planned'],
    ['phase' => '5.0', 'title' => 'Plugin SDK & Extension Framework', 'state' => 'planned'],
    ['phase' => '5.1', 'title' => 'Theme Engine & Template System', 'state' => 'planned'],
    ['phase' => '5.2', 'title' => 'CLI & Developer Tools', 'state' => 'planned'],
    ['phase' => '5.3', 'title' => 'REST API & Developer Services', 'state' => 'planned'],
    ['phase' => '5.4', 'title' => 'Provider Framework Expansion', 'state' => 'planned'],
    ['phase' => '5.5', 'title' => 'Native Authentication System', 'state' => 'planned'],
    ['phase' => '5.6', 'title' => 'Release Readiness & Final Security Review', 'state' => 'planned'],
    ['phase' => '6.0', 'title' => 'Public Release', 'state' => 'planned'],
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
