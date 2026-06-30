# Guild CMS Installation Architecture

Package: 4.4.0-1  
Phase: 4.4 - Installation & Bootstrap System  
Status: Defined

## Purpose

This document defines the installation architecture for Guild CMS before executable installer screens, database writes, or configuration-generation actions are implemented.

The installer targets the reusable Guild CMS product. The public Guild CMS information site is a documentation and branding reference only and is not the install target.

## Installer Boundary

The installer must be isolated from normal public runtime behavior. Future installer files should live under a dedicated installation path and should be disabled or locked after installation is complete.

The installer is responsible for preparing a Guild CMS installation, not for importing TheRegs.org-specific content or cloning the current public information site.

## Bootstrap Stage Model

1. Preflight requirements check
2. Environment and filesystem validation
3. Configuration collection and generation
4. Database connectivity validation
5. Schema bootstrap and migration baseline
6. Site identity bootstrap
7. Authentication provider bootstrap
8. Installer lock and post-install review

## Security Expectations

- Requirements checks run before configuration or database writes.
- Installer output must escape all displayed values.
- Installer forms must use CSRF protection.
- Secrets must never be written to public documentation, logs, or rendered pages.
- Configuration generation must preserve least-privilege file permissions.
- The installer must provide a lock mechanism before normal use.
- Re-running installer stages must be safe where practical and explicitly blocked where unsafe.

## Phase 4.4 Package Direction

Future Phase 4.4 packages should build from this architecture in small steps:

- 4.4.0-2: Requirements checker foundation
- 4.4.0-3: Environment and filesystem checks
- 4.4.0-4: Configuration generator design
- 4.4.0-5: Database bootstrap planning
- 4.4.0-6: Site bootstrap model
- 4.4.0-7: Authentication provider bootstrap model
- 4.4.0-8: Installer security review
