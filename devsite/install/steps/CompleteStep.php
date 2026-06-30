<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class CompleteStep extends AbstractInstallerStep
{
    public function key(): string { return 'complete'; }
    public function title(): string { return 'Welcome to Guild CMS'; }
    public function summary(): string { return 'Finish installation and begin using the new site.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Welcome to Guild CMS, part two.</h2>' . $this->paragraph('When the installer is complete, this page will direct administrators to the new site, the Administration Center, and getting-started guidance.') . '<div class="notice notice-good"><strong>The end of installation is the beginning of the site.</strong><br><span class="small-muted">Guild CMS should leave the administrator more confident than when they began.</span></div>');
    }
}
