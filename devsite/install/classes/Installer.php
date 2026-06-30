<?php
declare(strict_types=1);

namespace GuildCMS\Installer;

final class Installer
{
    /** @var array<string,InstallerStepInterface> */
    private array $steps = [];

    public function __construct(private readonly InstallerState $state)
    {
    }

    public function registerStep(InstallerStepInterface $step): void
    {
        $this->steps[$step->key()] = $step;
    }

    /** @return array<string,InstallerStepInterface> */
    public function steps(): array
    {
        return $this->steps;
    }

    public function state(): InstallerState
    {
        return $this->state;
    }

    public function firstStepKey(): string
    {
        $keys = array_keys($this->steps);
        return $keys[0] ?? 'welcome';
    }

    public function nextStepKey(string $current): ?string
    {
        $keys = array_keys($this->steps);
        $index = array_search($current, $keys, true);

        if ($index === false || !isset($keys[$index + 1])) {
            return null;
        }

        return $keys[$index + 1];
    }

    public function previousStepKey(string $current): ?string
    {
        $keys = array_keys($this->steps);
        $index = array_search($current, $keys, true);

        if ($index === false || $index === 0) {
            return null;
        }

        return $keys[$index - 1];
    }

    public function position(string $current): int
    {
        $keys = array_keys($this->steps);
        $index = array_search($current, $keys, true);
        return $index === false ? 1 : $index + 1;
    }

    public function count(): int
    {
        return count($this->steps);
    }

    public function progressPercent(string $current): int
    {
        if ($this->count() < 1) {
            return 0;
        }

        return (int) round(($this->position($current) / $this->count()) * 100);
    }

    public function render(?string $stepKey = null): string
    {
        $stepKey = $stepKey ?: $this->firstStepKey();
        $step = $this->steps[$stepKey] ?? $this->steps[$this->firstStepKey()];

        ob_start();
        $installer = $this;
        require GUILDCMS_INSTALLER_ROOT . '/templates/layout.php';
        return (string) ob_get_clean();
    }
}
