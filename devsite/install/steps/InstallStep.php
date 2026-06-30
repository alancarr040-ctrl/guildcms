<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class InstallStep extends AbstractInstallerStep
{
    public function key(): string { return 'install'; }
    public function title(): string { return 'Install'; }
    public function summary(): string { return 'Show installation progress while permanent changes are performed.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Installation progress</h2>' . $this->paragraph('This package does not yet write permanent changes. Future packages will use this screen to show configuration writing, database setup, administrator creation, module registration, and final verification.') . $this->list(['Create configuration file.', 'Create database tables.', 'Insert default records.', 'Create administrator account.', 'Lock the installer.']));
    }
}
