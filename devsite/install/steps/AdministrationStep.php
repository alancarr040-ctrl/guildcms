<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class AdministrationStep extends AbstractInstallerStep
{
    public function key(): string { return 'administration'; }
    public function title(): string { return 'Administration'; }
    public function summary(): string { return 'Prepare the first administrator account.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Who will run this site?</h2>' . $this->paragraph('A later package will create the first administrator account. This account will be able to sign in, configure the site, and manage Guild CMS.') . $this->help('Why create this during installation?', 'A CMS needs a trusted administrator before it can be managed safely.'));
    }
}
