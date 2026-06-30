# GCMS-ENG-007 — Coding Standards

Publication ID: GCMS-ENG-007  
Title: Coding Standards  
Version: 1.0  
Status: Published  
Phase: 4.3.0-8  
Classification: Public Engineering Publication

## Summary

Coding Standards defines the required coding conventions for PHP, SQL, HTML, CSS, JavaScript, naming, formatting, escaping, documentation, compatibility, and release quality across Guild CMS.

## Core Requirements

- Prefer clear maintainable code over clever code.
- Preserve existing architecture and coding style unless the package explicitly modernizes that area.
- Use prepared statements for dynamic SQL.
- Escape output according to context.
- Use CSRF protection for state-changing forms.
- Keep public Engineering Library documents on the public Guild CMS site and track metadata in the Development Center.
- Include release documentation and verification guidance with every package.
- Run PHP syntax validation on changed PHP files before release.

The canonical readable version is published at `/engineering/coding-standards.php`.
