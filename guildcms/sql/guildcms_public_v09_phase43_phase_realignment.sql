-- Guild CMS Package 4.3.0-13
-- Phase Realignment Final
-- Purpose: close Phase 4.3 as Engineering Foundation & Governance and move installer/bootstrap milestones to Phase 4.4.
-- Safe to re-run. No schema changes are performed.

START TRANSACTION;

-- Ensure Phase 4.3 reflects the completed engineering/governance workstream.
UPDATE project_roadmap_phases
SET
    title = 'Phase 4.3 - Engineering Foundation & Governance',
    description = 'Engineering Foundation & Governance is complete. Phase 4.3 delivered the Engineering Library Volume I publication set, governance model, engineering standards, Architecture Decision Records, contribution workflow, coding standards, security standards, and publication framework.',
    status = 'complete',
    progress = 100,
    completed_at = COALESCE(completed_at, CURDATE()),
    sort_order = 430,
    is_public = 1
WHERE phase_key = '4.3'
   OR phase_key = 'phase_4_3'
   OR phase_key = 'phase-4-3'
   OR title LIKE 'Phase 4.3%';

-- Ensure Phase 4.4 is the planned Installation & Bootstrap System phase.
UPDATE project_roadmap_phases
SET
    title = 'Phase 4.4 - Installation & Bootstrap System',
    description = 'Installer, bootstrap workflow, environment checks, initial configuration, database bootstrap, site bootstrap, authentication provider bootstrap, plugin discovery preparation, and deployment preparation.',
    status = 'planned',
    progress = 0,
    started_at = NULL,
    completed_at = NULL,
    sort_order = 440,
    is_public = 1
WHERE phase_key = '4.4'
   OR phase_key = 'phase_4_4'
   OR phase_key = 'phase-4-4'
   OR title LIKE 'Phase 4.4%'
   OR title LIKE '%Installation & Bootstrap%';

-- Capture phase IDs for item movement.
SET @phase43_id := (
    SELECT id
    FROM project_roadmap_phases
    WHERE phase_key = '4.3'
       OR phase_key = 'phase_4_3'
       OR phase_key = 'phase-4-3'
       OR title LIKE 'Phase 4.3%'
    ORDER BY sort_order ASC, id ASC
    LIMIT 1
);

SET @phase44_id := (
    SELECT id
    FROM project_roadmap_phases
    WHERE phase_key = '4.4'
       OR phase_key = 'phase_4_4'
       OR phase_key = 'phase-4-4'
       OR title LIKE 'Phase 4.4%'
       OR title LIKE '%Installation & Bootstrap%'
    ORDER BY sort_order ASC, id ASC
    LIMIT 1
);

-- Move installer/bootstrap items from Phase 4.3 to Phase 4.4 where they already exist.
UPDATE project_roadmap_items
SET
    phase_id = @phase44_id,
    status = 'planned',
    progress = 0,
    is_public = 1
WHERE @phase44_id IS NOT NULL
  AND title IN (
    'Requirements Checker',
    'Configuration Generator',
    'Database Bootstrap',
    'Site Bootstrap',
    'Authentication Provider Bootstrap',
    'Installer Security Review',
    'Installer Bootstrap Framework',
    'Plugin Manifest Format',
    'Plugin Discovery'
  );

-- Ensure Phase 4.3 engineering/governance items are complete if they exist.
UPDATE project_roadmap_items
SET
    phase_id = COALESCE(@phase43_id, phase_id),
    status = 'complete',
    progress = 100,
    is_public = 1
WHERE title IN (
    'Engineering Library',
    'Engineering Publications',
    'Engineering Governance',
    'Engineering Standards',
    'Architecture Decision Records',
    'Engineering Workflow',
    'Development Center Realignment',
    'Engineering Library Volume I'
  );

-- Add missing Phase 4.3 governance items so the completed phase is self-describing.
INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, is_public)
SELECT @phase43_id, 'Engineering Publications', 'Publish GCMS-ENG-000 through GCMS-ENG-010 as Engineering Library Volume I.', 'complete', 100, 431, 1
WHERE @phase43_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Engineering Publications');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, is_public)
SELECT @phase43_id, 'Architecture Decision Records', 'Establish ADR structure, lifecycle, numbering, and initial foundational architecture decisions.', 'complete', 100, 432, 1
WHERE @phase43_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Architecture Decision Records');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, is_public)
SELECT @phase43_id, 'Engineering Workflow', 'Establish architecture discussion, implementation, security review, public site, and release package workflow.', 'complete', 100, 433, 1
WHERE @phase43_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Engineering Workflow');

-- Add missing Phase 4.4 installer items so the next phase starts with a complete planned milestone list.
INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, is_public)
SELECT @phase44_id, 'Requirements Checker', 'Check PHP version, required extensions, writable paths, database access, Composer dependencies, and server compatibility.', 'planned', 0, 441, 1
WHERE @phase44_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Requirements Checker');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, is_public)
SELECT @phase44_id, 'Configuration Generator', 'Generate initial Guild CMS configuration files without requiring manual edits.', 'planned', 0, 442, 1
WHERE @phase44_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Configuration Generator');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, is_public)
SELECT @phase44_id, 'Database Bootstrap', 'Create and seed required core tables for a new installation.', 'planned', 0, 443, 1
WHERE @phase44_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Database Bootstrap');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, is_public)
SELECT @phase44_id, 'Site Bootstrap', 'Create the initial site identity, default theme, admin access, and base content.', 'planned', 0, 444, 1
WHERE @phase44_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Site Bootstrap');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, is_public)
SELECT @phase44_id, 'Authentication Provider Bootstrap', 'Support phpBB as the initial authentication provider while preparing for future native providers.', 'planned', 0, 445, 1
WHERE @phase44_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Authentication Provider Bootstrap');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, is_public)
SELECT @phase44_id, 'Plugin Manifest Format', 'Define plugin metadata, compatibility, dependency, permissions, and lifecycle fields.', 'planned', 0, 446, 1
WHERE @phase44_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Plugin Manifest Format');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, is_public)
SELECT @phase44_id, 'Plugin Discovery', 'Prepare installer-aware plugin discovery and package scanning groundwork.', 'planned', 0, 447, 1
WHERE @phase44_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Plugin Discovery');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, is_public)
SELECT @phase44_id, 'Installer Bootstrap Framework', 'Create the first installer architecture and bootstrap workflow foundation.', 'planned', 0, 448, 1
WHERE @phase44_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Installer Bootstrap Framework');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, is_public)
SELECT @phase44_id, 'Installer Security Review', 'Audit installer permissions, configuration handling, install directory locking/removal, and default hardening before phase completion.', 'planned', 0, 449, 1
WHERE @phase44_id IS NOT NULL
  AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Installer Security Review');

-- Record the realignment in the public/internal changelog.
INSERT INTO project_changelog_entries
    (entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Package 4.3.0-13 Phase Realignment Final',
    'Phase 4.3 has been closed as Engineering Foundation & Governance. Installer and bootstrap milestones were moved into Phase 4.4 so the next phase begins from a clean Installation & Bootstrap System roadmap.',
    '4.3.0-13',
    'milestone',
    1,
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_changelog_entries
    WHERE phase_key = '4.3.0-13'
      AND title = 'Package 4.3.0-13 Phase Realignment Final'
);

-- Record the work session/journal entry when the Development Center session table is available.
INSERT INTO project_development_sessions
    (session_date, title, phase_key, focus, completed, files_changed, next_steps, status, completed_at, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Package 4.3.0-13 Phase Realignment Final',
    '4.3.0-13',
    'Correct the Phase 4.3 and Phase 4.4 roadmap boundary before installer development begins.',
    'Closed Phase 4.3 as Engineering Foundation & Governance, moved installer/bootstrap items to Phase 4.4, and synchronized the public and Development Center roadmaps.',
    'Development Center roadmap, public roadmap, timeline, journal, changelog, SQL migration, and release documentation.',
    'Begin Phase 4.4 Package 4.4.0-1 with installer/bootstrap architecture and no public-info-site installer scope confusion.',
    'complete',
    NOW(),
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_development_sessions
    WHERE phase_key = '4.3.0-13'
      AND title = 'Package 4.3.0-13 Phase Realignment Final'
);

COMMIT;
