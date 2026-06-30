<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class DatabaseStep extends AbstractInstallerStep
{
    public function key(): string { return 'database'; }
    public function title(): string { return 'Database'; }
    public function summary(): string { return 'Prepare the database information Guild CMS will need.'; }

    public function render(Installer $installer): string
    {
        return $this->panel('<h2>Connect to your database</h2>' . $this->paragraph('Guild CMS stores pages, users, settings, modules, and site content in a database. A later package will add the secure connection test and database form.') . $this->list(['Database host, often localhost.', 'Database name.', 'Database username.', 'Database password.']) . $this->help('What if the database connection fails?', 'Guild CMS will explain which part failed when possible, reassure you that nothing has been written, and let you correct the information before trying again.'));
    }
}
