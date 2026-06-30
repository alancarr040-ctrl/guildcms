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

    public function status(): string
    {
        $checks = InstallerEnvironment::recommendedChecks();
        return InstallerEnvironment::passedCount($checks) === count($checks) ? 'Ready' : 'Optional Warnings';
    }

    public function render(Installer $installer): string
    {
        $checks = InstallerEnvironment::recommendedChecks();
        $passed = InstallerEnvironment::passedCount($checks);
        $total = count($checks);

        $body = '<h2>These features are helpful, but not required.</h2>' .
            $this->paragraph('Guild CMS can continue without these items. When something is missing, we explain what it improves so you can decide whether to enable it now or later.') .
            '<div class="readiness-summary summary-info"><strong>' . InstallerView::escape((string) $passed) . ' of ' . InstallerView::escape((string) $total) . ' recommended checks passed.</strong><br><span class="small-muted">Recommended items improve the experience, but they do not block installation.</span></div>';

        foreach ($checks as $check) {
            $body .= $this->renderCheck($check);
        }

        return $this->panel(
            $body .
            '<div class="notice notice-good"><strong>You can continue.</strong><br><span class="small-muted">Recommended features can be revisited after installation. Guild CMS is telling you about them now so there are no surprises later.</span></div>' .
            $this->help('Why are these not required?', 'Some hosting environments are intentionally minimal. Guild CMS separates required items from recommended improvements so a missing optional feature does not prevent a valid installation.')
        );
    }

    /** @param array<string,string|bool> $check */
    private function renderCheck(array $check): string
    {
        $passed = !empty($check['passed']);
        $label = (string) ($check['label'] ?? 'Check');
        $why = (string) ($check['why'] ?? '');
        $fix = (string) ($check['fix'] ?? '');
        $detail = (string) ($check['detail'] ?? '');

        $out = '<div class="check-row">';
        $out .= '<div class="check-title ' . ($passed ? 'check-pass' : 'check-warn') . '"><span aria-hidden="true">' . ($passed ? '✓' : '⚠') . '</span> ' . InstallerView::escape($label) . '</div>';
        $out .= '<p class="small-muted">' . InstallerView::escape($why) . '</p>';
        if ($detail !== '') {
            $out .= '<p class="check-detail"><strong>Detected:</strong> ' . InstallerView::escape($detail) . '</p>';
        }
        if (!$passed) {
            $out .= '<p><strong>Suggested improvement:</strong> ' . InstallerView::escape($fix) . '</p>';
        }
        $out .= '</div>';

        return $out;
    }
}
