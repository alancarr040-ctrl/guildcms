<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class SiteSettingsStep extends AbstractInstallerStep
{
    public function key(): string { return 'site-settings'; }
    public function title(): string { return 'Site Settings'; }
    public function summary(): string { return 'Prepare the public identity of the new Guild CMS site.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>How should your site introduce itself?</h2>' . $this->paragraph('Site settings will collect the site name, URL, default email, and basic identity that replaces any source-site assumptions.') . $this->help('Can this be changed later?', 'Yes. Initial settings help the site launch, but administrators should be able to adjust them from the Administration Center.'));
    }
}
