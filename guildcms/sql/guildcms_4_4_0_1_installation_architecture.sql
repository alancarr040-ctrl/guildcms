-- Guild CMS Package 4.4.0-1
-- Phase 4.4 - Installation & Bootstrap System
-- Deliverable: Installation Architecture
--
-- This migration activates Phase 4.4, preserves the normalized roadmap
-- sort_order convention, and records the installer architecture package.

START TRANSACTION;

-- Normalize phase titles and sort order. These values are the Guild CMS roadmap standard.
UPDATE project_roadmap_phases
SET title = 'Phase 4.1 - Security Foundation', sort_order = 410, status = 'complete', progress = 100, updated_at = NOW()
WHERE phase_key = '4.1';

UPDATE project_roadmap_phases
SET title = 'Phase 4.2 - Security Hardening', sort_order = 420, status = 'complete', progress = 100, updated_at = NOW()
WHERE phase_key = '4.2';

UPDATE project_roadmap_phases
SET title = 'Phase 4.3 - Engineering Foundation & Governance', sort_order = 430, status = 'complete', progress = 100, updated_at = NOW()
WHERE phase_key = '4.3';

UPDATE project_roadmap_phases
SET title = 'Phase 4.4 - Installation & Bootstrap System', sort_order = 440, status = 'in_progress', progress = 10, updated_at = NOW()
WHERE phase_key = '4.4';

UPDATE project_roadmap_phases
SET title = 'Phase 4.5 - Upgrade & Migration Framework', sort_order = 450, status = 'planned', progress = 0, updated_at = NOW()
WHERE phase_key = '4.5';

UPDATE project_roadmap_phases
SET title = 'Phase 5.0 - Plugin SDK & Extension Framework', sort_order = 500, status = 'planned', progress = 0, updated_at = NOW()
WHERE phase_key = '5.0';

UPDATE project_roadmap_phases
SET title = 'Phase 5.1 - Theme Engine & Template System', sort_order = 510, status = 'planned', progress = 0, updated_at = NOW()
WHERE phase_key = '5.1';

UPDATE project_roadmap_phases
SET title = 'Phase 5.2 - CLI & Developer Tools', sort_order = 520, status = 'planned', progress = 0, updated_at = NOW()
WHERE phase_key = '5.2';

UPDATE project_roadmap_phases
SET title = 'Phase 5.3 - REST API & Developer Services', sort_order = 530, status = 'planned', progress = 0, updated_at = NOW()
WHERE phase_key = '5.3';

UPDATE project_roadmap_phases
SET title = 'Phase 5.4 - Provider Framework Expansion', sort_order = 540, status = 'planned', progress = 0, updated_at = NOW()
WHERE phase_key = '5.4';

UPDATE project_roadmap_phases
SET title = 'Phase 5.5 - Native Authentication System', sort_order = 550, status = 'planned', progress = 0, updated_at = NOW()
WHERE phase_key = '5.5';

-- Convert legacy final phases if present.
UPDATE project_roadmap_phases
SET phase_key = '5.6', title = 'Phase 5.6 - Release Readiness & Final Security Review', sort_order = 560, status = 'planned', progress = 0, updated_at = NOW()
WHERE phase_key IN ('6.0', 'release-readiness')
  AND title LIKE '%Release Readiness%';

UPDATE project_roadmap_phases
SET phase_key = '6.0', title = 'Phase 6.0 - Public Release', sort_order = 600, status = 'planned', progress = 0, updated_at = NOW()
WHERE phase_key IN ('7.0', 'public-release')
  AND title LIKE '%Public Release%';

-- Insert normalized final phases when they do not already exist.
-- If a pre-normalization Phase 6.0 placeholder remains, treat it as the public release phase.
UPDATE project_roadmap_phases
SET title = 'Phase 6.0 - Public Release', description = 'Guild CMS 1.0 public release, public documentation publication, download portal, and initial support period.', sort_order = 600, status = 'planned', progress = 0, updated_at = NOW()
WHERE phase_key = '6.0'
  AND title NOT LIKE '%Public Release%';

INSERT INTO project_roadmap_phases (phase_key, title, description, status, progress, sort_order, created_at, updated_at)
SELECT '5.6', 'Phase 5.6 - Release Readiness & Final Security Review', 'Release candidate validation, final security review, documentation review, installer validation, and readiness certification.', 'planned', 0, 560, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM project_roadmap_phases WHERE phase_key = '5.6');

INSERT INTO project_roadmap_phases (phase_key, title, description, status, progress, sort_order, created_at, updated_at)
SELECT '6.0', 'Phase 6.0 - Public Release', 'Guild CMS 1.0 public release, public documentation publication, download portal, and initial support period.', 'planned', 0, 600, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM project_roadmap_phases WHERE phase_key = '6.0');

-- Add Phase 4.4 installation architecture roadmap items.
INSERT INTO project_roadmap_items (phase_id, title, description, status, sort_order, created_at, updated_at)
SELECT p.id, 'Installation Architecture', 'Define installer boundary, bootstrap stage model, package sequence, and security expectations.', 'complete', 10, NOW(), NOW()
FROM project_roadmap_phases p
WHERE p.phase_key = '4.4'
  AND NOT EXISTS (
      SELECT 1 FROM project_roadmap_items i
      WHERE i.phase_id = p.id AND i.title = 'Installation Architecture'
  );

INSERT INTO project_roadmap_items (phase_id, title, description, status, sort_order, created_at, updated_at)
SELECT p.id, 'Requirements Checker', 'Validate PHP, extensions, filesystem permissions, database connectivity, and runtime prerequisites before installation.', 'planned', 20, NOW(), NOW()
FROM project_roadmap_phases p
WHERE p.phase_key = '4.4'
  AND NOT EXISTS (
      SELECT 1 FROM project_roadmap_items i
      WHERE i.phase_id = p.id AND i.title = 'Requirements Checker'
  );

INSERT INTO project_roadmap_items (phase_id, title, description, status, sort_order, created_at, updated_at)
SELECT p.id, 'Configuration Generator', 'Collect installation settings and generate safe configuration output for review.', 'planned', 30, NOW(), NOW()
FROM project_roadmap_phases p
WHERE p.phase_key = '4.4'
  AND NOT EXISTS (
      SELECT 1 FROM project_roadmap_items i
      WHERE i.phase_id = p.id AND i.title = 'Configuration Generator'
  );

INSERT INTO project_roadmap_items (phase_id, title, description, status, sort_order, created_at, updated_at)
SELECT p.id, 'Database Bootstrap', 'Create the schema baseline and migration records needed for a new Guild CMS installation.', 'planned', 40, NOW(), NOW()
FROM project_roadmap_phases p
WHERE p.phase_key = '4.4'
  AND NOT EXISTS (
      SELECT 1 FROM project_roadmap_items i
      WHERE i.phase_id = p.id AND i.title = 'Database Bootstrap'
  );

INSERT INTO project_roadmap_items (phase_id, title, description, status, sort_order, created_at, updated_at)
SELECT p.id, 'Site Bootstrap', 'Initialize site identity, base settings, public runtime defaults, and first-run state.', 'planned', 50, NOW(), NOW()
FROM project_roadmap_phases p
WHERE p.phase_key = '4.4'
  AND NOT EXISTS (
      SELECT 1 FROM project_roadmap_items i
      WHERE i.phase_id = p.id AND i.title = 'Site Bootstrap'
  );

INSERT INTO project_roadmap_items (phase_id, title, description, status, sort_order, created_at, updated_at)
SELECT p.id, 'Authentication Provider Bootstrap', 'Prepare installation-time selection and validation of the initial authentication provider.', 'planned', 60, NOW(), NOW()
FROM project_roadmap_phases p
WHERE p.phase_key = '4.4'
  AND NOT EXISTS (
      SELECT 1 FROM project_roadmap_items i
      WHERE i.phase_id = p.id AND i.title = 'Authentication Provider Bootstrap'
  );

INSERT INTO project_roadmap_items (phase_id, title, description, status, sort_order, created_at, updated_at)
SELECT p.id, 'Installer Security Review', 'Review installer forms, CSRF, output escaping, configuration secrets, lock behavior, and re-run safety.', 'planned', 70, NOW(), NOW()
FROM project_roadmap_phases p
WHERE p.phase_key = '4.4'
  AND NOT EXISTS (
      SELECT 1 FROM project_roadmap_items i
      WHERE i.phase_id = p.id AND i.title = 'Installer Security Review'
  );

-- Development Center entries.
INSERT INTO project_development_sessions
(session_date, title, phase_key, focus, completed, files_changed, next_steps, status, completed_at, created_by_user_id, created_by_username)
SELECT CURDATE(), 'Package 4.4.0-1 - Installation Architecture', '4.4',
       'Define the Guild CMS installer architecture and bootstrap framework boundary.',
       'Activated Phase 4.4, defined the installer stage model, updated public and Development Center roadmap metadata, and documented installer security expectations.',
       'Development Center roadmap, public roadmap, timeline, journal, changelog, release history, SQL migration, and installation architecture documentation.',
       'Begin the requirements checker foundation package.',
       'complete', NOW(), 0, 'System'
WHERE NOT EXISTS (
    SELECT 1 FROM project_development_sessions
    WHERE title = 'Package 4.4.0-1 - Installation Architecture'
);

INSERT INTO project_changelog_entries
(entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username)
SELECT CURDATE(), 'Package 4.4.0-1 - Installation Architecture',
       'Opened Phase 4.4 - Installation & Bootstrap System. Defined the Guild CMS installer boundary, bootstrap stage model, package sequence, and security expectations. Updated roadmap metadata and public project pages.',
       '4.4', 'release', 1, 0, 'System'
WHERE NOT EXISTS (
    SELECT 1 FROM project_changelog_entries
    WHERE title = 'Package 4.4.0-1 - Installation Architecture'
);

COMMIT;
