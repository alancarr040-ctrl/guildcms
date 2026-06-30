-- Guild CMS Package 4.4.0-6
-- GCMS-ENG-012 - Installer Testing & Certification Framework
-- Schema-aligned migration for current project tables.

START TRANSACTION;

-- Ensure Phase 4.4 remains active and records the certification framework work.
UPDATE project_roadmap_phases
SET
    status = 'in_progress',
    progress = GREATEST(progress, 30),
    sort_order = 440,
    updated_at = NOW()
WHERE phase_key = '4.4';

-- Insert or update the canonical roadmap deliverable for GCMS-ENG-012.
UPDATE project_roadmap_items i
JOIN project_roadmap_phases p ON p.id = i.phase_id
SET
    i.title = 'GCMS-ENG-012 - Installer Testing & Certification Framework',
    i.description = 'Defines installer testing philosophy, runtime support policy, certification environments, base and perfect expectations, failure and recovery tests, UX validation, security checks, and release certification standards.',
    i.status = 'complete',
    i.progress = 100,
    i.sort_order = 36,
    i.is_public = 1,
    i.updated_at = NOW()
WHERE p.phase_key = '4.4'
  AND i.item_key = 'GCMS-ENG-012';

INSERT INTO project_roadmap_items
    (phase_id, parent_id, item_key, title, description, status, progress, sort_order, is_public)
SELECT
    p.id,
    NULL,
    'GCMS-ENG-012',
    'GCMS-ENG-012 - Installer Testing & Certification Framework',
    'Defines installer testing philosophy, runtime support policy, certification environments, base and perfect expectations, failure and recovery tests, UX validation, security checks, and release certification standards.',
    'complete',
    100,
    36,
    1
FROM project_roadmap_phases p
WHERE p.phase_key = '4.4'
  AND NOT EXISTS (
      SELECT 1
      FROM project_roadmap_items i
      JOIN project_roadmap_phases p2 ON p2.id = i.phase_id
      WHERE p2.phase_key = '4.4'
        AND i.item_key = 'GCMS-ENG-012'
  );

-- Record the publication as a public milestone changelog entry.
INSERT INTO project_changelog_entries
    (entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'GCMS-ENG-012 Installer Testing & Certification Framework Published',
    'Published GCMS-ENG-012 to define installer testing philosophy, PHP runtime support policy, certification environments, base and perfect expectations, failure recovery tests, UX validation, security checks, and release certification standards for Phase 4.4.',
    '4.4.0-6',
    'milestone',
    1,
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_changelog_entries
    WHERE phase_key = '4.4.0-6'
      AND title = 'GCMS-ENG-012 Installer Testing & Certification Framework Published'
);

-- Record the development session / journal entry.
INSERT INTO project_development_sessions
    (session_date, title, phase_key, focus, completed, files_changed, next_steps, status, completed_at, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Package 4.4.0-6 - GCMS-ENG-012 Installer Testing & Certification Framework',
    '4.4.0-6',
    'Define the installer testing and certification framework before continuing deeper installer implementation.',
    'Published GCMS-ENG-012, documented runtime support policy, certification environments, base and perfect installer expectations, failure recovery tests, UX validation, security checks, and release certification criteria.',
    'admin/, guildcms/, devsite/docs/, sql/, release documentation',
    'Use the certification framework to validate future Phase 4.4 installer packages on development and clean installation environments.',
    'complete',
    NOW(),
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_development_sessions
    WHERE phase_key = '4.4.0-6'
      AND title = 'Package 4.4.0-6 - GCMS-ENG-012 Installer Testing & Certification Framework'
);

-- Add a Development Center architecture note for the certification policy.
INSERT INTO project_architecture_notes
    (title, body, category, status, sort_order)
SELECT
    'Installer Certification Framework',
    'Guild CMS installer work is validated through a certification framework that distinguishes compatible, supported, and certified runtimes; certifies environments rather than paid hosting panels; and requires both technical validation and user-experience validation for installer releases.',
    'Phase 4.4 Architecture',
    'active',
    446
WHERE NOT EXISTS (
    SELECT 1 FROM project_architecture_notes
    WHERE title = 'Installer Certification Framework'
      AND category = 'Phase 4.4 Architecture'
);

COMMIT;
