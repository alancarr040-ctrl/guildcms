<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class ModulesStep extends AbstractInstallerStep
{
    public function key(): string { return 'modules'; }
    public function title(): string { return 'Modules'; }
    public function summary(): string { return 'Prepare default modules for the installation.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Choose the starting capabilities</h2>' . $this->paragraph('A later package will define which core modules are required and which optional modules can be enabled during installation.') . $this->help('Why mention modules during setup?', 'Modules explain how Guild CMS grows without requiring administrators to modify core files.'));
    }
}
