<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class DatabaseStep extends AbstractInstallerStep
{
    public function key(): string { return 'database'; }
    public function title(): string { return 'Database'; }
    public function summary(): string { return 'Prepare database connection and bootstrap workflow.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Database Bootstrap Placeholder</h2>' . $this->paragraph('Connection testing, schema installation, seed data, and migration registration will be added in later Phase 4.4 packages.'));
    }
}
