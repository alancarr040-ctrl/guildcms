<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (!function_exists('guildcms_h')) {
    function guildcms_h($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('guildcms_db')) {
    function guildcms_db(): ?mysqli
    {
        if (!is_file(GUILD_CMS_MAIN_CONFIG)) {
            return null;
        }

        require GUILD_CMS_MAIN_CONFIG;

        if (!isset($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS)) {
            return null;
        }

        $db = @new mysqli((string) $DB_HOST, (string) $DB_USER, (string) $DB_PASS, (string) $DB_NAME);

        if ($db->connect_errno) {
            return null;
        }

        $db->set_charset('utf8mb4');

        return $db;
    }
}

if (!function_exists('guildcms_table_exists')) {
    function guildcms_table_exists(mysqli $db, string $table): bool
    {
        $sql = "SELECT COUNT(*) AS c
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?";

        $stmt = $db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param('s', $table);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
        $stmt->close();

        return (int) ($row['c'] ?? 0) > 0;
    }
}

if (!function_exists('guildcms_query_all')) {
    function guildcms_query_all(mysqli $db, string $sql): array
    {
        $result = $db->query($sql);

        if (!$result) {
            return [];
        }

        $rows = [];

        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $result->free();

        return $rows;
    }
}

if (!function_exists('guildcms_badge')) {
    function guildcms_badge(string $status): string
    {
        $classes = [
            'complete' => 'success',
            'in_progress' => 'warning text-dark',
            'planned' => 'secondary',
            'deferred' => 'info text-dark',
            'archived' => 'dark',
            'active' => 'success',
            'draft' => 'secondary',
            'session' => 'primary',
            'release' => 'success',
            'milestone' => 'warning text-dark',
            'note' => 'secondary',
        ];

        $class = $classes[$status] ?? 'secondary';
        $label = ucwords(str_replace('_', ' ', $status));

        return '<span class="badge bg-' . $class . '">' . guildcms_h($label) . '</span>';
    }
}

if (!function_exists('guildcms_progress')) {
    function guildcms_progress(int $progress, bool $show_label = true): string
    {
        $progress = max(0, min(100, $progress));
        $label = $show_label ? guildcms_h($progress . '%') : '';

        return '
            <div class="progress guild-progress" role="progressbar" aria-valuenow="' . $progress . '" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: ' . $progress . '%;">' . $label . '</div>
            </div>';
    }
}

if (!function_exists('guildcms_schema_ready')) {
    function guildcms_schema_ready(mysqli $db): bool
    {
        foreach (['project_roadmap_phases', 'project_roadmap_items', 'project_changelog_entries', 'project_vision_notes', 'project_metrics'] as $table) {
            if (!guildcms_table_exists($db, $table)) {
                return false;
            }
        }

        return true;
    }
}
