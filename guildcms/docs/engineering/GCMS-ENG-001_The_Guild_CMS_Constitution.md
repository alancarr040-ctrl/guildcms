# GCMS-ENG-001 — The Guild CMS Constitution

**Publication:** Publication 1  
**Volume:** Volume I  
**Version:** 1.0  
**Status:** Published  
**Phase:** 4.3.0-4  
**First Published:** June 2026  
**Applies To:** Guild CMS v0.9+  
**Classification:** Public Engineering Publication

## Revision History

| Version | Date | Description |
|---|---|---|
| 1.0 | June 2026 | Initial publication of GCMS-ENG-001 during Phase 4.3. |

## Preamble

Guild CMS began as a practical effort to modernize and preserve a long-running community website. Over time, that effort became a broader platform: a content management system intended for guilds, gaming communities, and organizations that need durable, maintainable, extensible, and understandable software.

This Constitution defines the principles that guide Guild CMS as both software and engineering practice. It is not a feature list, implementation manual, or coding standard. It is the foundation against which future architecture, standards, release decisions, documentation, and stewardship responsibilities are measured.

Technologies will change. Interfaces will mature. Providers, themes, plugins, APIs, and installation systems will evolve. The principles established here are intended to remain stable even as the implementation grows.

## Section 1 — Purpose

### §1.1 Mission
Guild CMS exists to provide a secure, modular, extensible, and maintainable content management platform for guilds, gaming communities, and related organizations.

### §1.2 Engineering Purpose
The project exists not only to provide features, but to demonstrate disciplined engineering. Guild CMS values architecture, documentation, security, testability, and long-term clarity alongside user-facing capability.

### §1.3 Long-Term Scope
Guild CMS is intended to grow beyond a single installation. The platform shall be developed so that site-specific behavior, community-specific content, and reusable CMS capabilities can be separated over time.

### §1.4 Maintainability Over Convenience
Short-term convenience shall not take priority over long-term maintainability. Temporary decisions may be made when required, but they should be documented and revisited through the roadmap, Development Center, or Architecture Decision Records.

## Section 2 — Core Values

### §2.1 Security First
Security is a foundational requirement. Guild CMS shall prefer secure defaults, explicit authorization, careful input handling, safe output escaping, and regular review over assumptions of safety.

### §2.2 Engineering Excellence
Engineering quality is a product feature. Code should be understandable, reviewable, maintainable, and aligned with the published standards of the project.

### §2.3 Documentation as a Feature
Documentation is part of the deliverable. A change is not complete merely because code exists; the reasoning, usage, release impact, and maintenance expectations must be recorded where appropriate.

### §2.4 Transparency
Guild CMS shall favor visible decisions and public engineering rationale. Important architectural decisions should be documented so future maintainers understand why the system exists in its current form.

### §2.5 Modularity
The project shall prefer modular, replaceable, and well-bounded components over tightly coupled systems. Modules, providers, themes, plugins, and services should have clear responsibilities.

### §2.6 Continuous Improvement
Guild CMS shall improve through incremental packages, review, testing, documentation, and correction. Defects in code, architecture, documentation, or project data should be treated as opportunities to strengthen the platform.

## Section 3 — Engineering Governance

### §3.1 Development Center
The Development Center is the engineering management system for Guild CMS. It records project state, roadmap progress, publication metadata, development history, security posture, and engineering workflow.

### §3.2 Engineering Library
The Engineering Library is the authoritative public home for published engineering knowledge. It contains the Constitution, vision documents, standards, handbooks, Architecture Decision Records, and future engineering publications.

### §3.3 Separation of Record and Publication
The Development Center tracks publication metadata and project state. The public Guild CMS website hosts published engineering documents. Document bodies should not be duplicated between the two systems.

### §3.4 Roadmap Discipline
The roadmap shall describe the intended engineering path of the project. Roadmap changes should be deliberate, recorded, and synchronized between the Development Center and the public site.

### §3.5 Architecture Decision Records
Significant architecture decisions should be preserved as Architecture Decision Records when the decision has lasting impact, introduces tradeoffs, or affects future extension points.

## Section 4 — Standards

Guild CMS shall maintain and follow published standards for architecture, coding, security, documentation, releases, contribution, and other recurring engineering practices. Standards may evolve as the project matures, but changes should preserve backward understanding and explain their reasons.

## Section 5 — Security

Guild CMS shall prefer secure defaults, defense in depth, least privilege, and mandatory security review for packages affecting authentication, authorization, forms, uploads, configuration, database writes, file handling, or public exposure. Security defects shall be treated with priority and documented through remediation.

## Section 6 — Documentation

Major changes should include documentation appropriate to their impact. Engineering knowledge should not exist only in source code, private memory, or temporary conversation. Public engineering knowledge belongs in the Engineering Library when appropriate.

## Section 7 — Architecture

Guild CMS architecture should be modular, with clear boundaries, stable interfaces, replaceable components, public/private separation, and intentional legacy compatibility where required.

## Section 8 — Releases

Every Guild CMS release package should contain only the files changed by that release, required database migrations, release documentation, and verification guidance. Release packages should include a README, Release Notes, Implementation Guide, and Security Review.

## Section 9 — Contributors

Contributors should communicate respectfully, review constructively, understand existing architecture before replacing it, and make decisions based on evidence, maintainability, security, and project principles.

## Section 10 — Stewardship

Guild CMS is intended to outlive individual features, implementation details, and temporary technology choices. Contributors are stewards of the project and should preserve commitments to security, maintainability, transparency, documentation, and responsible engineering.

## Glossary

- **Development Center:** The administrative engineering management area used to track Guild CMS roadmap state, engineering metadata, package progress, and internal project records.
- **Engineering Library:** The public Guild CMS publication area containing engineering documents, standards, governance references, and architecture records.
- **Engineering Publication:** A versioned public document with a stable identifier, metadata, publication status, and maintained content.
- **Release Package:** An incremental ZIP package containing changed files, SQL scripts when required, release documentation, and verification guidance.
- **Architecture Decision Record:** A document that records an important architecture decision, its context, tradeoffs, outcome, and status.

## Related Publications

- GCMS-ENG-000 — Founder’s Note
- GCMS-ENG-002 — Vision & Mission (planned)
- GCMS-ENG-003 — Engineering Principles (planned)
- GCMS-ENG-004 — Architecture Standards (planned)
- GCMS-ENG-008 — Security Standards (planned)

## Publication Certification

**Publication:** GCMS-ENG-001  
**Title:** The Guild CMS Constitution  
**Version:** 1.0  
**Status:** Published  
**Approved During:** Phase 4.3 — Engineering Foundation & Governance  
**Package:** 4.3.0-4  
**Maintained By:** Guild CMS Engineering
