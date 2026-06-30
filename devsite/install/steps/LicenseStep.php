<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class LicenseStep extends AbstractInstallerStep
{
    public function key(): string { return 'license'; }
    public function title(): string { return 'License'; }
    public function summary(): string { return 'Reserve the license and project terms step.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>License Placeholder</h2>' . $this->paragraph('License review and acceptance handling will be implemented after the framework is verified.'));
    }
}
