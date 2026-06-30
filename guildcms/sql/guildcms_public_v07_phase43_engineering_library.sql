-- Guild CMS Public Site
-- Package 4.3.0-2: Public Site Roadmap and Engineering Library Foundation
-- Applies the revised roadmap after Package 4.3.0-1 realigned the Development Center.
-- Review table/column names before applying if your public roadmap schema has been customized.

START TRANSACTION;

UPDATE project_roadmap_phases
SET status = 'complete', progress = 100
WHERE phase_key IN ('4.1', 'phase_4_1', 'phase-4-1')
   OR title LIKE 'Phase 4.1%';

UPDATE project_roadmap_phases
SET title = 'Phase 4.2 — Security Hardening',
    description = 'Security hardening completed: headers, CSP, upload hardening, CSRF review, permissions review, and public security posture improvements.',
    status = 'complete',
    progress = 100,
    sort_order = 420
WHERE phase_key IN ('4.2', 'phase_4_2', 'phase-4-2')
   OR title LIKE 'Phase 4.2%';

UPDATE project_roadmap_phases
SET title = 'Phase 4.3 — Engineering Foundation & Governance',
    description = 'Introduces the Guild CMS Engineering Library, engineering governance, architecture standards, documentation standards, and public engineering documentation framework.',
    status = 'in_progress',
    progress = 5,
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

UPDATE project_roadmap_phases
SET title = 'Phase 4.5 — Upgrade & Migration Framework',
    description = 'Upgrade workflow, migration runner, versioned schema changes, and safe platform update procedures.',
    status = 'planned',
    progress = 0,
    sort_order = 450
WHERE phase_key IN ('4.5', 'phase_4_5', 'phase-4-5')
   OR title LIKE 'Phase 4.5%'
   OR title LIKE '%Upgrade & Migration%';

INSERT INTO project_changelog_entries (phase_key, entry_date, title, body, is_public)
SELECT '4.3.0-2', CURDATE(), 'Public site prepared for Engineering Library', 'Public navigation, footer resources, roadmap text, documentation links, Engineering Library landing page, Founder''s Note, and placeholder volume pages were added for Phase 4.3.', 1
WHERE NOT EXISTS (
    SELECT 1 FROM project_changelog_entries WHERE phase_key = '4.3.0-2' AND title = 'Public site prepared for Engineering Library'
);

COMMIT;
