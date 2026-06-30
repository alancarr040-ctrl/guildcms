<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;
use GuildCMS\Installer\InstallerPlatform;
use GuildCMS\Installer\InstallerView;

final class EnvironmentStep extends AbstractInstallerStep
{
    public function key(): string { return 'environment'; }
    public function title(): string { return 'Environment Detection'; }
    public function summary(): string { return 'Identify the server platform so later installer steps can provide accurate guidance.'; }

    public function render(Installer $installer): string
    {
        $environment = InstallerPlatform::detect();
        $installer->state()->setEnvironmentSnapshot($environment);

        $os = $environment['os'] ?? [];
        $web = $environment['web_server'] ?? [];
        $php = $environment['php'] ?? [];
        $drivers = $environment['database_drivers'] ?? [];
        $filesystem = $environment['filesystem'] ?? [];
        $process = $environment['process'] ?? [];
        $https = $environment['https'] ?? [];
        $security = $environment['security_controls'] ?? [];
        $packageManager = (string) ($environment['package_manager'] ?? 'unknown');

        $body = '<h2>Let\'s identify your server environment.</h2>' .
            $this->paragraph('Guild CMS uses this information to explain requirements in terms that match your server. This page explains what was detected first, then places technical details behind expandable sections for anyone who wants to inspect them.') .
            '<div class="notice notice-good"><strong>Detection only.</strong><br><span class="small-muted">This step does not change files, connect to your database, or write configuration. It records a snapshot for later installer steps.</span></div>' .
            '<div class="environment-grid environment-grid-readable">' .
            $this->operatingSystemCard($os, $packageManager) .
            $this->webServerCard($web, $filesystem) .
            $this->phpRuntimeCard($php, $process) .
            $this->databaseCard($drivers) .
            $this->includesDirectoryCard($filesystem, $process, $packageManager) .
            $this->securityCard($https, $security) .
            '</div>' .
            $this->extensionsCard($php['extensions'] ?? [], $php);

        return $this->panel(
            $body .
            $this->help('Why does Guild CMS detect the platform?', 'Helpful installer guidance depends on context. If Guild CMS knows whether the server is Rocky, AlmaLinux, Ubuntu, or Debian, it can provide more accurate next steps when something needs attention.') .
            $this->help('Why show technical details at all?', 'Guild CMS is designed for new administrators and experienced administrators. The summary explains what matters. The technical details show exact paths, users, groups, permissions, and PHP configuration when you need them.')
        );
    }

    /** @param array<string,mixed> $os */
    private function operatingSystemCard(array $os, string $packageManager): string
    {
        $name = (string) ($os['pretty_name'] ?? 'Unknown Linux environment');
        $id = (string) ($os['id'] ?? 'unknown');

        $status = in_array($id, ['rocky', 'almalinux', 'ubuntu', 'debian'], true)
            ? 'Recognized environment'
            : 'Environment detected';

        $message = 'Guild CMS detected the operating system so future instructions can use the right package manager and terminology.';

        return $this->educationalCard(
            'Operating System',
            $status,
            $name,
            $message,
            [
                'Distribution ID' => (string) ($os['id'] ?? 'unknown'),
                'Version' => (string) ($os['version_id'] ?? 'unknown'),
                'Package Manager' => $packageManager,
                'Kernel' => (string) ($os['kernel'] ?? 'unknown'),
                'Detection Source' => (string) ($os['source'] ?? 'unknown'),
            ],
            'Future installer guidance can reference ' . ($packageManager === 'unknown' ? 'general server instructions' : $packageManager . ' commands') . ' when something needs attention.'
        );
    }

    /** @param array<string,mixed> $web @param array<string,mixed> $filesystem */
    private function webServerCard(array $web, array $filesystem): string
    {
        $name = (string) ($web['name'] ?? 'Unknown');
        return $this->educationalCard(
            'Web Server',
            $name !== 'Unknown' ? 'Detected' : 'Not fully identified',
            $name,
            'The web server receives browser requests and passes PHP pages to Guild CMS. Knowing the web server helps the installer explain rewrite, path, and permission guidance later.',
            [
                'Reported Software' => (string) ($web['raw'] ?? 'unknown'),
                'PHP SAPI' => (string) ($web['sapi'] ?? 'unknown'),
                'Document Root' => (string) ($filesystem['document_root'] ?? 'unknown'),
            ],
            'The next checks use this information to decide how Guild CMS should describe server paths and execution context.'
        );
    }

    /** @param array<string,mixed> $php @param array<string,mixed> $process */
    private function phpRuntimeCard(array $php, array $process): string
    {
        $version = (string) ($php['version'] ?? PHP_VERSION);
        $phpUser = (string) ($process['user'] ?? 'unknown');

        return $this->educationalCard(
            'PHP Runtime',
            version_compare($version, '8.2.0', '>=') ? 'Supported' : 'Attention needed',
            'PHP ' . $version,
            'PHP runs Guild CMS. The installer also detects the operating system account PHP is using so permission guidance can tell you which user needs access to write configuration files.',
            [
                'PHP SAPI' => (string) ($php['sapi'] ?? PHP_SAPI),
                'Effective PHP User' => $phpUser,
                'Effective PHP Group' => (string) ($process['group'] ?? 'unknown'),
                'Memory Limit' => (string) ($php['memory_limit'] ?? 'unknown'),
                'Upload Limit' => (string) ($php['upload_max_filesize'] ?? 'unknown'),
                'Post Limit' => (string) ($php['post_max_size'] ?? 'unknown'),
                'Timezone' => (string) ($php['timezone'] ?? 'unknown'),
                'Loaded php.ini' => (string) ($php['ini_file'] ?? 'not reported'),
                'Additional INI Files' => (string) ($php['additional_ini_files'] ?? 'not reported'),
            ],
            $phpUser !== 'unknown'
                ? 'If Guild CMS cannot write a file, this is usually the system user that needs permission.'
                : 'The installer could not identify the PHP user. Permission messages will remain more general.'
        );
    }

    /** @param array<string,mixed> $drivers */
    private function databaseCard(array $drivers): string
    {
        $mysqli = !empty($drivers['mysqli']);
        $pdoMysql = !empty($drivers['pdo_mysql']);

        return $this->educationalCard(
            'Database Support',
            ($mysqli || $pdoMysql) ? 'Available' : 'Attention needed',
            ($mysqli || $pdoMysql) ? 'MySQL-compatible driver detected' : 'No MySQL-compatible driver detected',
            'Guild CMS stores pages, settings, users, modules, and configuration records in a database. The installer checks whether PHP can communicate with MySQL or MariaDB.',
            [
                'MySQLi' => $mysqli ? 'Available' : 'Not available',
                'PDO MySQL' => $pdoMysql ? 'Available' : 'Not available',
            ],
            ($mysqli || $pdoMysql)
                ? 'The database configuration step will later use this support to test your database credentials.'
                : 'A database driver must be installed before Guild CMS can connect to MariaDB or MySQL.'
        );
    }

    /** @param array<string,mixed> $filesystem @param array<string,mixed> $process */
    private function includesDirectoryCard(array $filesystem, array $process, string $packageManager): string
    {
        $exists = !empty($filesystem['includes_exists']);
        $writable = !empty($filesystem['includes_writable']);
        $configExists = !empty($filesystem['config_exists']);

        $status = !$exists ? 'Attention needed' : ($writable ? 'Ready' : 'Attention needed');
        $headline = !$exists
            ? 'Includes directory was not found'
            : ($writable ? 'Includes directory found and writable' : 'Includes directory found but not writable');

        $why = 'Guild CMS stores its generated configuration file in the includes directory. The installer must be able to write there when it creates includes/config.inc.php later in the installation.';

        $details = [
            'Includes Path' => (string) ($filesystem['includes_path'] ?? 'unknown'),
            'Config Target' => (string) ($filesystem['config_target'] ?? 'unknown'),
            'Sample Config' => (string) ($filesystem['config_sample'] ?? 'unknown'),
            'Directory Owner' => (string) ($filesystem['includes_owner'] ?? 'unknown'),
            'Directory Group' => (string) ($filesystem['includes_group'] ?? 'unknown'),
            'Permissions' => (string) ($filesystem['includes_permissions'] ?? 'unknown'),
            'Effective PHP User' => (string) ($process['user'] ?? 'unknown'),
            'Effective PHP Group' => (string) ($process['group'] ?? 'unknown'),
            'Configuration Exists' => $configExists ? 'Yes' : 'No',
            'Sample Configuration Exists' => !empty($filesystem['sample_exists']) ? 'Yes' : 'No',
        ];

        $guidance = $writable
            ? ($configExists
                ? 'A configuration file already exists. Later installer steps should treat this as a possible existing or partially started installation.'
                : 'No configuration file exists yet. That is expected for a new installation. Guild CMS will create it only after validation succeeds.')
            : $this->permissionGuidance($filesystem, $process, $packageManager);

        return $this->educationalCard(
            'Includes Directory',
            $status,
            $headline,
            $why,
            $details,
            $guidance
        );
    }

    /** @param array<string,mixed> $filesystem @param array<string,mixed> $process */
    private function permissionGuidance(array $filesystem, array $process, string $packageManager): string
    {
        $path = (string) ($filesystem['includes_path'] ?? 'includes');
        $user = (string) ($process['user'] ?? 'the PHP user');
        $group = (string) ($process['group'] ?? $user);

        $command = 'chown -R ' . $user . ':' . $group . ' ' . $path . "\n" . 'chmod 775 ' . $path;
        $prefix = $packageManager === 'apt' ? 'sudo ' : '';

        return 'Guild CMS cannot write to this directory yet. PHP is running as <strong>' . InstallerView::escape($user) . '</strong>. If this is your server, one possible fix is:' .
            '<pre class="install-code">' . InstallerView::escape($prefix . str_replace("\n", "\n" . $prefix, $command)) . '</pre>' .
            '<span class="small-muted">These commands change ownership and permissions so the PHP user can create the configuration file. Review them before running them, especially on shared hosting.</span>';
    }

    /** @param array<string,mixed> $https @param array<string,mixed> $security */
    private function securityCard(array $https, array $security): string
    {
        $secure = !empty($https['enabled']);

        return $this->educationalCard(
            'Security Context',
            $secure ? 'HTTPS detected' : 'HTTPS not detected',
            $secure ? 'Secure installer request' : 'Installer request is not using HTTPS',
            'Guild CMS checks HTTPS and Linux security controls because they can affect installation safety, file access, and administrator login security.',
            [
                'HTTPS' => $secure ? 'Detected' : 'Not detected',
                'Server Port' => (string) ($https['server_port'] ?? 'unknown'),
                'Forwarded Protocol' => (string) ($https['forwarded_proto'] ?? 'not provided'),
                'SELinux' => (string) ($security['selinux'] ?? 'Unknown'),
                'AppArmor' => (string) ($security['apparmor'] ?? 'Unknown'),
            ],
            $secure
                ? 'HTTPS is active for this request.'
                : 'Guild CMS can continue during development, but production installations should use HTTPS before administrator credentials are entered.'
        );
    }

    /** @param array<string,string> $details */
    private function educationalCard(string $title, string $status, string $headline, string $why, array $details, string $next): string
    {
        return '<div class="environment-card environment-card-education">' .
            '<div class="environment-card-header"><h3>' . InstallerView::escape($title) . '</h3><span class="environment-status">' . InstallerView::escape($status) . '</span></div>' .
            '<p class="environment-headline">' . InstallerView::escape($headline) . '</p>' .
            '<p><strong>Why this matters:</strong> ' . InstallerView::escape($why) . '</p>' .
            '<p><strong>What happens next:</strong> ' . $next . '</p>' .
            $this->technicalDetails($details) .
            '</div>';
    }

    /** @param array<string,string> $rows */
    private function technicalDetails(array $rows): string
    {
        $out = '<details class="technical-details"><summary>Show Technical Details</summary><dl>';
        foreach ($rows as $label => $value) {
            $out .= '<dt>' . InstallerView::escape((string) $label) . '</dt><dd>' . InstallerView::escape((string) $value) . '</dd>';
        }
        return $out . '</dl></details>';
    }

    /** @param mixed $extensions @param array<string,mixed> $php */
    private function extensionsCard(mixed $extensions, array $php): string
    {
        if (!is_array($extensions)) {
            $extensions = [];
        }

        $important = ['curl', 'gd', 'imagick', 'intl', 'json', 'mbstring', 'mysqli', 'openssl', 'pdo', 'pdo_mysql', 'session', 'xml', 'zip'];
        $extensionNames = [];
        foreach ($extensions as $extension) {
            if (is_string($extension)) {
                $extensionNames[] = $extension;
            }
        }
        sort($extensionNames, SORT_NATURAL | SORT_FLAG_CASE);

        $importantFound = array_values(array_intersect($important, $extensionNames));
        $otherExtensions = array_values(array_diff($extensionNames, $importantFound));

        return '<div class="environment-card environment-card-wide environment-card-education">' .
            '<div class="environment-card-header"><h3>Loaded PHP Extensions</h3><span class="environment-status">Detected</span></div>' .
            '<p>PHP extensions add capabilities to Guild CMS. The installer highlights commonly important extensions first and keeps the complete list available for technical review.</p>' .
            '<p><strong>Commonly important extensions:</strong></p>' .
            '<div class="extension-list">' . $this->extensionPills($importantFound) . '</div>' .
            '<details class="technical-details"><summary>Show all loaded PHP extensions and PHP INI details</summary>' .
            '<div class="extension-list extension-list-muted">' . $this->extensionPills($otherExtensions) . '</div>' .
            '<dl><dt>Loaded php.ini</dt><dd>' . InstallerView::escape((string) ($php['ini_file'] ?? 'not reported')) . '</dd>' .
            '<dt>Additional INI Files</dt><dd>' . InstallerView::escape((string) ($php['additional_ini_files'] ?? 'not reported')) . '</dd></dl>' .
            '</details>' .
            '</div>';
    }

    /** @param array<int,string> $extensions */
    private function extensionPills(array $extensions): string
    {
        if ($extensions === []) {
            return '<span class="small-muted">None detected in this group.</span>';
        }

        $items = [];
        foreach ($extensions as $extension) {
            $items[] = '<span class="extension-pill">' . InstallerView::escape($extension) . '</span>';
        }

        return implode('', $items);
    }
}
