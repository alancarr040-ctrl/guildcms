<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class SummaryStep extends AbstractInstallerStep
{
    public function key(): string { return 'summary'; }
    public function title(): string { return 'Summary'; }
    public function summary(): string { return 'Review what Guild CMS will do before anything permanent is written.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Review before installation</h2>' . $this->paragraph('The summary step will show the configuration, database target, administrator, site settings, and selected modules before Guild CMS writes permanent changes.') . '<div class="notice notice-good"><strong>This is the last safe review point.</strong><br><span class="small-muted">The installer is designed so you can go back and correct mistakes before the install phase begins.</span></div>');
    }
}
