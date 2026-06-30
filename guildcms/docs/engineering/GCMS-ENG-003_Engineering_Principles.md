# GCMS-ENG-003 — Engineering Principles

**Publication:** Publication 3  
**Volume:** Volume III  
**Version:** 1.0  
**Status:** Published  
**Phase:** 4.3.0-5  
**First Published:** June 2026  
**Applies To:** Guild CMS v0.9+  
**Classification:** Public Engineering Publication

## Preamble

Engineering Principles defines the practical rules of judgment used when Guild CMS code, documentation, architecture, security, and releases are planned, reviewed, and maintained.

The Guild CMS Constitution defines the project's enduring commitments. Vision & Mission defines where the project is going and who it exists to serve. Engineering Principles converts those commitments into practical guidance for daily engineering work.

## 1. Principle of Security First

Security is a design requirement. Guild CMS work should consider security during design, implementation, review, installation, upgrade, and operation.

Guild CMS should prefer secure defaults, defense in depth, least privilege, validation, escaping, reviewable SQL, and clear security documentation.

## 2. Principle of Maintainability

Code should be written for future maintainers. Clear names, predictable structure, documented assumptions, and focused changes are preferred over cleverness.

Important behavior should not exist only in memory, chat history, or scattered comments.

## 3. Principle of Documentation as Deliverable

Documentation is part of the work. A feature, release, standard, or migration is not complete until the documentation needed to understand, install, test, or maintain it has been provided.

The public Guild CMS website remains the authoritative home for published Engineering Library documents. The Development Center tracks publication metadata and status.

## 4. Principle of Incremental Change

Guild CMS releases should prefer focused incremental packages over large mixed changes. Release packages should contain only changed files, new files, required SQL, verification scripts, and release documentation.

## 5. Principle of Reviewability

A reviewer should be able to understand what changed, why it changed, and what risk it introduces. Release notes and implementation guides exist to make that review possible.

Updates should preserve the existing architecture unless the package explicitly includes a refactor.

## 6. Principle of Separation of Responsibilities

The public Guild CMS site publishes official project knowledge. The Development Center manages engineering state, planning, package tracking, and publication metadata.

Published documents should not be duplicated between systems.

## 7. Principle of Compatibility and Migration

Existing installations matter. Guild CMS should avoid breaking existing installations without a documented reason, migration path, and release warning.

SQL changes should be explicit, reviewable, and accompanied by verification steps when practical.

## 8. Principle of Operational Clarity

Installation, upgrade, configuration, verification, and troubleshooting should become increasingly predictable as the platform matures.

Roadmap state, package status, security posture, publication status, and release progress should be visible through the Development Center or public project pages as appropriate.

## 9. Principle of Public Knowledge

The Engineering Library is the canonical public home for Guild CMS engineering publications, including the Constitution, Vision & Mission, Engineering Principles, standards, and decision records.

Engineering publications should use stable identifiers and section numbering so future documents, release notes, issues, and decisions can cite them reliably.

## 10. Principle of Stewardship

Guild CMS should be built as a system that can be inherited by future administrators, contributors, and communities.

Every package should leave the project easier to understand, safer to operate, or better prepared for future development.

## Publication Certification

**Publication:** GCMS-ENG-003  
**Title:** Engineering Principles  
**Version:** 1.0  
**Status:** Published  
**Approved During:** Phase 4.3  
**Maintained By:** Guild CMS Engineering
