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

    public function render(Installer $installer): string
    {
        $checks = InstallerEnvironment::requiredChecks();
        $hasFailures = InstallerEnvironment::hasBlockingFailures($checks);
        $body = '<h2>Let\'s make sure your server is ready.</h2>' .
            $this->paragraph('These checks are required because Guild CMS cannot run reliably without them. This step does not write files or change your database.');

        foreach ($checks as $check) {
            $passed = !empty($check['passed']);
            $body .= '<div class="check-row">';
            $body .= '<div class="check-title ' . ($passed ? 'check-pass' : 'check-fail') . '">' . ($passed ? '✓' : '✕') . ' ' . InstallerView::escape((string) $check['label']) . '</div>';
            $body .= '<p class="small-muted">' . InstallerView::escape((string) $check['why']) . '</p>';
            if (!$passed) {
                $body .= '<p><strong>How to fix this:</strong> ' . InstallerView::escape((string) $check['fix']) . '</p>';
            }
            $body .= '</div>';
        }

        if ($hasFailures) {
            $body .= '<div class="notice notice-warn"><strong>Attention needed before installation can continue.</strong><br><span class="small-muted">Nothing has been changed. Correct the items above, refresh this page, and Guild CMS will check again.</span></div>';
        } else {
            $body .= '<div class="notice notice-good"><strong>Your server passed the required checks.</strong><br><span class="small-muted">You are ready to continue to recommended features.</span></div>';
        }

        return $this->panel($body . $this->help('Why are these checked first?', 'If Guild CMS needs something to run, you deserve to know immediately. The installer should never wait until the end to report a blocking server problem.'));
    }
}
