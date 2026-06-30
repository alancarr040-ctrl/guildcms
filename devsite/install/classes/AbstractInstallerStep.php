<?php
declare(strict_types=1);

namespace GuildCMS\Installer;

abstract class AbstractInstallerStep implements InstallerStepInterface
{
    protected function panel(string $body): string
    {
        return '<div class="install-panel">' . $body . '</div>';
    }

    protected function paragraph(string $text): string
    {
        return '<p>' . InstallerView::escape($text) . '</p>';
    }

    public function status(): string
    {
        return 'Framework';
    }
}
