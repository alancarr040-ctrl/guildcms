-- Guild CMS Package 4.4.0-10
-- Phase Roadmap Realignment
-- Purpose: keep all installer lifecycle responsibilities in Phase 4.4 and restore Phase 4.5 as Data Normalization & Governance.

START TRANSACTION;

-- Resolve phase ids without assuming numeric ids.
SET @phase44_id := (SELECT id FROM project_roadmap_phases WHERE phase_key = '4.4' LIMIT 1);
SET @phase45_id := (SELECT id FROM project_roadmap_phases WHERE phase_key = '4.5' LIMIT 1);
SET @phase46_id := (SELECT id FROM project_roadmap_phases WHERE phase_key = '4.6' LIMIT 1);

-- Reopen Phase 4.4 as the active complete installer lifecycle.
UPDATE project_roadmap_phases
SET title = 'Phase 4.4 - Installation & Bootstrap System',
    description = 'Phase 4.4 owns the complete Guild CMS installer lifecycle: installer architecture, setup detection, product separation, platform intelligence, requirements validation, configuration generation, database bootstrap, database initialization, administrator account creation, first-run site configuration, plugin bootstrap responsibilities, site bootstrap, installer locking, recovery, certification, and security review.',
    status = 'in_progress',
    progress = 75,
    sort_order = 440,
    completed_at = NULL,
    updated_at = NOW(),
    is_public = 1
WHERE phase_key = '4.4';

-- Restore Phase 4.5 as post-installer data governance work.
UPDATE project_roadmap_phases
SET title = 'Phase 4.5 - Data Normalization & Governance',
    description = 'Normalize project data, document database schemas and enum values, establish database governance rules, and prepare reliable migrations after the installer lifecycle is complete.',
    status = 'planned',
    progress = 0,
    sort_order = 450,
    started_at = NULL,
    completed_at = NULL,
    updated_at = NOW(),
    is_public = 1
WHERE phase_key = '4.5';

-- Preserve Phase 4.6 as the upgrade and migration framework.
UPDATE project_roadmap_phases
SET title = 'Phase 4.6 - Upgrade & Migration Framework',
    description = 'Upgrade workflow, migration runner, versioned schema changes, and safe platform update procedures.',
    status = 'planned',
    progress = 0,
    sort_order = 460,
    updated_at = NOW(),
    is_public = 1
WHERE phase_key = '4.6';

-- Move any existing installer lifecycle records from Phase 4.5 to Phase 4.4.
UPDATE project_roadmap_items
SET phase_id = @phase44_id,
    updated_at = NOW(),
    is_public = 1
WHERE @phase44_id IS NOT NULL
  AND title IN (
    'Configuration Generator',
    'Configuration Generation',
    'Requirements & Validation',
    'Database Bootstrap',
    'Database Initialization',
    'Administrator Account Creation',
    'First-Run Site Configuration',
    'Plugin Manifest Format',
    'Plugin Discovery',
    'Hook/Event System',
    'Site Bootstrap',
    'Installation Locking and Recovery'
  );

-- Normalize duplicate/renamed installer records so the public roadmap uses the agreed package language.
-- If both labels exist, keep Configuration Generator and archive the alternate duplicate.
UPDATE project_roadmap_items
SET status = 'archived',
    progress = 0,
    updated_at = NOW(),
    is_public = 0
WHERE phase_id = @phase44_id
  AND title = 'Configuration Generation'
  AND EXISTS (
      SELECT 1 FROM (
          SELECT id FROM project_roadmap_items
          WHERE phase_id = @phase44_id AND title = 'Configuration Generator'
      ) AS existing_configuration_generator
  );

UPDATE project_roadmap_items
SET title = 'Configuration Generator'
WHERE phase_id = @phase44_id
  AND title = 'Configuration Generation'
  AND NOT EXISTS (
      SELECT 1 FROM (
          SELECT id FROM project_roadmap_items
          WHERE phase_id = @phase44_id AND title = 'Configuration Generator'
      ) AS existing_configuration_generator
  );

UPDATE project_roadmap_items
SET description = 'Collect installation settings and generate safe configuration output for review without requiring manual edits.',
    status = 'planned',
    progress = 0,
    sort_order = 451,
    updated_at = NOW(),
    is_public = 1
WHERE phase_id = @phase44_id
  AND title = 'Configuration Generator';

UPDATE project_roadmap_items
SET description = 'Validate PHP, required extensions, writable paths, sessions, filesystem behavior, database readiness, and installation safety before bootstrap work begins.',
    status = CASE WHEN status = 'complete' THEN status ELSE 'planned' END,
    sort_order = 450,
    updated_at = NOW(),
    is_public = 1
WHERE phase_id = @phase44_id AND title = 'Requirements & Validation';

UPDATE project_roadmap_items
SET description = 'Install schema, seed default data, prepare administrator creation, and validate database bootstrap safety.',
    status = CASE WHEN status = 'complete' THEN status ELSE 'planned' END,
    sort_order = 452,
    updated_at = NOW(),
    is_public = 1
WHERE phase_id = @phase44_id AND title = 'Database Bootstrap';

UPDATE project_roadmap_items
SET description = 'Create and seed the initial Guild CMS schema required for a new installation.',
    status = 'planned',
    progress = 0,
    sort_order = 453,
    updated_at = NOW(),
    is_public = 1
WHERE phase_id = @phase44_id AND title = 'Database Initialization';

UPDATE project_roadmap_items
SET description = 'Create the first administrator account or validate the selected authentication provider during installation.',
    status = 'planned',
    progress = 0,
    sort_order = 454,
    updated_at = NOW(),
    is_public = 1
WHERE phase_id = @phase44_id AND title = 'Administrator Account Creation';

UPDATE project_roadmap_items
SET description = 'Initialize site identity, base settings, default content, and initial public/admin runtime state.',
    status = 'planned',
    progress = 0,
    sort_order = 455,
    updated_at = NOW(),
    is_public = 1
WHERE phase_id = @phase44_id AND title = 'First-Run Site Configuration';

UPDATE project_roadmap_items
SET description = 'Define plugin metadata, compatibility, dependency, permissions, and lifecycle fields required during bootstrap.',
    status = 'planned',
    progress = 0,
    sort_order = 456,
    updated_at = NOW(),
    is_public = 1
WHERE phase_id = @phase44_id AND title = 'Plugin Manifest Format';

UPDATE project_roadmap_items
SET description = 'Discover installed plugins and validate manifests safely during installation and bootstrap.',
    status = 'planned',
    progress = 0,
    sort_order = 457,
    updated_at = NOW(),
    is_public = 1
WHERE phase_id = @phase44_id AND title = 'Plugin Discovery';

UPDATE project_roadmap_items
SET description = 'Provide controlled extension points for modules, themes, plugins, and installer/bootstrap events.',
    status = 'planned',
    progress = 0,
    sort_order = 458,
    updated_at = NOW(),
    is_public = 1
WHERE phase_id = @phase44_id AND title = 'Hook/Event System';

UPDATE project_roadmap_items
SET description = 'Create the initial site identity, default theme, admin access, and base content.',
    status = 'planned',
    progress = 0,
    sort_order = 459,
    updated_at = NOW(),
    is_public = 1
WHERE phase_id = @phase44_id AND title = 'Site Bootstrap';

UPDATE project_roadmap_items
SET description = 'Lock completed installations, prevent unsafe re-runs, and document recovery or reinstall workflows.',
    status = 'planned',
    progress = 0,
    sort_order = 460,
    updated_at = NOW(),
    is_public = 1
WHERE phase_id = @phase44_id AND title = 'Installation Locking and Recovery';

-- Insert any missing installer roadmap items. NOT EXISTS checks are title-based to keep this idempotent.
INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, created_at, updated_at, is_public)
SELECT @phase44_id, 'Configuration Generator', 'Collect installation settings and generate safe configuration output for review without requiring manual edits.', 'planned', 0, 451, NOW(), NOW(), 1
WHERE @phase44_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title IN ('Configuration Generator', 'Configuration Generation'));

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, created_at, updated_at, is_public)
SELECT @phase44_id, 'Requirements & Validation', 'Validate PHP, required extensions, writable paths, sessions, filesystem behavior, database readiness, and installation safety before bootstrap work begins.', 'planned', 0, 450, NOW(), NOW(), 1
WHERE @phase44_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Requirements & Validation');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, created_at, updated_at, is_public)
SELECT @phase44_id, 'Database Bootstrap', 'Install schema, seed default data, prepare administrator creation, and validate database bootstrap safety.', 'planned', 0, 452, NOW(), NOW(), 1
WHERE @phase44_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Database Bootstrap');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, created_at, updated_at, is_public)
SELECT @phase44_id, 'Database Initialization', 'Create and seed the initial Guild CMS schema required for a new installation.', 'planned', 0, 453, NOW(), NOW(), 1
WHERE @phase44_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Database Initialization');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, created_at, updated_at, is_public)
SELECT @phase44_id, 'Administrator Account Creation', 'Create the first administrator account or validate the selected authentication provider during installation.', 'planned', 0, 454, NOW(), NOW(), 1
WHERE @phase44_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Administrator Account Creation');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, created_at, updated_at, is_public)
SELECT @phase44_id, 'First-Run Site Configuration', 'Initialize site identity, base settings, default content, and initial public/admin runtime state.', 'planned', 0, 455, NOW(), NOW(), 1
WHERE @phase44_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'First-Run Site Configuration');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, created_at, updated_at, is_public)
SELECT @phase44_id, 'Plugin Manifest Format', 'Define plugin metadata, compatibility, dependency, permissions, and lifecycle fields required during bootstrap.', 'planned', 0, 456, NOW(), NOW(), 1
WHERE @phase44_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Plugin Manifest Format');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, created_at, updated_at, is_public)
SELECT @phase44_id, 'Plugin Discovery', 'Discover installed plugins and validate manifests safely during installation and bootstrap.', 'planned', 0, 457, NOW(), NOW(), 1
WHERE @phase44_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Plugin Discovery');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, created_at, updated_at, is_public)
SELECT @phase44_id, 'Hook/Event System', 'Provide controlled extension points for modules, themes, plugins, and installer/bootstrap events.', 'planned', 0, 458, NOW(), NOW(), 1
WHERE @phase44_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Hook/Event System');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, created_at, updated_at, is_public)
SELECT @phase44_id, 'Site Bootstrap', 'Create the initial site identity, default theme, admin access, and base content.', 'planned', 0, 459, NOW(), NOW(), 1
WHERE @phase44_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Site Bootstrap');

INSERT INTO project_roadmap_items (phase_id, title, description, status, progress, sort_order, created_at, updated_at, is_public)
SELECT @phase44_id, 'Installation Locking and Recovery', 'Lock completed installations, prevent unsafe re-runs, and document recovery or reinstall workflows.', 'planned', 0, 460, NOW(), NOW(), 1
WHERE @phase44_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM project_roadmap_items WHERE title = 'Installation Locking and Recovery');

-- Record the package in project history.
INSERT INTO project_changelog_entries
(entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username, created_at, updated_at)
SELECT '2026-07-01',
       'Package 4.4.0-10 - Phase Roadmap Realignment',
       'Phase 4.4 has been realigned to represent the complete installer lifecycle. Configuration generation, requirements validation, database bootstrap, database initialization, administrator account creation, first-run site configuration, plugin manifest format, plugin discovery, hook/event system, site bootstrap, and installation locking/recovery now remain in Phase 4.4. Phase 4.5 is restored as Data Normalization & Governance. No new installer runtime functionality was introduced in this package.',
       '4.4.0-10', 'milestone', 1, 0, 'Guild CMS', NOW(), NULL
WHERE NOT EXISTS (
    SELECT 1 FROM project_changelog_entries
    WHERE phase_key = '4.4.0-10'
      AND title = 'Package 4.4.0-10 - Phase Roadmap Realignment'
);

INSERT INTO project_development_sessions
(session_date, title, phase_key, focus, completed, files_changed, next_steps, status, started_at, completed_at, created_by_user_id, created_by_username, created_at, updated_at)
SELECT '2026-07-01',
       'Package 4.4.0-10 - Phase Roadmap Realignment',
       '4.4.0-10',
       'Realign the roadmap so Phase 4.4 owns the complete installer lifecycle.',
       'Moved installer lifecycle items from Phase 4.5 into Phase 4.4, restored Phase 4.5 as Data Normalization & Governance, and preserved normalized roadmap sort_order values.',
       'admin/, guildcms/, devsite/, sql/, release documentation',
       'Continue Phase 4.4 implementation against the complete installer lifecycle roadmap.',
       'complete', NULL, NOW(), 0, 'Guild CMS', NOW(), NULL
WHERE NOT EXISTS (
    SELECT 1 FROM project_development_sessions
    WHERE phase_key = '4.4.0-10'
      AND title = 'Package 4.4.0-10 - Phase Roadmap Realignment'
);

COMMIT;
