-- Guild CMS Package 4.4.0-8a
-- Environment Detection Usability & Educational Enhancements
-- Schema-aligned migration for current project tables.
-- This package primarily changes installer presentation and detection detail.
-- SQL records the package in Development Center history without creating a duplicate roadmap deliverable.

START TRANSACTION;

UPDATE project_roadmap_items i
JOIN project_roadmap_phases p ON p.id = i.phase_id
SET
    i.description = 'Detect operating system, distribution version, package manager family, web server, PHP runtime, PHP extensions, database drivers, filesystem readiness, effective PHP user/group, document root, HTTPS status, SELinux, and AppArmor so later installer steps can provide educational platform-aware guidance.',
    i.updated_at = NOW()
WHERE p.phase_key = '4.4'
  AND i.item_key = '4.4.0-8';

INSERT INTO project_changelog_entries
    (entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Environment Detection Usability Enhanced',
    'Refined the installer Environment Detection page to explain results before displaying technical details. Added effective PHP user/group, document root, includes directory ownership, permissions, php.ini information, progressive disclosure, and permission guidance.',
    '4.4.0-8a',
    'release',
    1,
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_changelog_entries
    WHERE phase_key = '4.4.0-8a'
      AND title = 'Environment Detection Usability Enhanced'
);

INSERT INTO project_development_sessions
    (session_date, title, phase_key, focus, completed, files_changed, next_steps, status, completed_at, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Package 4.4.0-8a - Environment Detection Usability',
    '4.4.0-8a',
    'Improve installer Environment Detection so new administrators can understand filesystem paths, PHP execution user, ownership, permissions, and technical details without being overwhelmed.',
    'Added progressive disclosure to Environment Detection, expanded platform detection to include PHP user/group and filesystem ownership, improved includes directory explanation, and updated installer usability guidance.',
    'devsite/install/classes/InstallerPlatform.php, devsite/install/steps/EnvironmentStep.php, devsite/install/assets/install.css, devsite/docs/INSTALLER_PLATFORM_INTELLIGENCE.md, guildcms/docs/engineering/, admin/pages/installer_architecture.inc.php, guildcms/installation.php',
    'Use the enhanced environment snapshot in System Readiness and future permission remediation checks.',
    'complete',
    NOW(),
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_development_sessions
    WHERE phase_key = '4.4.0-8a'
      AND title = 'Package 4.4.0-8a - Environment Detection Usability'
);

COMMIT;
