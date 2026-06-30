-- Guild CMS Package 4.4.0-8
-- Installer Environment Detection & Platform Intelligence
-- Schema-aligned migration for current project tables.

START TRANSACTION;

UPDATE project_roadmap_phases
SET
    status = 'in_progress',
    progress = GREATEST(progress, 45),
    sort_order = 440,
    updated_at = NOW()
WHERE phase_key = '4.4';

UPDATE project_roadmap_items i
JOIN project_roadmap_phases p ON p.id = i.phase_id
SET
    i.title = 'Installer Environment Detection & Platform Intelligence',
    i.description = 'Detect operating system, distribution version, package manager family, web server, PHP runtime, PHP extensions, database drivers, filesystem readiness, HTTPS status, SELinux, and AppArmor so later installer steps can provide platform-aware guidance.',
    i.status = 'complete',
    i.progress = 100,
    i.sort_order = 48,
    i.is_public = 1,
    i.updated_at = NOW()
WHERE p.phase_key = '4.4'
  AND i.item_key = '4.4.0-8';

INSERT INTO project_roadmap_items
    (phase_id, parent_id, item_key, title, description, status, progress, sort_order, is_public)
SELECT
    p.id,
    NULL,
    '4.4.0-8',
    'Installer Environment Detection & Platform Intelligence',
    'Detect operating system, distribution version, package manager family, web server, PHP runtime, PHP extensions, database drivers, filesystem readiness, HTTPS status, SELinux, and AppArmor so later installer steps can provide platform-aware guidance.',
    'complete',
    100,
    48,
    1
FROM project_roadmap_phases p
WHERE p.phase_key = '4.4'
  AND NOT EXISTS (
      SELECT 1
      FROM project_roadmap_items i
      JOIN project_roadmap_phases p2 ON p2.id = i.phase_id
      WHERE p2.phase_key = '4.4'
        AND i.item_key = '4.4.0-8'
  );

INSERT INTO project_changelog_entries
    (entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Installer Environment Detection Added',
    'Added platform intelligence to the Guild CMS installer so it can detect the operating system, package manager family, web server, PHP runtime, database drivers, filesystem readiness, HTTPS status, SELinux, and AppArmor before later setup steps use that information for guidance.',
    '4.4.0-8',
    'release',
    1,
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_changelog_entries
    WHERE phase_key = '4.4.0-8'
      AND title = 'Installer Environment Detection Added'
);

INSERT INTO project_development_sessions
    (session_date, title, phase_key, focus, completed, files_changed, next_steps, status, completed_at, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Package 4.4.0-8 - Installer Environment Detection',
    '4.4.0-8',
    'Add platform intelligence to the installer so later readiness, database, and configuration steps can explain issues using the detected server environment.',
    'Added environment detection step, platform detector, session storage for environment snapshots, PHP 8.2 minimum preflight, installer documentation updates, and public/admin installer references.',
    'devsite/install/, devsite/docs/, admin/pages/installer_architecture.inc.php, guildcms/installation.php, guildcms/docs/engineering/, sql/, release documentation',
    'Use the detected environment snapshot in the next installer packages for readiness validation, recommended features, and platform-specific remediation guidance.',
    'complete',
    NOW(),
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_development_sessions
    WHERE phase_key = '4.4.0-8'
      AND title = 'Package 4.4.0-8 - Installer Environment Detection'
);

INSERT INTO project_architecture_notes
    (title, body, category, status, sort_order)
SELECT
    'Installer Platform Intelligence',
    'The Guild CMS installer detects the operating system, package manager family, web server, PHP runtime, database drivers, filesystem readiness, HTTPS status, SELinux, and AppArmor before later installer steps rely on environment-specific guidance. Detection is read-only and stored in installer session state.',
    'Installer',
    'active',
    448
WHERE NOT EXISTS (
    SELECT 1 FROM project_architecture_notes
    WHERE title = 'Installer Platform Intelligence'
      AND category = 'Installer'
);

COMMIT;
