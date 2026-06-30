<?php
declare(strict_types=1);

namespace GuildCMS\Installer;

final class InstallerEnvironment
{
    /** @return array<int,array<string,string|bool>> */
    public static function requiredChecks(): array
    {
        return [
            self::check('PHP Version', version_compare(PHP_VERSION, '8.1.0', '>='), 'Guild CMS requires PHP 8.1 or newer for modern language features. Your server is running PHP ' . PHP_VERSION . '.', 'Ask your hosting provider to switch this site to PHP 8.1 or newer.'),
            self::check('JSON Extension', extension_loaded('json'), 'Guild CMS uses JSON for configuration, installer state, plugins, and API responses.', 'Enable the PHP JSON extension.'),
            self::check('MySQLi Extension', extension_loaded('mysqli'), 'Guild CMS uses MySQLi to connect to MariaDB/MySQL databases.', 'Enable the PHP MySQLi extension.'),
            self::check('Session Support', function_exists('session_start'), 'Sessions allow the installer to remember progress while setup is underway.', 'Enable PHP session support.'),
            self::check('Configuration Directory Writable', is_writable(GUILDCMS_ROOT . '/includes'), 'The installer must be able to create includes/config.inc.php during the install phase.', 'Make the includes directory writable by the web server for installation, then secure it afterward.'),
        ];
    }

    /** @return array<int,array<string,string|bool>> */
    public static function recommendedChecks(): array
    {
        $https = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') === '443');

        return [
            self::check('HTTPS', $https, 'HTTPS protects administrator logins and sensitive setup information.', 'Install an SSL/TLS certificate before putting the site into production.'),
            self::check('OPcache', extension_loaded('Zend OPcache') || extension_loaded('opcache'), 'OPcache improves PHP performance by caching compiled scripts.', 'Enable OPcache for better production performance.'),
            self::check('cURL', extension_loaded('curl'), 'cURL supports future update checks and integrations with external services.', 'Enable the PHP cURL extension if you plan to use integrations.'),
            self::check('Image Processing', extension_loaded('gd') || extension_loaded('imagick'), 'Image processing support helps with thumbnails, uploads, and media tools.', 'Enable GD or ImageMagick when media features are needed.'),
            self::check('ZIP Extension', extension_loaded('zip'), 'ZIP support will help future plugin, theme, backup, and package workflows.', 'Enable the PHP ZIP extension for package handling.'),
        ];
    }

    /** @param array<int,array<string,string|bool>> $checks */
    public static function hasBlockingFailures(array $checks): bool
    {
        foreach ($checks as $check) {
            if (empty($check['passed'])) {
                return true;
            }
        }

        return false;
    }

    /** @return array<string,string|bool> */
    private static function check(string $label, bool $passed, string $why, string $fix): array
    {
        return [
            'label' => $label,
            'passed' => $passed,
            'why' => $why,
            'fix' => $fix,
        ];
    }
}
