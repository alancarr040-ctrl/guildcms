# Implementation Guide - 4.4.0-9

Apply the package by copying the changed files into the existing repository layout.

Expected roots:

```text
admin/
guildcms/
devsite/
sql/
```

Apply `sql/guildcms_4_4_0_9_installer_certification_publication.sql` after uploading the files to synchronize database-backed roadmap, journal, changelog, and phase status records.

After upload, verify:

1. Development Center loads without PHP errors.
2. Public Engineering Library lists GCMS-ENG-012, GCMS-ENG-013, and Installer Certification Milestone 1.
3. Public roadmap shows Phase 4.4 completion messaging.
4. Public timeline includes the completed Phase 4.4 certification milestone.
5. `devsite/docs/certifications/` contains the certification report set.
