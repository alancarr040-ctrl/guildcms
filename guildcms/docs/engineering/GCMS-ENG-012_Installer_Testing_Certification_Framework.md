# GCMS-ENG-012 – Installer Testing & Certification Framework

Version: 1.0  
Status: Published  
Phase: 4.4.0-6  
Applies To: Guild CMS Phase 4.4 installer development and future installer releases

## Purpose

GCMS-ENG-012 defines how Guild CMS validates the installation experience. The installer is not considered successful merely because code executes. It must also explain, guide, diagnose, recover, and help the administrator complete installation with confidence.

## Testing Philosophy

Guild CMS tests both the software and the experience. Every failure discovered during installer testing should become guidance for the next administrator.

The installer must avoid raw PHP failures wherever possible. If Guild CMS cannot continue, it should explain what happened, why it matters, what has or has not changed, and what to do next.

## Runtime Support Policy

Guild CMS distinguishes between compatibility, support, and certification.

| Runtime | Status | Meaning |
| --- | --- | --- |
| PHP 8.0 | Unsupported | Installer must show a friendly minimum-version message before loading incompatible framework code. |
| PHP 8.1 | Compatible | May run, but not part of official certification target. |
| PHP 8.2 | Supported | Minimum supported PHP runtime for the current development cycle. |
| PHP 8.3+ | Supported | Recommended modern runtime target as certification environments are added. |

The installer bootstrap must use conservative syntax until the PHP version check has completed.

## Certification Environments

Guild CMS certifies environments, not paid hosting panels. Control panels provision Apache, PHP, filesystems, databases, SSL, and virtual hosts. Guild CMS validates the resulting runtime.

Tier 1 certification targets:

- Rocky Linux 9, Virtualmin, Apache, PHP-FPM, MariaDB, PHP 8.2+
- Ubuntu Server 24.04 LTS, Apache, PHP-FPM, MariaDB, PHP 8.2+

Tier 2 targets:

- AlmaLinux 9
- Debian 12

Tier 3 future targets:

- Nginx deployments
- Containerized installations

## Base Expectation

The installer works correctly on a clean supported environment, never fails with raw PHP errors, and clearly explains any required issue that prevents installation from continuing.

## Perfect Expectation

The installer guides, teaches, diagnoses, recovers, resumes, and completes installation across multiple validated environments while preserving a professional, modern, accessible, and educational experience.

## Required Installer Test Cases

- Fresh installation with no configuration file
- Unsupported PHP version
- Supported PHP version with required extensions present
- Missing required PHP extension
- Missing recommended feature
- Unwritable configuration target
- Incorrect database host/name/user/password
- Successful database connection
- Configuration generation
- Administrator account creation
- Module selection and installation
- Installation progress screen
- Completion screen with links to the site and Administration Center

## Failure and Recovery Tests

- Refresh during each step
- Close browser after saving progress
- Resume saved installation
- Start over from saved installation
- Cancel before permanent writes
- Recheck after correcting server issue
- Recover from partial write failures in later installer phases

## User Experience Tests

Each installer page must be reviewed against GCMS-ENG-011:

- Does it explain before asking?
- Does it teach rather than assume?
- Does it avoid blame?
- Does it provide next steps?
- Does it make progress visible?
- Does it help the administrator recover?

## Security Tests

Installer certification must include configuration file handling, secret exposure, session state, CSRF protections, filesystem permissions, installer lockout/removal, and safe failure handling.

Sensitive values must not be displayed, logged, committed, or left in world-readable artifacts.

## Release Certification Checklist

- Development environment smoke test completed
- Clean Rocky Linux / Virtualmin installation test completed
- Ubuntu installation test completed when available
- Required checks pass or block with educational guidance
- Recommended checks warn without blocking
- Save, resume, cancel, back, refresh, and recheck behavior verified
- Accessibility review completed
- Security review completed

## Certification Records

The Development Center should record installer certification results by package, environment, PHP version, database, result, and known issues. Phase 4.5 data normalization should later formalize these records into a structured certification dashboard.
