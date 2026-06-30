-- Guild CMS Package 4.4.0-7
-- GCMS-ENG-013 - Guild CMS Development & Release Standard
-- Schema-aligned migration for current project tables.

START TRANSACTION;

UPDATE project_roadmap_phases
SET
    status = 'in_progress',
    progress = GREATEST(progress, 40),
    sort_order = 440,
    updated_at = NOW()
WHERE phase_key = '4.4';

UPDATE project_roadmap_items i
JOIN project_roadmap_phases p ON p.id = i.phase_id
SET
    i.title = 'GCMS-ENG-013 - Guild CMS Development & Release Standard',
    i.description = 'Defines the official Guild CMS package workflow, repository layout, release documentation, SQL migration rules, roadmap semantics, validation gates, package manifests, and Git baseline policy.',
    i.status = 'complete',
    i.progress = 100,
    i.sort_order = 37,
    i.is_public = 1,
    i.updated_at = NOW()
WHERE p.phase_key = '4.4'
  AND i.item_key = 'GCMS-ENG-013';

INSERT INTO project_roadmap_items
    (phase_id, parent_id, item_key, title, description, status, progress, sort_order, is_public)
SELECT
    p.id,
    NULL,
    'GCMS-ENG-013',
    'GCMS-ENG-013 - Guild CMS Development & Release Standard',
    'Defines the official Guild CMS package workflow, repository layout, release documentation, SQL migration rules, roadmap semantics, validation gates, package manifests, and Git baseline policy.',
    'complete',
    100,
    37,
    1
FROM project_roadmap_phases p
WHERE p.phase_key = '4.4'
  AND NOT EXISTS (
      SELECT 1
      FROM project_roadmap_items i
      JOIN project_roadmap_phases p2 ON p2.id = i.phase_id
      WHERE p2.phase_key = '4.4'
        AND i.item_key = 'GCMS-ENG-013'
  );

INSERT INTO project_changelog_entries
    (entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'GCMS-ENG-013 Development & Release Standard Published',
    'Published GCMS-ENG-013 to define the official Guild CMS development and release standard, including package layout, changed-file packaging, SQL migration rules, roadmap semantics, validation gates, release documentation, package manifests, and Git baseline policy.',
    '4.4.0-7',
    'milestone',
    1,
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_changelog_entries
    WHERE phase_key = '4.4.0-7'
      AND title = 'GCMS-ENG-013 Development & Release Standard Published'
);

INSERT INTO project_development_sessions
    (session_date, title, phase_key, focus, completed, files_changed, next_steps, status, completed_at, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Package 4.4.0-7 - GCMS-ENG-013 Development & Release Standard',
    '4.4.0-7',
    'Establish the standard package, release, SQL, validation, and Git baseline workflow for Guild CMS development.',
    'Published GCMS-ENG-013, updated Engineering Library metadata, added public and Development Center references, and documented the official changed-files package layout and validation requirements.',
    'admin/, guildcms/, devsite/docs/, sql/, release documentation',
    'Use GCMS-ENG-013 as the default package standard for future Guild CMS development prompts and release packages.',
    'complete',
    NOW(),
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_development_sessions
    WHERE phase_key = '4.4.0-7'
      AND title = 'Package 4.4.0-7 - GCMS-ENG-013 Development & Release Standard'
);

INSERT INTO project_architecture_notes
    (title, body, category, status, sort_order)
SELECT
    'Development and Release Standard',
    'Guild CMS packages follow GCMS-ENG-013: build against the authoritative baseline, modify actual source files, preserve admin/guildcms/devsite layout, include schema-compatible SQL, include release documentation and a package manifest, validate PHP syntax, and package only changed files without extra nesting.',
    'Release Engineering',
    'active',
    447
WHERE NOT EXISTS (
    SELECT 1 FROM project_architecture_notes
    WHERE title = 'Development and Release Standard'
      AND category = 'Release Engineering'
);

COMMIT;
