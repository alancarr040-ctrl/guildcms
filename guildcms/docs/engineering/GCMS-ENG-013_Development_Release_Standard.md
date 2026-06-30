# GCMS-ENG-013 – Guild CMS Development & Release Standard

**Publication:** Publication 13  
**Volume:** Volume XIII  
**Version:** 1.0  
**Phase:** 4.4.0-7  
**Status:** Published  

## Purpose

GCMS-ENG-013 defines the official Guild CMS development and release standard. It captures the package workflow, source baseline rules, folder layout, SQL migration expectations, release documentation requirements, roadmap semantics, validation gates, package manifests, and Git baseline policy used for Guild CMS development.

The standard exists so future package requests can focus on what needs to be built while this publication defines how the work is delivered.

## Authoritative Source Baseline

Package work is built against the current tested repository export containing:

```text
admin/
guildcms/
devsite/
```

The `admin/` tree is the Development Center. The `guildcms/` tree is the public Guild CMS project and documentation website. The `devsite/` tree is the installable Guild CMS product.

## Package Layout

Packages must contain only changed files and preserve repository-root layout:

```text
admin/
guildcms/
devsite/
sql/
README.md
RELEASE_NOTES.md
IMPLEMENTATION_GUIDE.md
SECURITY_REVIEW.md
PACKAGE_MANIFEST.md
```

Packages must not include extra nesting such as `admin/admin/`, `guildcms/guildcms/`, `devsite/devsite/`, or an unnecessary package folder above the project layout.

## Development Workflow

1. Confirm phase, package number, and deliverable.
2. Build against the current authoritative baseline.
3. Modify actual source files.
4. Update Development Center and public documentation when applicable.
5. Create schema-compatible SQL migrations when database records must change.
6. Run PHP syntax validation on modified PHP files.
7. Package only changed files using the standard layout.
8. Test on the server before committing to Git.

## Release Documentation

Every package must include:

- `README.md`
- `RELEASE_NOTES.md`
- `IMPLEMENTATION_GUIDE.md`
- `SECURITY_REVIEW.md`
- `PACKAGE_MANIFEST.md`

Release documentation belongs at the package root unless it is intentionally being published to a site.

## SQL Migration Standard

SQL migrations must target the current schema, use valid enum values, avoid guessing columns, and prefer idempotent update/insert patterns. Shared project tables use a single shared migration unless separate databases are intentionally involved.

Roadmap updates must use canonical deliverable identifiers. Package numbers belong in history records, not as duplicate roadmap deliverables.

## Roadmap and History Semantics

- Roadmap: what exists.
- Timeline: when it happened.
- Journal: why it happened.
- Changelog: what changed.
- Release documentation: what shipped.

A revised publication updates the existing publication roadmap item rather than creating another roadmap item.

## Validation Gates

- Modified PHP files pass syntax checks.
- SQL is schema-compatible.
- Public site and Development Center references are synchronized.
- Installer packages are tested on development and clean certification environments when available.
- Packages avoid raw errors, broken links, duplicate roadmap entries, and accidental release-document publication.

## Git Baseline Policy

Git is the authoritative project history. Generated packages are applied to local working copies and test systems first. Only tested and accepted results should be committed and pushed.

## Package Manifest Requirement

Every package must include a manifest listing package number, deliverable, changed files, SQL files, PHP syntax checks, affected tables, expected validation, and recommended Git commit message.


## Package 4.4.0-8 Addendum: Environment-Aware Installer Packages

Installer implementation packages that add platform behavior must update the installer testing references and include validation notes for clean operating system environments. Packages should avoid assuming a hosting control panel. When platform-specific guidance is needed, it should be derived from detected operating system and package manager data.
