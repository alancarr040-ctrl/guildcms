# Guild CMS Package 4.3.0-13 - Phase Realignment Final

## Purpose

Package 4.3.0-13 finalizes Phase 4.3 as **Engineering Foundation & Governance** and corrects the roadmap boundary before Phase 4.4 begins.

This package does not add installer code. It moves installer/bootstrap milestones out of Phase 4.3 and into Phase 4.4 so the next phase can begin cleanly as **Installation & Bootstrap System**.

## Included Areas

- Development Center roadmap/status updates
- Public Guild CMS roadmap/status updates
- Timeline update
- Development Journal update
- Changelog update
- SQL migration for roadmap, changelog, and journal/session records
- Release documentation

## Changed Files

### Admin / Development Center

- `admin/data/development_center_data.php`
- `admin/includes/phase_status.inc.php`
- `admin/pages/changelog.inc.php`
- `admin/pages/development_journal.inc.php`
- `admin/pages/timeline.inc.php`
- `admin/sql/phase4_3_0_13_phase_realignment.sql`

### Public Guild CMS Site

- `README.md`
- `roadmap.php`
- `release-history.php`
- `timeline.php`
- `includes/pages/changelog.php`
- `includes/pages/development_timeline.php`
- `includes/pages/roadmap.php`
- `sql/guildcms_public_v09_phase43_phase_realignment.sql`

### Package Documentation

- `docs/releases/4.3.0-13/README.md`
- `docs/releases/4.3.0-13/RELEASE_NOTES.md`
- `docs/releases/4.3.0-13/IMPLEMENTATION_GUIDE.md`
- `docs/releases/4.3.0-13/SECURITY_REVIEW.md`

## SQL

Run the SQL migration once after uploading files:

```bash
mysql -u DB_USER -p DB_NAME < admin/sql/phase4_3_0_13_phase_realignment.sql
```

For the public Guild CMS site tree, the matching copy is also available at:

```bash
mysql -u DB_USER -p DB_NAME < sql/guildcms_public_v09_phase43_phase_realignment.sql
```

Run only one copy against the same database.
