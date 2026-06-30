<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class WelcomeStep extends AbstractInstallerStep
{
    public function key(): string { return 'welcome'; }
    public function title(): string { return 'Welcome'; }
    public function summary(): string { return 'Start the Guild CMS installation workflow.'; }

    public function render(Installer $installer): string
    {
        return $this->panel(
            '<h2>Welcome to the Guild CMS Installer</h2>' .
            $this->paragraph('This package establishes the installer framework only. Later Phase 4.4 packages will add requirements checking, configuration writing, database bootstrap, administrator creation, and final install locking.') .
            $this->paragraph('The framework already proves the installer route, step registration, shared layout, navigation model, and session-backed state container.')
        );
    }
}
