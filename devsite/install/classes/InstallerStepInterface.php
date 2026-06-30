<?php
declare(strict_types=1);

namespace GuildCMS\Installer;

interface InstallerStepInterface
{
    public function key(): string;

    public function title(): string;

    public function summary(): string;

    public function status(): string;

    public function render(Installer $installer): string;
}
