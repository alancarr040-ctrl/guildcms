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
                'saved_at' => null,
                'cancelled_at' => null,
                'environment' => null,
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

    public function save(): void
    {
        $this->session['saved_at'] = gmdate('c');
    }

    public function savedAt(): ?string
    {
        $savedAt = $this->session['saved_at'] ?? null;
        return is_string($savedAt) ? $savedAt : null;
    }

    /** @param array<string,mixed> $environment */
    public function setEnvironmentSnapshot(array $environment): void
    {
        $this->session['environment'] = $environment;
        $this->session['environment_detected_at'] = gmdate('c');
    }

    /** @return array<string,mixed>|null */
    public function environmentSnapshot(): ?array
    {
        $environment = $this->session['environment'] ?? null;
        return is_array($environment) ? $environment : null;
    }

    public function environmentDetectedAt(): ?string
    {
        $detectedAt = $this->session['environment_detected_at'] ?? null;
        return is_string($detectedAt) ? $detectedAt : null;
    }

    public function cancel(): void
    {
        $this->session = [
            'started_at' => gmdate('c'),
            'completed_steps' => [],
            'saved_at' => null,
            'cancelled_at' => gmdate('c'),
            'environment' => null,
            'environment_detected_at' => null,
        ];
    }
}

