<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class LicenseStep extends AbstractInstallerStep
{
    public function key(): string { return 'license'; }
    public function title(): string { return 'License'; }
    public function summary(): string { return 'Review the software license before installation.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Guild CMS License</h2>' . $this->paragraph('Guild CMS is distributed under the GPL-2.0 license. A complete license acceptance workflow will be implemented in a later package.') . $this->help('Why is the license part of installation?', 'The installer should make important project terms visible before permanent changes are made.'));
    }
}
