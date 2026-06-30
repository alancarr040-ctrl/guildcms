-- The Guild CMS public site visibility upgrade.
-- Safe to run after Development Center 0.2.

ALTER TABLE project_roadmap_phases
    ADD COLUMN IF NOT EXISTS is_public TINYINT(1) NOT NULL DEFAULT 1;

ALTER TABLE project_roadmap_items
    ADD COLUMN IF NOT EXISTS is_public TINYINT(1) NOT NULL DEFAULT 1;

ALTER TABLE project_ideas
    ADD COLUMN IF NOT EXISTS is_public TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE project_vision_notes
    ADD COLUMN IF NOT EXISTS is_public TINYINT(1) NOT NULL DEFAULT 1;

-- Make major public-safe milestone entries visible.
UPDATE project_changelog_entries
SET is_public = 1
WHERE title IN (
    'Completed Phase 4.1 Codebase Modernization',
    'Started Development Center'
);

UPDATE project_vision_notes
SET is_public = 1
WHERE status = 'active';
