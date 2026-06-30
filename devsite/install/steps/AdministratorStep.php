<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class AdministratorStep extends AbstractInstallerStep
{
    public function key(): string { return 'administrator'; }
    public function title(): string { return 'Administrator'; }
    public function summary(): string { return 'Prepare first administrator creation.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Administrator Placeholder</h2>' . $this->paragraph('The first administrator workflow will be added after the database and configuration bootstrap layers exist.'));
    }
}
