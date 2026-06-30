# GCMS-ENG-008: Security Standards

Version: 1.0  
Status: Published  
Phase: 4.3.0-9  
Classification: Public Engineering Publication / Security Standard

Security Standards defines the minimum security expectations for Guild CMS development.

## Core Requirements

- Validate input before use.
- Escape output according to context.
- Protect state-changing requests with CSRF tokens.
- Enforce authentication and authorization boundaries.
- Use prepared statements for dynamic SQL.
- Review upload, filesystem, session, cookie, and header behavior.
- Include a security review with every release package.

The public canonical version is available at:

https://guildcms.theregs.org/engineering/security-standards.php
