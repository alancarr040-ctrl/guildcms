<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class ConfigurationStep extends AbstractInstallerStep
{
    public function key(): string { return 'configuration'; }
    public function title(): string { return 'Configuration'; }
    public function summary(): string { return 'Prepare site configuration generation.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Configuration Placeholder</h2>' . $this->paragraph('The configuration writer will eventually generate installation-specific settings without exposing sensitive source-site values.'));
    }
}
