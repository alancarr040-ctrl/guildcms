-- Guild CMS Package 4.4.0-5 - System Readiness Check
-- Schema-aligned migration for project_roadmap_phases, project_roadmap_items,
-- project_development_sessions, and project_changelog_entries.

START TRANSACTION;

UPDATE project_roadmap_phases
SET
    status = 'in_progress',
    progress = GREATEST(progress, 45),
    title = 'Phase 4.4 - Installation & Bootstrap System',
    sort_order = 440,
    updated_at = NOW()
WHERE phase_key = '4.4';

INSERT INTO project_roadmap_items
    (phase_id, parent_id, item_key, title, description, status, progress, sort_order, is_public)
SELECT
    p.id,
    NULL,
    '4.4.0-5',
    'System Readiness Check',
    'Implement the first testable installer validation step with required server checks, recommended feature checks, clear explanations, corrective guidance, and safe recheck behavior.',
    'complete',
    100,
    50,
    1
FROM project_roadmap_phases p
WHERE p.phase_key = '4.4'
  AND NOT EXISTS (
      SELECT 1 FROM project_roadmap_items i
      WHERE i.phase_id = p.id
        AND i.item_key = '4.4.0-5'
  );

UPDATE project_roadmap_items i
JOIN project_roadmap_phases p ON p.id = i.phase_id
SET
    i.title = 'System Readiness Check',
    i.description = 'Implement the first testable installer validation step with required server checks, recommended feature checks, clear explanations, corrective guidance, and safe recheck behavior.',
    i.status = 'complete',
    i.progress = 100,
    i.sort_order = 50,
    i.updated_at = NOW(),
    i.is_public = 1
WHERE p.phase_key = '4.4'
  AND i.item_key = '4.4.0-5';

INSERT INTO project_development_sessions
    (session_date, title, phase_key, focus, completed, files_changed, next_steps, status, completed_at, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Package 4.4.0-5 - System Readiness Check',
    '4.4',
    'Make the Guild CMS installer test real server readiness before collecting database or configuration information.',
    'Implemented required and recommended installer checks, explanatory pass/warning/fail messaging, recheck behavior, and public/Development Center updates.',
    'devsite/install/classes/InstallerEnvironment.php; devsite/install/steps/RequirementsStep.php; devsite/install/steps/RecommendedStep.php; devsite/install/templates/layout.php; devsite/install/assets/install.css; guildcms/installation.php; admin/data/development_center_data.php; sql/guildcms_4_4_0_5_system_readiness.sql',
    'Continue Phase 4.4 with installer save/resume refinement or the next installer step implementation.',
    'complete',
    NOW(),
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_development_sessions
    WHERE phase_key = '4.4'
      AND title = 'Package 4.4.0-5 - System Readiness Check'
);

INSERT INTO project_changelog_entries
    (entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Package 4.4.0-5 - System Readiness Check',
    'Implemented the first testable installer validation step. Guild CMS now separates required server readiness checks from recommended feature checks, explains why each item matters, provides corrective guidance, and blocks continuation only when a required runtime capability is missing.',
    '4.4.0-5',
    'release',
    1,
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_changelog_entries
    WHERE phase_key = '4.4.0-5'
      AND title = 'Package 4.4.0-5 - System Readiness Check'
);

COMMIT;
