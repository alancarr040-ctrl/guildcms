# The Guild CMS Public Site v0.1

Target subdomain:

```text
guildcms.theregs.org
```

Recommended install path:

```text
/home/theregs/domains/guildcms.theregs.org/public_html/
```

## Upload

Upload the contents of this ZIP into the subdomain public_html folder.

## Configure

Edit:

```text
includes/config.php
```

If needed, update:

```php
const GUILD_CMS_MAIN_CONFIG = '/home/theregs/public_html/includes/config.inc.php';
```

## Database

Run:

```bash
mysql -u DB_USER -p DB_NAME < sql/guildcms_public_visibility.sql
```

## Verify

```bash
php -l index.php
php -l roadmap.php
php -l changelog.php
php -l vision.php
php -l docs.php
php -l includes/db.php
php -l includes/header.php
php -l includes/footer.php
```

## Public pages

```text
/
 /roadmap.php
 /changelog.php
 /vision.php
 /docs.php
```

This is read-only and displays public-safe roadmap/changelog/vision records.
