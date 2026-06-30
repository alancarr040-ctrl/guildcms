<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;
use GuildCMS\Installer\InstallerEnvironment;
use GuildCMS\Installer\InstallerView;

final class RequirementsStep extends AbstractInstallerStep
{
    public function key(): string { return 'requirements'; }
    public function title(): string { return 'System Readiness'; }
    public function summary(): string { return 'Check what Guild CMS must have before installation can continue.'; }

    public function status(): string
    {
        return InstallerEnvironment::hasBlockingFailures(InstallerEnvironment::requiredChecks()) ? 'Attention Needed' : 'Ready';
    }

    public function canContinue(): bool
    {
        return !InstallerEnvironment::hasBlockingFailures(InstallerEnvironment::requiredChecks());
    }

    public function render(Installer $installer): string
    {
        $checks = InstallerEnvironment::requiredChecks();
        $hasFailures = InstallerEnvironment::hasBlockingFailures($checks);
        $passed = InstallerEnvironment::passedCount($checks);
        $total = count($checks);

        $body = '<h2>Let\'s make sure your server is ready.</h2>' .
            $this->paragraph('These checks happen first because Guild CMS should tell you immediately if the server is missing something required. This step does not write files, create database tables, or make permanent changes.') .
            '<div class="readiness-summary ' . ($hasFailures ? 'summary-warn' : 'summary-good') . '">' .
            '<strong>' . InstallerView::escape((string) $passed) . ' of ' . InstallerView::escape((string) $total) . ' required checks passed.</strong><br>' .
            '<span class="small-muted">' . ($hasFailures ? 'A few items need attention before installation can continue.' : 'Your server passed every required readiness check.') . '</span>' .
            '</div>';

        foreach ($checks as $check) {
            $body .= $this->renderCheck($check, true);
        }

        if ($hasFailures) {
            $body .= '<div class="notice notice-warn"><strong>Attention needed before installation can continue.</strong><br><span class="small-muted">Nothing has been changed. Correct the items above, then refresh or use Recheck to run the readiness check again.</span></div>';
        } else {
            $body .= '<div class="notice notice-good"><strong>Your server passed the required checks.</strong><br><span class="small-muted">You are ready to continue to recommended features.</span></div>';
        }

        return $this->panel(
            $body .
            $this->help('Why does Guild CMS check this before anything else?', 'If Guild CMS needs something in order to run, you deserve to know immediately. The installer should never wait until the end to report a blocking server problem.') .
            $this->help('What should I do if a check fails?', 'Read the explanation and suggested fix for that item. After correcting the server configuration, refresh this page. Guild CMS will recheck the environment without losing your installer progress.')
        );
    }

    /** @param array<string,string|bool> $check */
    private function renderCheck(array $check, bool $required): string
    {
        $passed = !empty($check['passed']);
        $stateClass = $passed ? 'check-pass' : ($required ? 'check-fail' : 'check-warn');
        $symbol = $passed ? '✓' : ($required ? '✕' : '⚠');
        $label = (string) ($check['label'] ?? 'Check');
        $why = (string) ($check['why'] ?? '');
        $fix = (string) ($check['fix'] ?? '');
        $detail = (string) ($check['detail'] ?? '');

        $out = '<div class="check-row">';
        $out .= '<div class="check-title ' . $stateClass . '"><span aria-hidden="true">' . $symbol . '</span> ' . InstallerView::escape($label) . '</div>';
        $out .= '<p class="small-muted">' . InstallerView::escape($why) . '</p>';
        if ($detail !== '') {
            $out .= '<p class="check-detail"><strong>Detected:</strong> ' . InstallerView::escape($detail) . '</p>';
        }
        if (!$passed) {
            $out .= '<p><strong>How to fix this:</strong> ' . InstallerView::escape($fix) . '</p>';
        }
        $out .= '</div>';

        return $out;
    }
}
