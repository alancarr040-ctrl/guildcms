<?php
declare(strict_types=1);

namespace GuildCMS\Installer\Steps;

use GuildCMS\Installer\AbstractInstallerStep;
use GuildCMS\Installer\Installer;

final class WelcomeStep extends AbstractInstallerStep
{
    public function key(): string { return 'welcome'; }
    public function title(): string { return 'Welcome to Guild CMS'; }
    public function summary(): string { return 'Start with a clear introduction to what Guild CMS will do and what to expect.'; }

    public function render(Installer $installer): string
    {
        return $this->panel(
            '<h2>We will guide you through the installation.</h2>' .
            $this->paragraph('This installer is both an introduction and a setup tool. Each step explains what is happening, why it matters, what information is needed, and what happens next.') .
            $this->list([
                'Verify your server is ready for Guild CMS.',
                'Review recommended features that improve the experience.',
                'Connect to your database.',
                'Prepare the configuration file that identifies this site.',
                'Create the first administrator and choose basic site settings.',
                'Review everything before the installer writes permanent changes.',
            ]) .
            '<div class="notice notice-good"><strong>No permanent changes happen until the Install step.</strong><br><span class="small-muted">You can go back, save your place, cancel, or refresh after fixing a problem.</span></div>' .
            $this->help('Why does Guild CMS start with an introduction?', 'Installation is the first experience most administrators have with Guild CMS. We want that experience to be educational, professional, modern, and accessible.')
        );
    }
}
