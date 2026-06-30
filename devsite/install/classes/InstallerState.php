<?php
declare(strict_types=1);

namespace GuildCMS\Installer;

final class InstallerState
{
    /** @var array<string,mixed> */
    private array $session;

    /** @param array<string,mixed> $session */
    public function __construct(array &$session)
    {
        if (!isset($session['guildcms_installer']) || !is_array($session['guildcms_installer'])) {
            $session['guildcms_installer'] = [
                'started_at' => gmdate('c'),
                'completed_steps' => [],
            ];
        }

        $this->session =& $session['guildcms_installer'];
    }

    /** @return array<int,string> */
    public function completedSteps(): array
    {
        return array_values(array_filter($this->session['completed_steps'] ?? [], 'is_string'));
    }

    public function isComplete(string $step): bool
    {
        return in_array($step, $this->completedSteps(), true);
    }

    public function markComplete(string $step): void
    {
        if (!$this->isComplete($step)) {
            $this->session['completed_steps'][] = $step;
        }
    }
}
