<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class FinalizeStep extends AbstractInstallerStep
{
    public function key(): string { return 'finalize'; }
    public function title(): string { return 'Finalize'; }
    public function summary(): string { return 'Prepare completion and installer lock workflow.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Finalize Placeholder</h2>' . $this->paragraph('Finalization will eventually verify installation health, write installer lock state, and direct the user to the new Admin Dashboard.'));
    }
}
