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

    protected function help(string $title, string $body): string
    {
        return '<details class="install-help"><summary>' . InstallerView::escape($title) . '</summary><p>' . InstallerView::escape($body) . '</p></details>';
    }

    protected function list(array $items): string
    {
        $out = '<ul class="install-list">';
        foreach ($items as $item) {
            $out .= '<li>' . InstallerView::escape((string) $item) . '</li>';
        }
        return $out . '</ul>';
    }

    public function status(): string
    {
        return 'Ready';
    }
}
