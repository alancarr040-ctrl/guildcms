<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class ConfigurationStep extends AbstractInstallerStep
{
    public function key(): string { return 'configuration'; }
    public function title(): string { return 'Configuration'; }
    public function summary(): string { return 'Prepare the generated configuration file.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Create the site configuration</h2>' . $this->paragraph('The installer will generate includes/config.inc.php. Guild CMS does not expect a new administrator to hand-write that file.') . $this->help('Why not ship a completed config file?', 'A completed configuration belongs to one installation. Shipping one would be confusing and could expose source-site assumptions.'));
    }
}
