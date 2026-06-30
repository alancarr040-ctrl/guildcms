# GCMS-ENG-011 – User Experience & Educational Design Principles

**Status:** Published  
**Version:** 1.0  
**Phase:** 4.4.0-3  
**Applies to:** Guild CMS v0.9+ and Phase 4.4 installer work

## Purpose

GCMS-ENG-011 defines how Guild CMS should interact with administrators, site owners, developers, and contributors. Guild CMS should not simply complete tasks. It should explain what is happening, teach as it guides, and leave people more confident than when they began.

Guild CMS should be educational, professional, modern, and accessible.

## Core Product Experience Principles

1. **Explain before asking.** Explain what information is needed, why it is needed, where it can usually be found, and what will happen next.
2. **Teach, do not assume.** Do not assume the administrator already understands databases, PHP extensions, file permissions, sessions, HTTPS, modules, or configuration files.
3. **Build confidence.** A successful feature leaves the person more confident, not merely with a completed action.
4. **Do not blame.** Error messages should describe what happened and how to fix it without making the administrator feel at fault.
5. **Make recovery safe.** Back, cancel, refresh, and resume should be intentional parts of the experience.
6. **Documentation complements the software.** Documentation teaches depth; the software itself must provide enough guidance for common tasks.

## Installer Experience

The installer is the first Guild CMS experience many administrators will ever see. It is both an introduction and an installation medium.

The planned installer flow is:

1. Welcome
2. Requirements
3. Recommended Features
4. License
5. Database
6. Database Issues, when needed
7. Configuration
8. Administration
9. Site Settings
10. Modules
11. Summary
12. Install
13. Complete

The installer must allow safe back navigation, saving progress, canceling before permanent changes occur, resuming saved progress, refreshing after correcting issues, and viewing meaningful progress during the install phase.

## System Readiness

Required environment checks should happen before the administrator spends time entering configuration, database, or account details. If Guild CMS requires something in order to run, the administrator should know immediately.

Required checks block installation when they fail. Recommended checks explain useful capabilities that improve the experience but do not block installation.

## First-Run Behavior

The installer should generate `includes/config.inc.php`. A fresh installable package should not depend on confusing placeholder production configuration. If a new Guild CMS site is opened before setup is complete, `index.php` should explain that Guild CMS is not installed yet and direct the administrator to `/install/` instead of failing with raw PHP or database errors.

## Product Identity

Guild CMS exists to help people succeed, not merely to complete tasks. The installer, Administration Center, documentation, public site, and Development Center should all reflect the same values: clarity, respect, education, confidence, and accessibility.

## Progressive Disclosure and Server Knowledge

Guild CMS must never assume prior Linux, hosting, or server administration knowledge.

When the installer introduces technical concepts such as filesystem paths, PHP users, directory ownership, or permissions, it must explain:

1. What the item is.
2. Why Guild CMS needs it.
3. Where it is located, when applicable.
4. How to correct it if something needs attention.

Technical details should not be removed. They should be presented through progressive disclosure: a clear explanation first, followed by expandable implementation details for administrators who need or want them.

This principle exists because many administrators encounter instructions such as "edit this file" or "change ownership" without being told where the file lives, who PHP is running as, or which account needs permission. Guild CMS should avoid that failure mode by teaching the context directly inside the installer.
