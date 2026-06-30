<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class RequirementsStep extends AbstractInstallerStep
{
    public function key(): string { return 'requirements'; }
    public function title(): string { return 'Requirements'; }
    public function summary(): string { return 'Prepare PHP, extension, permission, and environment checks.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Requirements Framework Placeholder</h2>' . $this->paragraph('The requirements checker will be implemented in a later package. This step is registered now so the installer workflow has a stable architecture.'));
    }
}
