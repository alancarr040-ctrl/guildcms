-- Guild CMS Public Site v0.5 - Phase 4.2 Progress Update
-- Apply from the public Guild CMS site root if you want the database-backed roadmap/changelog to match this public update.

UPDATE project_roadmap_phases
SET
    status = 'in_progress',
    progress = 95,
    description = 'Security hardening is in final review after completing security headers, cookie/session review, CSRF review, CSP Report-Only rollout, upload/file review, filesystem permission cleanup, and Gallery upload hardening.',
    sort_order = 42,
    is_public = 1
WHERE phase_key = '4.2';

UPDATE project_roadmap_phases
SET
    title = 'Installation & Bootstrap System',
    status = 'planned',
    progress = 0,
    sort_order = 43,
    is_public = 1
WHERE phase_key = '4.3';

UPDATE project_roadmap_phases
SET
    title = 'Release Readiness & Final Security Review',
    description = 'Complete the final platform-wide security audit, release readiness review, documentation review, installer validation, upgrade validation, and release candidate sign-off before public release.',
    status = 'planned',
    progress = 0,
    sort_order = 60,
    is_public = 1
WHERE phase_key = '6.0';

UPDATE project_roadmap_phases
SET
    title = 'Public Release',
    description = 'Public release of The Guild CMS after release readiness and final security review are complete.',
    status = 'planned',
    progress = 0,
    sort_order = 70,
    is_public = 1
WHERE phase_key = '7.0';

INSERT INTO project_changelog_entries
    (entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username)
SELECT
    CURDATE(),
    'Security Hardening Enters Final Review',
    'Phase 4.2 completed the main security audits and remediation work: centralized security headers, CSP Report-Only rollout, cookie/session review, CSRF review, upload/file security review, filesystem permission cleanup, legacy phpBB Gallery cleanup, and Gallery upload hardening. The remaining step is the final Phase 4.2 security review before Phase 4.3 begins.',
    '4.2',
    'milestone',
    1,
    0,
    'Guild CMS'
WHERE NOT EXISTS (
    SELECT 1 FROM project_changelog_entries
    WHERE title = 'Security Hardening Enters Final Review'
      AND phase_key = '4.2'
);
