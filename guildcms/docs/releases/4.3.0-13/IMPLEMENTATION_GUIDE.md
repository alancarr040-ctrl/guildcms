# Implementation Guide - Package 4.3.0-13

## 1. Backup

Before applying this package, back up the current admin files, public Guild CMS site files, and database.

## 2. Upload Files

Upload the changed files from this package to the matching locations in the current admin and public Guild CMS site trees.

This package is changed-files-only. It is not a full replacement source tree.

## 3. Apply SQL

Run one SQL migration against the Development Center/Public Guild CMS database.

Admin copy:

```bash
mysql -u DB_USER -p DB_NAME < admin/sql/phase4_3_0_13_phase_realignment.sql
```

Public site copy:

```bash
mysql -u DB_USER -p DB_NAME < sql/guildcms_public_v09_phase43_phase_realignment.sql
```

Do not run both copies against the same database unless you are intentionally re-running the migration. The migration is written to be safe to re-run, but one run is sufficient.

## 4. Verify Development Center

Open:

```text
/admin/?page=development&tab=roadmap
```

Confirm:

- Phase 4.3 is Engineering Foundation & Governance.
- Phase 4.3 is complete.
- Installer/bootstrap milestones are not listed under Phase 4.3.
- Phase 4.4 is Installation & Bootstrap System.
- Phase 4.4 contains the installer/bootstrap milestone list.

## 5. Verify Public Site

Open:

```text
/roadmap.php
/timeline.php
/release-history.php
```

Confirm the public roadmap matches the Development Center roadmap boundary.

## 6. Verify Changelog and Journal

Open the Development Center changelog, journal, and timeline pages and confirm Package 4.3.0-13 is recorded.

## 7. Rollback

If rollback is required, restore the file and database backups taken before applying this package.
