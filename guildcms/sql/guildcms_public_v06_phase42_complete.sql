-- Guild CMS Public Site v0.6 - Phase 4.2 Complete
-- Applies the shared roadmap/changelog/vision updates used by the public site.
-- Run from the public site root or public_html with:
-- mysql -u theregs_admin -p theregs_site < sql/guildcms_public_v06_phase42_complete.sql

UPDATE project_roadmap_phases
SET
    status = 'complete',
    progress = 100,
    completed_at = CURDATE(),
    description = 'Security Hardening is complete. Phase 4.2 established the Guild CMS security baseline: centralized application-layer security headers, Report-Only CSP, cookie/session review, CSRF review, upload and file security review, filesystem permission cleanup, gallery upload hardening, and final phase security review.'
WHERE phase_key = '4.2';

UPDATE project_roadmap_phases
SET
    status = 'in_progress',
    progress = GREATEST(progress, 5),
    started_at = COALESCE(started_at, CURDATE()),
    title = 'Installation & Bootstrap System',
    description = 'Build the installer and bootstrap path that will allow The Guild CMS to move beyond the flagship TheRegs.org installation and become installable as a platform.'
WHERE phase_key = '4.3';

UPDATE project_roadmap_items i
INNER JOIN project_roadmap_phases p ON p.id = i.phase_id
SET i.status = 'complete', i.progress = 100
WHERE p.phase_key = '4.2';

INSERT INTO project_changelog_entries
    (entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Phase 4.2 Security Hardening Complete',
    'Phase 4.2 Security Hardening has been completed. The Guild CMS now has a documented security baseline including centralized application-layer security headers, Content-Security-Policy Report-Only, cookie and session audit, CSRF audit, upload and file security audit, filesystem permission cleanup, gallery upload hardening, legacy cleanup, and final security review. Development now moves to Phase 4.3: Installation & Bootstrap System.',
    '4.2',
    'milestone',
    1,
    0,
    'System'
WHERE NOT EXISTS (
    SELECT 1
    FROM project_changelog_entries
    WHERE phase_key = '4.2'
      AND title = 'Phase 4.2 Security Hardening Complete'
);

INSERT INTO project_changelog_entries
    (entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Phase 4.3 Installation & Bootstrap System Begins',
    'With Security Hardening complete, The Guild CMS begins Phase 4.3. This phase focuses on the installation and bootstrap system: requirements checks, configuration generation, database setup, first-run setup, installer safety, and the path toward making the CMS portable beyond the flagship TheRegs.org installation.',
    '4.3',
    'milestone',
    1,
    0,
    'System'
WHERE NOT EXISTS (
    SELECT 1
    FROM project_changelog_entries
    WHERE phase_key = '4.3'
      AND title = 'Phase 4.3 Installation & Bootstrap System Begins'
);

INSERT INTO project_vision_notes
    (title, category, body, status, sort_order, is_public)
SELECT
    'Security-First Engineering',
    'Engineering Standards',
    'The Guild CMS now treats security review as a phase completion gate. Each major development phase concludes with a security review, documented findings, remediation where needed, Development Center updates, and public roadmap updates. Before public release, Phase 6.0 will perform a final platform-wide security and release-readiness review.',
    'active',
    35,
    1
WHERE NOT EXISTS (
    SELECT 1
    FROM project_vision_notes
    WHERE title = 'Security-First Engineering'
      AND category = 'Engineering Standards'
);
