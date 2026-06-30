<?php
declare(strict_types=1);

namespace GuildCMS\Installer;

final class InstallerPlatform
{
    /** @return array<string,mixed> */
    public static function detect(): array
    {
        $os = self::detectOperatingSystem();
        $server = self::detectWebServer();
        $php = self::detectPhp();
        $security = self::detectSecurityControls();
        $paths = self::detectPaths();

        $packageManager = self::detectPackageManager($os);

        return [
            'detected_at' => gmdate('c'),
            'os' => $os,
            'package_manager' => $packageManager,
            'web_server' => $server,
            'php' => $php,
            'database_drivers' => self::detectDatabaseDrivers(),
            'filesystem' => $paths,
            'process' => self::detectProcessIdentity(),
            'https' => self::detectHttps(),
            'security_controls' => $security,
        ];
    }

    /** @return array<string,string> */
    private static function detectOperatingSystem(): array
    {
        $data = [
            'family' => PHP_OS_FAMILY,
            'kernel' => php_uname('r'),
            'pretty_name' => php_uname('s') . ' ' . php_uname('r'),
            'id' => '',
            'version_id' => '',
            'source' => 'php_uname',
        ];

        $osRelease = self::parseOsRelease('/etc/os-release');
        if ($osRelease !== []) {
            $data['pretty_name'] = $osRelease['PRETTY_NAME'] ?? $data['pretty_name'];
            $data['id'] = strtolower($osRelease['ID'] ?? '');
            $data['version_id'] = $osRelease['VERSION_ID'] ?? '';
            $data['source'] = '/etc/os-release';
        }

        return $data;
    }

    /** @return array<string,string> */
    private static function parseOsRelease(string $file): array
    {
        if (!is_readable($file)) {
            return [];
        }

        $rows = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($rows === false) {
            return [];
        }

        $data = [];
        foreach ($rows as $row) {
            if (!str_contains($row, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $row, 2);
            $data[$key] = trim($value, " \t\n\r\0\x0B\"");
        }

        return $data;
    }

    /** @param array<string,string> $os */
    private static function detectPackageManager(array $os): string
    {
        $id = $os['id'] ?? '';
        if (in_array($id, ['rocky', 'almalinux', 'rhel', 'centos', 'fedora'], true)) {
            return 'dnf';
        }
        if (in_array($id, ['ubuntu', 'debian'], true)) {
            return 'apt';
        }
        if (self::commandExists('dnf')) {
            return 'dnf';
        }
        if (self::commandExists('apt-get')) {
            return 'apt';
        }
        return 'unknown';
    }

    /** @return array<string,string> */
    private static function detectWebServer(): array
    {
        $raw = (string) ($_SERVER['SERVER_SOFTWARE'] ?? '');
        $lower = strtolower($raw);
        $name = 'Unknown';

        if (str_contains($lower, 'apache')) {
            $name = 'Apache';
        } elseif (str_contains($lower, 'nginx')) {
            $name = 'Nginx';
        } elseif (str_contains($lower, 'litespeed')) {
            $name = 'LiteSpeed';
        }

        return [
            'name' => $name,
            'raw' => $raw !== '' ? $raw : 'Not reported by web server',
            'sapi' => PHP_SAPI,
        ];
    }

    /** @return array<string,mixed> */
    private static function detectPhp(): array
    {
        $extensions = get_loaded_extensions();
        sort($extensions, SORT_NATURAL | SORT_FLAG_CASE);

        return [
            'version' => PHP_VERSION,
            'version_id' => PHP_VERSION_ID,
            'sapi' => PHP_SAPI,
            'memory_limit' => ini_get('memory_limit') ?: 'unknown',
            'upload_max_filesize' => ini_get('upload_max_filesize') ?: 'unknown',
            'post_max_size' => ini_get('post_max_size') ?: 'unknown',
            'timezone' => date_default_timezone_get(),
            'ini_file' => php_ini_loaded_file() ?: 'not reported',
            'additional_ini_files' => php_ini_scanned_files() ?: 'not reported',
            'extensions' => $extensions,
        ];
    }

    /** @return array<string,bool> */
    private static function detectDatabaseDrivers(): array
    {
        return [
            'mysqli' => extension_loaded('mysqli'),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
        ];
    }

    /** @return array<string,mixed> */
    private static function detectPaths(): array
    {
        $includes = GUILDCMS_ROOT . '/includes';
        $config = $includes . '/config.inc.php';
        $sample = $includes . '/config.sample.inc.php';
        $includesExists = is_dir($includes);

        return [
            'root' => GUILDCMS_ROOT,
            'document_root' => (string) ($_SERVER['DOCUMENT_ROOT'] ?? GUILDCMS_ROOT),
            'installer_root' => GUILDCMS_INSTALLER_ROOT,
            'includes_path' => $includes,
            'config_target' => $config,
            'config_sample' => $sample,
            'includes_exists' => $includesExists,
            'includes_writable' => $includesExists && is_writable($includes),
            'includes_owner_uid' => $includesExists ? (string) @fileowner($includes) : 'unknown',
            'includes_owner' => $includesExists ? self::ownerName($includes) : 'unknown',
            'includes_group_gid' => $includesExists ? (string) @filegroup($includes) : 'unknown',
            'includes_group' => $includesExists ? self::groupName($includes) : 'unknown',
            'includes_permissions' => $includesExists ? self::permissionsString($includes) : 'unknown',
            'config_exists' => is_file($config),
            'sample_exists' => is_file($sample),
        ];
    }

    /** @return array<string,string> */
    private static function detectProcessIdentity(): array
    {
        $uid = function_exists('posix_geteuid') ? (int) posix_geteuid() : null;
        $gid = function_exists('posix_getegid') ? (int) posix_getegid() : null;

        $user = 'unknown';
        if ($uid !== null && function_exists('posix_getpwuid')) {
            $row = @posix_getpwuid($uid);
            if (is_array($row) && isset($row['name'])) {
                $user = (string) $row['name'];
            }
        }

        $group = 'unknown';
        if ($gid !== null && function_exists('posix_getgrgid')) {
            $row = @posix_getgrgid($gid);
            if (is_array($row) && isset($row['name'])) {
                $group = (string) $row['name'];
            }
        }

        return [
            'uid' => $uid !== null ? (string) $uid : 'unknown',
            'user' => $user,
            'gid' => $gid !== null ? (string) $gid : 'unknown',
            'group' => $group,
            'script_owner' => get_current_user(),
        ];
    }

    private static function ownerName(string $path): string
    {
        $uid = @fileowner($path);
        if ($uid === false) {
            return 'unknown';
        }
        if (function_exists('posix_getpwuid')) {
            $row = @posix_getpwuid((int) $uid);
            if (is_array($row) && isset($row['name'])) {
                return (string) $row['name'];
            }
        }
        return (string) $uid;
    }

    private static function groupName(string $path): string
    {
        $gid = @filegroup($path);
        if ($gid === false) {
            return 'unknown';
        }
        if (function_exists('posix_getgrgid')) {
            $row = @posix_getgrgid((int) $gid);
            if (is_array($row) && isset($row['name'])) {
                return (string) $row['name'];
            }
        }
        return (string) $gid;
    }

    private static function permissionsString(string $path): string
    {
        $perms = @fileperms($path);
        if ($perms === false) {
            return 'unknown';
        }

        return substr(sprintf('%o', $perms), -4);
    }

    /** @return array<string,mixed> */
    private static function detectHttps(): array
    {
        $enabled = false;
        if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
            $enabled = true;
        }
        if (($_SERVER['SERVER_PORT'] ?? '') === '443') {
            $enabled = true;
        }
        $forwardedProto = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
        if ($forwardedProto === 'https') {
            $enabled = true;
        }

        return [
            'enabled' => $enabled,
            'server_port' => (string) ($_SERVER['SERVER_PORT'] ?? 'unknown'),
            'forwarded_proto' => $forwardedProto !== '' ? $forwardedProto : 'not provided',
        ];
    }

    /** @return array<string,string> */
    private static function detectSecurityControls(): array
    {
        return [
            'selinux' => self::detectSelinux(),
            'apparmor' => self::detectAppArmor(),
        ];
    }

    private static function detectSelinux(): string
    {
        $getenforce = self::runCommand('getenforce');
        if ($getenforce !== '') {
            return $getenforce;
        }
        if (is_dir('/sys/fs/selinux')) {
            return 'Present, status unavailable to PHP';
        }
        return 'Not detected';
    }

    private static function detectAppArmor(): string
    {
        $aaStatus = self::runCommand('aa-status --enabled 2>/dev/null && echo enabled');
        if (str_contains(strtolower($aaStatus), 'enabled')) {
            return 'Enabled';
        }
        if (is_dir('/sys/kernel/security/apparmor')) {
            return 'Present, status unavailable to PHP';
        }
        return 'Not detected';
    }

    private static function commandExists(string $command): bool
    {
        $result = self::runCommand('command -v ' . escapeshellarg($command));
        return $result !== '';
    }

    private static function runCommand(string $command): string
    {
        if (!function_exists('shell_exec')) {
            return '';
        }
        $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
        if (in_array('shell_exec', $disabled, true)) {
            return '';
        }

        $result = @shell_exec($command);
        return is_string($result) ? trim($result) : '';
    }
}
