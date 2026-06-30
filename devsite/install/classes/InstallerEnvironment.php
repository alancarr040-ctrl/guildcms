<?php
declare(strict_types=1);

namespace GuildCMS\Installer;

final class InstallerEnvironment
{
    /** @return array<int,array<string,string|bool>> */
    public static function requiredChecks(): array
    {
        return [
            self::check(
                'PHP Version',
                version_compare(PHP_VERSION, '8.2.0', '>='),
                'Guild CMS requires PHP 8.2 or newer for the supported installation path, current security posture, and predictable runtime behavior.',
                self::phpUpgradeGuidance(),
                'Current PHP version: ' . PHP_VERSION
            ),
            self::check(
                'JSON Extension',
                extension_loaded('json'),
                'Guild CMS uses JSON for installer state, plugin metadata, API responses, and configuration-related data.',
                'Enable the PHP JSON extension. On most supported PHP builds this is enabled by default.',
                extension_loaded('json') ? 'JSON support is available.' : 'JSON support is not currently available.'
            ),
            self::check(
                'MySQLi Extension',
                extension_loaded('mysqli'),
                'Guild CMS uses MySQLi to communicate with MariaDB/MySQL databases during installation and normal operation.',
                'Enable the PHP MySQLi extension, then refresh this page so Guild CMS can check again.',
                extension_loaded('mysqli') ? 'MySQLi support is available.' : 'MySQLi support is not currently available.'
            ),
            self::check(
                'Session Support',
                function_exists('session_start') && session_status() !== PHP_SESSION_DISABLED,
                'Sessions allow the installer to remember progress while setup is underway and will later support administrator login workflows.',
                'Enable PHP session support for this site.',
                'Current session status: ' . self::sessionStatusLabel()
            ),
            self::check(
                'Configuration Directory Writable',
                self::directoryAcceptsTemporaryFile(GUILDCMS_ROOT . '/includes'),
                'The installer must be able to create includes/config.inc.php during the final install phase.',
                'Make the includes directory writable by the web server for installation. After installation, you may secure it according to your hosting policy.',
                'Checked path: ' . GUILDCMS_ROOT . '/includes'
            ),
            self::check(
                'Configuration Sample Available',
                is_file(GUILDCMS_ROOT . '/includes/config.sample.inc.php') && is_readable(GUILDCMS_ROOT . '/includes/config.sample.inc.php'),
                'Guild CMS ships a sample configuration so the installer can generate the real includes/config.inc.php without requiring manual editing.',
                'Restore includes/config.sample.inc.php from the Guild CMS package.',
                'Checked file: includes/config.sample.inc.php'
            ),
            self::check(
                'Installer Directory Readable',
                is_dir(GUILDCMS_INSTALLER_ROOT) && is_readable(GUILDCMS_INSTALLER_ROOT),
                'The installer needs access to its own step, template, and class files in order to guide setup safely.',
                'Verify the install directory exists and is readable by the web server.',
                'Checked path: ' . GUILDCMS_INSTALLER_ROOT
            ),
        ];
    }

    /** @return array<int,array<string,string|bool>> */
    public static function recommendedChecks(): array
    {
        $https = self::isHttps();
        $imageSupport = extension_loaded('gd') || extension_loaded('imagick');

        return [
            self::check(
                'HTTPS',
                $https,
                'HTTPS protects administrator logins, database credentials entered during installation, and other sensitive traffic.',
                'Install an SSL/TLS certificate before putting the site into production. You may continue testing without HTTPS.',
                $https ? 'HTTPS appears to be active for this request.' : 'HTTPS was not detected for this request.'
            ),
            self::check(
                'OPcache',
                extension_loaded('Zend OPcache') || extension_loaded('opcache'),
                'OPcache improves PHP performance by caching compiled scripts so pages can load faster.',
                'Enable OPcache for better production performance when your hosting environment allows it.',
                (extension_loaded('Zend OPcache') || extension_loaded('opcache')) ? 'OPcache is available.' : 'OPcache is not currently available.'
            ),
            self::check(
                'cURL',
                extension_loaded('curl'),
                'cURL supports future update checks, external integrations, provider connections, and package communication workflows.',
                'Enable the PHP cURL extension if you plan to use integrations, update checks, or external services.',
                extension_loaded('curl') ? 'cURL is available.' : 'cURL is not currently available.'
            ),
            self::check(
                'Image Processing',
                $imageSupport,
                'GD or ImageMagick helps Guild CMS create thumbnails, process uploaded images, and support richer media tools.',
                'Enable GD or ImageMagick when media features are needed.',
                $imageSupport ? self::imageSupportLabel() : 'Neither GD nor ImageMagick is currently available.'
            ),
            self::check(
                'ZIP Extension',
                extension_loaded('zip'),
                'ZIP support will help future plugin, theme, backup, and package workflows.',
                'Enable the PHP ZIP extension for package handling.',
                extension_loaded('zip') ? 'ZIP support is available.' : 'ZIP support is not currently available.'
            ),
            self::check(
                'Intl Extension',
                extension_loaded('intl'),
                'The Intl extension improves localization, formatting, and future multilingual features.',
                'Enable the PHP Intl extension if your site will support multiple languages or locales.',
                extension_loaded('intl') ? 'Intl support is available.' : 'Intl support is not currently available.'
            ),
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

    /** @param array<int,array<string,string|bool>> $checks */
    public static function passedCount(array $checks): int
    {
        $count = 0;
        foreach ($checks as $check) {
            if (!empty($check['passed'])) {
                $count++;
            }
        }
        return $count;
    }

    /** @return array<string,string|bool> */
    private static function check(string $label, bool $passed, string $why, string $fix, string $detail): array
    {
        return [
            'label' => $label,
            'passed' => $passed,
            'why' => $why,
            'fix' => $fix,
            'detail' => $detail,
        ];
    }


    private static function phpUpgradeGuidance(): string
    {
        $platform = InstallerPlatform::detect();
        $manager = (string) ($platform['package_manager'] ?? 'unknown');
        if ($manager === 'dnf') {
            return 'Switch this site to PHP 8.2 or newer. On Rocky Linux or AlmaLinux, use the PHP 8.2 AppStream module or the PHP packages supplied by your server policy, then restart Apache/PHP-FPM.';
        }
        if ($manager === 'apt') {
            return 'Switch this site to PHP 8.2 or newer. On Ubuntu or Debian, install the supported PHP packages for this virtual host, then restart Apache/PHP-FPM.';
        }
        return 'Ask your hosting provider or server administrator to switch this site to PHP 8.2 or newer before continuing.';
    }

    private static function sessionStatusLabel(): string
    {
        return match (session_status()) {
            PHP_SESSION_ACTIVE => 'active',
            PHP_SESSION_NONE => 'available but not active',
            PHP_SESSION_DISABLED => 'disabled',
            default => 'unknown',
        };
    }

    private static function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
            return true;
        }

        if (($_SERVER['SERVER_PORT'] ?? '') === '443') {
            return true;
        }

        $forwardedProto = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
        return $forwardedProto === 'https';
    }

    private static function imageSupportLabel(): string
    {
        $available = [];
        if (extension_loaded('gd')) {
            $available[] = 'GD';
        }
        if (extension_loaded('imagick')) {
            $available[] = 'ImageMagick';
        }

        return 'Available image support: ' . implode(', ', $available) . '.';
    }

    private static function directoryAcceptsTemporaryFile(string $path): bool
    {
        if (!is_dir($path) || !is_writable($path)) {
            return false;
        }

        $testFile = rtrim($path, '/\\') . '/.guildcms_write_test_' . bin2hex(random_bytes(4));
        $written = @file_put_contents($testFile, 'Guild CMS installer write test');

        if ($written === false) {
            return false;
        }

        @unlink($testFile);
        return true;
    }
}
