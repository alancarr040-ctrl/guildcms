-- Guild CMS Package 4.4.0-4 - First Installer
-- Schema-aligned Development Center migration.

START TRANSACTION;

UPDATE project_roadmap_phases
SET
    title = 'Phase 4.4 - Installation & Bootstrap System',
    status = 'in_progress',
    progress = 40,
    sort_order = 440,
    description = 'Phase 4.4 builds the Guild CMS installation and bootstrap system against the devsite installable product tree. Package 4.4.0-4 introduces setup detection and the first educational installer experience.',
    updated_at = NOW()
WHERE phase_key = '4.4';

INSERT INTO project_roadmap_items
    (phase_id, parent_id, item_key, title, description, status, progress, sort_order, is_public)
SELECT
    p.id,
    NULL,
    'first-installer',
    'First Installer Experience',
    'Introduce setup detection, a welcoming unconfigured-site page, installer progress navigation, required system readiness checks, recommended feature checks, save/cancel controls, and the first educational installer workflow in devsite.',
    'complete',
    100,
    35,
    1
FROM project_roadmap_phases p
WHERE p.phase_key = '4.4'
  AND NOT EXISTS (
      SELECT 1 FROM project_roadmap_items i
      WHERE i.phase_id = p.id
        AND i.item_key = 'first-installer'
  );

UPDATE project_roadmap_items i
JOIN project_roadmap_phases p ON p.id = i.phase_id
SET
    i.title = 'First Installer Experience',
    i.description = 'Introduce setup detection, a welcoming unconfigured-site page, installer progress navigation, required system readiness checks, recommended feature checks, save/cancel controls, and the first educational installer workflow in devsite.',
    i.status = 'complete',
    i.progress = 100,
    i.sort_order = 35,
    i.is_public = 1,
    i.updated_at = NOW()
WHERE p.phase_key = '4.4'
  AND i.item_key = 'first-installer';

INSERT INTO project_changelog_entries
    (entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Package 4.4.0-4 First Installer Published',
    'Introduced the first Guild CMS installer experience in devsite, including setup detection, a setup-required landing page, config sample reference, expanded installer step model, required system readiness checks, recommended feature checks, progress display, save and cancel controls, and public/admin documentation updates.',
    '4.4',
    'milestone',
    1,
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_changelog_entries
    WHERE phase_key = '4.4'
      AND title = 'Package 4.4.0-4 First Installer Published'
);

INSERT INTO project_development_sessions
    (session_date, title, phase_key, focus, completed, files_changed, next_steps, status, started_at, completed_at, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Package 4.4.0-4 First Installer',
    '4.4',
    'Begin real installer implementation against the devsite installable product tree.',
    'Added setup detection, setup-required page, config sample, expanded installer steps, required and recommended environment checks, installer progress UI, save/cancel controls, and synchronized public/admin references.',
    'devsite/index.php; devsite/includes/setup_required.php; devsite/includes/config.sample.inc.php; devsite/install/*; admin/pages/installer_architecture.inc.php; admin/data/development_center_data.php; guildcms/installation.php',
    'Continue with deeper setup detection, product separation cleanup, and configuration generation in the next Phase 4.4 package.',
    'complete',
    NOW(),
    NOW(),
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_development_sessions
    WHERE phase_key = '4.4'
      AND title = 'Package 4.4.0-4 First Installer'
);

INSERT INTO project_architecture_notes
    (title, body, category, status, sort_order)
SELECT
    'Installer Setup Detection Boundary',
    'Guild CMS must not fail with PHP or database errors when a new installable tree has not been configured. The front controller detects missing or placeholder configuration and renders an educational setup-required page that links to the installer. The installer creates includes/config.inc.php during the install phase; completed configuration is installation-specific and should not be treated as reusable project source.',
    'Installer',
    'active',
    440
WHERE NOT EXISTS (
    SELECT 1 FROM project_architecture_notes
    WHERE title = 'Installer Setup Detection Boundary'
);

COMMIT;
