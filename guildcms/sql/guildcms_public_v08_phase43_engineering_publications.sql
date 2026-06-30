-- Guild CMS Public Site
-- Package 4.3.0-3: Engineering Library Publication Foundation
-- Adds the public Engineering Library publication framework and records the package in the public changelog.
-- No schema changes are performed.

START TRANSACTION;

UPDATE project_roadmap_phases
SET title = 'Phase 4.3 — Engineering Foundation & Governance',
    description = 'Introduces the Guild CMS Engineering Library, engineering governance, architecture standards, documentation standards, and public engineering documentation framework.',
    status = 'in_progress',
    progress = 25,
    sort_order = 430
WHERE phase_key IN ('4.3', 'phase_4_3', 'phase-4-3')
   OR title LIKE 'Phase 4.3%';

UPDATE project_roadmap_phases
SET title = 'Phase 4.4 — Installation & Bootstrap System',
    description = 'Installer, bootstrap workflow, environment checks, initial configuration, and deployment preparation.',
    status = 'planned',
    progress = 0,
    sort_order = 440
WHERE phase_key IN ('4.4', 'phase_4_4', 'phase-4-4')
   OR title LIKE 'Phase 4.4%'
   OR title LIKE '%Installation & Bootstrap%';

INSERT INTO project_changelog_entries (phase_key, entry_date, title, body, is_public)
SELECT '4.3.0-3', CURDATE(), 'Engineering Library publication framework established', 'The Engineering Library now uses a formal public publication model with stable identifiers, shared metadata, library navigation, breadcrumbs, the published Founder''s Note, and reserved volume pages for future engineering standards.', 1
WHERE NOT EXISTS (
    SELECT 1
    FROM project_changelog_entries
    WHERE phase_key = '4.3.0-3'
      AND title = 'Engineering Library publication framework established'
);

COMMIT;
