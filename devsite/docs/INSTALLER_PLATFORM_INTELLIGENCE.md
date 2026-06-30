# Installer Platform Intelligence

Package: 4.4.0-8  
Status: Initial implementation  
Applies To: Guild CMS installer

## Purpose

Guild CMS detects the server environment before it asks configuration questions. The goal is not only to validate requirements, but to understand the context in which the administrator is installing Guild CMS.

## Detected Areas

- Operating system and distribution version
- Package manager family (`dnf`, `apt`, or unknown)
- Web server reporting and PHP SAPI
- PHP version, limits, timezone, and loaded extensions
- Database drivers such as MySQLi and PDO MySQL
- Filesystem readiness for configuration generation
- HTTPS request status
- SELinux and AppArmor visibility

## Design Rule

Detection is read-only. It must not write configuration, create database tables, or perform irreversible actions.

## Use in Later Steps

Later installer packages will use the stored environment snapshot to provide platform-specific guidance. For example, Rocky Linux and AlmaLinux guidance can reference `dnf`, while Ubuntu and Debian guidance can reference `apt`.

## Package 4.4.0-8a Usability Enhancements

Environment detection now uses progressive disclosure. The installer explains what was detected and why it matters before showing technical details.

Additional detection fields include:

- Effective PHP user and group
- Document root
- Includes directory owner and group
- Includes directory permissions
- Loaded php.ini and additional INI files

The includes directory is presented as an educational result rather than a raw filesystem row. Guild CMS explains that this directory is where `includes/config.inc.php` will be created, whether the directory is writable, and which system user PHP is running as. Technical filesystem paths remain available under expandable details so new administrators can learn where the files live without being overwhelmed.
