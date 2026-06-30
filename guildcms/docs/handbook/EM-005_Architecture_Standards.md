# GCMS-ENG-004 — Architecture Standards

**Publication:** Publication 4  
**Volume:** Volume IV  
**Version:** 1.0  
**Status:** Published  
**Phase:** 4.3.0-6  
**First Published:** June 2026  
**Applies To:** Guild CMS v0.9+  
**Classification:** Public Engineering Publication

## Preamble

Architecture Standards defines the structural rules used to design, extend, review, document, and evolve Guild CMS.

Guild CMS is intended to become a secure, modular, extensible platform for communities that may operate for many years. Architecture Standards exists so that new features, modules, providers, themes, plugins, and administrative tools are built with consistent structure instead of isolated one-off solutions.

## 1. Architectural Philosophy

Architecture must serve maintainability. Guild CMS architecture should favor structures that future maintainers can understand, test, document, and safely extend.

Features should be composed from focused responsibilities rather than large multi-purpose files. Controllers, views, data access, assets, configuration, and documentation should remain distinguishable wherever practical.

Important architecture decisions should be visible through the Engineering Library, Development Center, release notes, or Architecture Decision Records.

## 2. System Architecture

The public Guild CMS site is the authoritative publication surface for public project knowledge. The Development Center is the engineering management system for planning, status, metadata, release history, and internal project visibility.

Public pages, administrative pages, shared core functions, data access, and future extension systems should remain logically separated.

Shared functionality should be moved into predictable includes, services, helpers, providers, or future core components rather than copied across unrelated pages.

## 3. Module Architecture

A module should represent a recognizable feature area with clear ownership of controllers, display logic, assets, configuration, database migrations, and documentation.

Modules should follow a repeatable structure for entry points, includes, templates, assets, SQL, verification steps, and documentation where practical.

Modules should communicate through documented interfaces, shared services, events, hooks, or provider contracts rather than direct assumptions about another module's internal files or database queries.

## 4. Provider Architecture

A provider represents a replaceable implementation of a platform capability. Authentication, storage, search, cache, logging, notifications, uploads, and future AI-assisted services are examples of capabilities that may benefit from provider boundaries.

Provider contracts should document inputs, outputs, failure behavior, security expectations, configuration requirements, and compatibility concerns.

Provider abstraction must not hide authentication, authorization, validation, escaping, or data ownership responsibilities.

## 5. Data Layer Standards

Database access must be reviewable. Queries should be explicit, parameterized where user input is involved, and understandable during review.

Database changes should be provided as SQL scripts with release documentation and verification steps when practical.

Tables, records, and configuration values should have an identifiable owner or feature area. Shared tables should document their consumers and intended use.

## 6. User Interface Architecture

Guild CMS should use consistent page layouts, navigation patterns, cards, tables, forms, badges, and status indicators.

Public and administrative interfaces should be responsive and usable across desktop and mobile layouts.

Accessibility should be considered during design, including headings, navigation, links, form labels, color contrast, and status indicators.

## 7. Security Architecture

Requests, forms, sessions, authentication state, administrative access, uploaded files, database content, and external integrations must be treated as separate trust boundaries.

Administrative and destructive actions should verify authorization near the action itself rather than relying only on navigation visibility or earlier page routing.

Security review evaluates trust boundaries, data flow, dependencies, permissions, configuration, and operational exposure.

## 8. Plugin Architecture

Plugins should extend Guild CMS through documented extension points, events, hooks, providers, or APIs. Plugins should not require modification of unrelated core files to function.

Discovery, installation, activation, configuration, dependency handling, upgrade, disablement, and removal should be documented before plugins become a supported extension mechanism.

Future plugin tooling should expose compatibility, permissions, dependencies, and trust information where practical.

## 9. Theme Architecture

Themes should control presentation, layout, visual identity, assets, and template overrides. They should not become hidden controllers or duplicate platform logic.

Template overrides, child themes, and asset replacement should have documented rules so visual customization does not break upgrades or security expectations.

## 10. Documentation Architecture

Major features, architecture decisions, standards, migrations, and release packages should include documentation that explains intent, installation, verification, and long-term maintenance impact.

Engineering Library publications reside on the public Guild CMS site. The Development Center tracks metadata, status, and links but should not duplicate publication content.

Every Guild CMS release package should include README, Release Notes, Implementation Guide, Security Review, required SQL, verification scripts, and a package manifest.

## 11. Architecture Decision Records

When an architecture decision has long-term impact, meaningful tradeoffs, or future compatibility implications, it should be recorded as an Architecture Decision Record.

An ADR should explain the problem, context, decision, alternatives considered, consequences, status, and related publications or packages.

## 12. Architecture Evolution

Guild CMS architecture may change as the platform matures. Evolution should be intentional, documented, and compatible with the Constitution and Engineering Principles.

Existing installations, legacy URLs, data structures, and administrator workflows should not be broken casually. Breaking changes require justification, release notes, and migration guidance.

Refactoring should reduce risk, improve maintainability, clarify boundaries, or prepare for documented future work.

## Architecture Compliance Matrix

| Standard | Level | Notes |
| --- | --- | --- |
| Public Site / Development Center separation | Required | Public documents live on the public site; the Development Center tracks metadata. |
| Release documentation with each package | Required | README, Release Notes, Implementation Guide, Security Review, SQL, and manifest where applicable. |
| Parameterized database access for request-driven queries | Required | Detailed rules belong in Coding and Security Standards. |
| Module boundary documentation | Recommended | Required for large modules and future plugin-facing systems. |
| Provider abstraction for replaceable capabilities | Recommended | Expected to become required for Phase 5.4 provider systems. |
| Architecture Decision Records for major decisions | Recommended | Expected to become required for major platform decisions. |

## Related Publications

- **GCMS-ENG-001** — The Guild CMS Constitution
- **GCMS-ENG-002** — Vision & Mission
- **GCMS-ENG-003** — Engineering Principles
- **GCMS-ENG-005** — Developer Handbook (planned)
- **GCMS-ENG-007** — Coding Standards (planned)
- **GCMS-ENG-008** — Security Standards (planned)
- **GCMS-ADR-000** — Architecture Decision Records (planned)

## Publication Certification

**Publication:** GCMS-ENG-004  
**Title:** Architecture Standards  
**Version:** 1.0  
**Status:** Published  
**Approved During:** Phase 4.3  
**Maintained By:** Guild CMS Engineering
