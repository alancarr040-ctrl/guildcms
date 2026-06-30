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
        $https = $environment['https'] ?? [];
        $security = $environment['security_controls'] ?? [];

        $body = '<h2>Let\'s identify your server environment.</h2>' .
            $this->paragraph('Guild CMS uses this information to explain requirements in terms that match your server. A Rocky or AlmaLinux system may need dnf guidance, while Ubuntu or Debian usually needs apt guidance.') .
            '<div class="notice notice-good"><strong>Detection only.</strong><br><span class="small-muted">This step does not change files, connect to your database, or write configuration. It records a snapshot for later installer steps.</span></div>' .
            '<div class="environment-grid">' .
            $this->detailCard('Operating System', [
                'Detected' => (string) ($os['pretty_name'] ?? 'Unknown'),
                'Distribution ID' => (string) ($os['id'] ?? 'unknown'),
                'Version' => (string) ($os['version_id'] ?? 'unknown'),
                'Package Manager' => (string) ($environment['package_manager'] ?? 'unknown'),
                'Kernel' => (string) ($os['kernel'] ?? 'unknown'),
            ]) .
            $this->detailCard('Web Server', [
                'Detected' => (string) ($web['name'] ?? 'Unknown'),
                'Reported Software' => (string) ($web['raw'] ?? 'unknown'),
                'PHP SAPI' => (string) ($web['sapi'] ?? 'unknown'),
            ]) .
            $this->detailCard('PHP Runtime', [
                'Version' => (string) ($php['version'] ?? PHP_VERSION),
                'Memory Limit' => (string) ($php['memory_limit'] ?? 'unknown'),
                'Upload Limit' => (string) ($php['upload_max_filesize'] ?? 'unknown'),
                'Post Limit' => (string) ($php['post_max_size'] ?? 'unknown'),
                'Timezone' => (string) ($php['timezone'] ?? 'unknown'),
            ]) .
            $this->detailCard('Database Drivers', [
                'MySQLi' => !empty($drivers['mysqli']) ? 'Available' : 'Not available',
                'PDO MySQL' => !empty($drivers['pdo_mysql']) ? 'Available' : 'Not available',
            ]) .
            $this->detailCard('Filesystem', [
                'Includes Path' => (string) ($filesystem['includes_path'] ?? 'unknown'),
                'Includes Writable' => !empty($filesystem['includes_writable']) ? 'Yes' : 'No',
                'Config Exists' => !empty($filesystem['config_exists']) ? 'Yes' : 'No',
                'Sample Config Exists' => !empty($filesystem['sample_exists']) ? 'Yes' : 'No',
            ]) .
            $this->detailCard('Security Context', [
                'HTTPS' => !empty($https['enabled']) ? 'Detected' : 'Not detected',
                'SELinux' => (string) ($security['selinux'] ?? 'Unknown'),
                'AppArmor' => (string) ($security['apparmor'] ?? 'Unknown'),
            ]) .
            '</div>' .
            $this->extensionsCard($php['extensions'] ?? []);

        return $this->panel(
            $body .
            $this->help('Why does Guild CMS detect the platform?', 'Helpful installer guidance depends on context. If Guild CMS knows whether the server is Rocky, AlmaLinux, Ubuntu, or Debian, it can provide more accurate next steps when something needs attention.') .
            $this->help('What happens to this information?', 'The installer stores a session snapshot for later steps. It is used to guide installation and testing decisions. It is not written to the final site configuration during this package.')
        );
    }

    /** @param array<string,string> $rows */
    private function detailCard(string $title, array $rows): string
    {
        $out = '<div class="environment-card"><h3>' . InstallerView::escape($title) . '</h3><dl>';
        foreach ($rows as $label => $value) {
            $out .= '<dt>' . InstallerView::escape((string) $label) . '</dt><dd>' . InstallerView::escape((string) $value) . '</dd>';
        }
        return $out . '</dl></div>';
    }

    /** @param mixed $extensions */
    private function extensionsCard(mixed $extensions): string
    {
        if (!is_array($extensions)) {
            $extensions = [];
        }

        $items = [];
        foreach ($extensions as $extension) {
            if (is_string($extension)) {
                $items[] = '<span class="extension-pill">' . InstallerView::escape($extension) . '</span>';
            }
        }

        return '<div class="environment-card environment-card-wide"><h3>Loaded PHP Extensions</h3><p class="small-muted">These extensions help later installer steps decide which features are available now and which recommended improvements should be explained.</p><div class="extension-list">' . implode('', $items) . '</div></div>';
    }
}
