<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;
use GuildCMS\Installer\InstallerEnvironment;
use GuildCMS\Installer\InstallerView;

final class RecommendedStep extends AbstractInstallerStep
{
    public function key(): string { return 'recommended'; }
    public function title(): string { return 'Recommended Features'; }
    public function summary(): string { return 'Review optional features that improve security, performance, and future workflows.'; }

    public function render(Installer $installer): string
    {
        $body = '<h2>These features are helpful, but not required.</h2>' .
            $this->paragraph('Guild CMS can continue without these items. When something is missing, we explain what it improves so you can decide whether to enable it now or later.');

        foreach (InstallerEnvironment::recommendedChecks() as $check) {
            $passed = !empty($check['passed']);
            $body .= '<div class="check-row">';
            $body .= '<div class="check-title ' . ($passed ? 'check-pass' : 'check-warn') . '">' . ($passed ? '✓' : '⚠') . ' ' . InstallerView::escape((string) $check['label']) . '</div>';
            $body .= '<p class="small-muted">' . InstallerView::escape((string) $check['why']) . '</p>';
            if (!$passed) {
                $body .= '<p><strong>Suggested improvement:</strong> ' . InstallerView::escape((string) $check['fix']) . '</p>';
            }
            $body .= '</div>';
        }

        return $this->panel($body . '<div class="notice notice-good"><strong>You can continue.</strong><br><span class="small-muted">Recommended features can be revisited after installation.</span></div>');
    }
}
