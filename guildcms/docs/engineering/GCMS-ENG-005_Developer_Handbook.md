# GCMS-ENG-005: Developer Handbook

**Publication:** Publication 5  
**Volume:** Volume V  
**Version:** 1.0  
**Status:** Published  
**Phase:** 4.3.0-7  
**Classification:** Public Engineering Publication  
**Applies To:** Guild CMS v0.9+

## Revision History

| Version | Date | Description |
|---|---|---|
| 1.0 | June 2026 | Initial publication as GCMS-ENG-005. |

## 1. Purpose

The Developer Handbook is the practical onboarding and day-to-day engineering guide for Guild CMS. It translates the Constitution, Vision & Mission, Engineering Principles, and Architecture Standards into working habits for developers.

## 2. Developer Orientation

Guild CMS is developed as a public-facing product, an administration platform, and an engineering project. Developers must keep those responsibilities separate:

- The public site publishes project knowledge and Engineering Library content.
- The Development Center tracks engineering state and workflow.
- Release packages carry tested incremental changes.

## 3. Source Structure

Developers should preserve the existing layout and avoid creating parallel systems. Public files belong in the public Guild CMS site. Admin and Development Center files belong in the admin tree. SQL migrations belong in package-level `sql/` directories. Release documentation belongs at the package root.

## 4. Engineering Workflow

The standard workflow is:

1. Architecture discussion
2. Development Center alignment
3. Implementation
4. Security review
5. Public site publication when applicable
6. Release package

## 5. Development Center Responsibilities

The Development Center is the engineering management system. It tracks status, phase, roadmap, publication metadata, timeline entries, journal entries, changelog entries, and package history. It should link to public Engineering Library documents rather than duplicate their content.

## 6. Public Site Responsibilities

The public Guild CMS site is the authoritative publication surface for Engineering Library content. Published documents should have stable URLs, metadata, revision history, table of contents, and readable public formatting.

## 7. Database and SQL Work

Database changes must be explicit. Packages that change data should include SQL scripts and verification scripts. Packages that do not require database changes should say so clearly.

## 8. Security Review Expectations

Every package requires a security review. The review must identify whether authentication, authorization, database writes, file uploads, output rendering, headers, user input, or public exposure changed.

## 9. Release Package Preparation

A release package should contain only the files changed by that package plus required SQL and documentation. Required documentation includes README, release notes, implementation guide, security review, and package manifest.

## 10. Maintenance and Stewardship

Developers are stewards of both the codebase and the engineering record. A correct implementation that leaves the roadmap, Development Center, SQL, changelog, or public documentation inconsistent is incomplete.

## Publication Certification

**Publication:** GCMS-ENG-005  
**Title:** Developer Handbook  
**Status:** Published  
**Maintained By:** Guild CMS Engineering
