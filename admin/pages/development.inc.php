<?php
declare(strict_types=1);

/*
 * TheRegs Development Center
 * Version 0.9.1 - Engineering Publication Links
 *
 * Admin route:
 *   /admin/?page=development
 */

global $user, $request;

if (!defined('IN_PHPBB')) {
    exit;
}

$config_path = dirname(__DIR__, 2) . '/includes/config.inc.php';

if (!is_file($config_path)) {
    echo '<div class="alert alert-danger">Development Center could not find includes/config.inc.php.</div>';
    return;
}

require_once $config_path;

if (!isset($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS)) {
    echo '<div class="alert alert-danger">Development Center database config variables are not available.</div>';
    return;
}

$dev_mysqli = @new mysqli((string) $DB_HOST, (string) $DB_USER, (string) $DB_PASS, (string) $DB_NAME);

if ($dev_mysqli->connect_errno) {
    echo '<div class="alert alert-danger">Development Center database connection failed: '
        . htmlspecialchars($dev_mysqli->connect_error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        . '</div>';
    return;
}

$dev_mysqli->set_charset('utf8mb4');

if (!function_exists('devcenter_h')) {
    function devcenter_h($value): string
    {
        if (function_exists('h')) {
            return h((string) $value);
        }

        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('devcenter_table_exists')) {
    function devcenter_table_exists(mysqli $conn, string $table): bool
    {
        $sql = "SELECT COUNT(*) AS c
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = ?";

        $stmt = $conn->prepare($sql);

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

if (!function_exists('devcenter_query_all')) {
    function devcenter_query_all(mysqli $conn, string $sql): array
    {
        $result = $conn->query($sql);

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

if (!function_exists('devcenter_badge')) {
    function devcenter_badge(string $status): string
    {
        $classes = [
            'complete' => 'success',
            'in_progress' => 'warning text-dark',
            'planned' => 'secondary',
            'deferred' => 'info text-dark',
            'archived' => 'dark',
            'new' => 'primary',
            'accepted' => 'info text-dark',
            'rejected' => 'danger',
            'active' => 'success',
            'draft' => 'secondary',
            'deprecated' => 'warning text-dark',
            'session' => 'primary',
            'release' => 'success',
            'milestone' => 'warning text-dark',
            'note' => 'secondary',
            'critical' => 'danger',
            'high' => 'warning text-dark',
            'normal' => 'primary',
            'low' => 'secondary',
        ];

        $class = $classes[$status] ?? 'secondary';
        $label = ucwords(str_replace('_', ' ', $status));

        return '<span class="badge bg-' . $class . '">' . devcenter_h($label) . '</span>';
    }
}

if (!function_exists('devcenter_progress')) {
    function devcenter_progress(int $progress, bool $show_label = true): string
    {
        $progress = max(0, min(100, $progress));
        $label = $show_label ? devcenter_h($progress . '%') : '';

        return '
            <div class="progress bg-secondary" style="height: 1.1rem;">
                <div class="progress-bar" role="progressbar" style="width: ' . $progress . '%;" aria-valuenow="' . $progress . '" aria-valuemin="0" aria-valuemax="100">
                    ' . $label . '
                </div>
            </div>';
    }
}

if (!function_exists('devcenter_post_value')) {
    function devcenter_post_value(string $name, string $default = ''): string
    {
        global $request;

        return trim((string) $request->variable($name, $default, true));
    }
}

if (!function_exists('devcenter_verify_token')) {
    function devcenter_verify_token(string $form_key): bool
    {
        if (function_exists('admin_verify_form_token')) {
            return (bool) admin_verify_form_token($form_key);
        }

        if (function_exists('check_form_key')) {
            return (bool) check_form_key($form_key);
        }

        return false;
    }
}

if (!function_exists('devcenter_token_html')) {
    function devcenter_token_html(string $form_key): string
    {
        if (function_exists('admin_form_token')) {
            return (string) admin_form_token($form_key);
        }

        if (function_exists('add_form_key')) {
            add_form_key($form_key);
            return '';
        }

        return '';
    }
}

$required_tables = [
    'project_roadmap_phases',
    'project_roadmap_items',
    'project_changelog_entries',
    'project_ideas',
    'project_architecture_notes',
];

$optional_02_tables = [
    'project_development_sessions',
    'project_metrics',
    'project_vision_notes',
];

foreach ($required_tables as $table) {
    if (!devcenter_table_exists($dev_mysqli, $table)) {
        echo '<div class="alert alert-warning">';
        echo '<h2 class="h5">Development Center base schema is not installed yet.</h2>';
        echo '<p>Run:</p>';
        echo '<code>mysql -u DB_USER -p DB_NAME &lt; sql/phase4_1k_development_center.sql</code>';
        echo '</div>';
        return;
    }
}

$v02_ready = true;
foreach ($optional_02_tables as $table) {
    if (!devcenter_table_exists($dev_mysqli, $table)) {
        $v02_ready = false;
    }
}

if (!$v02_ready) {
    echo '<div class="alert alert-warning">';
    echo '<h2 class="h5">Development Center 0.2 upgrade is not installed yet.</h2>';
    echo '<p>Run:</p>';
    echo '<code>mysql -u DB_USER -p DB_NAME &lt; sql/phase4_1k_development_center_0_2.sql</code>';
    echo '</div>';
    return;
}

$tab = $request->variable('tab', 'dashboard');
$valid_tabs = ['dashboard', 'roadmap', 'timeline', 'sessions', 'log', 'ideas', 'security', 'architecture', 'vision', 'engineering_library'];

if (!in_array($tab, $valid_tabs, true)) {
    $tab = 'dashboard';
}

$messages = [];
$errors = [];

if ($request->is_set_post('devcenter_action')) {
    $action = $request->variable('devcenter_action', '');
    $form_key = 'devcenter_' . $action;

    if (!devcenter_verify_token($form_key)) {
        $errors[] = 'Invalid form token or token verification helper unavailable.';
    } elseif ($action === 'add_log') {
        $entry_date = devcenter_post_value('entry_date', date('Y-m-d'));
        $title = devcenter_post_value('title');
        $body = devcenter_post_value('body');
        $phase_key = devcenter_post_value('phase_key');
        $entry_type = devcenter_post_value('entry_type', 'session');
        $allowed_types = ['session', 'release', 'milestone', 'note'];

        if (!in_array($entry_type, $allowed_types, true)) {
            $entry_type = 'session';
        }

        if ($title === '' || $body === '') {
            $errors[] = 'Title and body are required for changelog entries.';
        } else {
            $sql = "INSERT INTO project_changelog_entries
                    (entry_date, title, body, phase_key, entry_type, is_public, created_by_user_id, created_by_username)
                    VALUES (?, ?, ?, ?, ?, 0, ?, ?)";
            $stmt = $dev_mysqli->prepare($sql);

            if ($stmt) {
                $user_id = (int) ($user->data['user_id'] ?? 0);
                $username = (string) ($user->data['username'] ?? 'Admin');

                $stmt->bind_param('sssssis', $entry_date, $title, $body, $phase_key, $entry_type, $user_id, $username);
                $stmt->execute();
                $stmt->close();

                $messages[] = 'Changelog entry added.';
                $tab = 'log';
            } else {
                $errors[] = 'Unable to prepare changelog insert.';
            }
        }
    } elseif ($action === 'add_idea') {
        $title = devcenter_post_value('title');
        $description = devcenter_post_value('description');
        $category = devcenter_post_value('category');
        $priority = devcenter_post_value('priority', 'normal');
        $allowed_priorities = ['low', 'normal', 'high', 'critical'];

        if (!in_array($priority, $allowed_priorities, true)) {
            $priority = 'normal';
        }

        if ($title === '') {
            $errors[] = 'Idea title is required.';
        } else {
            $sql = "INSERT INTO project_ideas
                    (title, description, status, priority, category, created_by_user_id, created_by_username)
                    VALUES (?, ?, 'new', ?, ?, ?, ?)";
            $stmt = $dev_mysqli->prepare($sql);

            if ($stmt) {
                $user_id = (int) ($user->data['user_id'] ?? 0);
                $username = (string) ($user->data['username'] ?? 'Admin');

                $stmt->bind_param('ssssis', $title, $description, $priority, $category, $user_id, $username);
                $stmt->execute();
                $stmt->close();

                $messages[] = 'Idea added.';
                $tab = 'ideas';
            } else {
                $errors[] = 'Unable to prepare idea insert.';
            }
        }
    } elseif ($action === 'add_session') {
        $session_date = devcenter_post_value('session_date', date('Y-m-d'));
        $title = devcenter_post_value('title');
        $phase_key = devcenter_post_value('phase_key');
        $focus = devcenter_post_value('focus');
        $completed = devcenter_post_value('completed');
        $files_changed = devcenter_post_value('files_changed');
        $next_steps = devcenter_post_value('next_steps');

        if ($title === '') {
            $errors[] = 'Session title is required.';
        } else {
            $sql = "INSERT INTO project_development_sessions
                    (session_date, title, phase_key, focus, completed, files_changed, next_steps, status, completed_at, created_by_user_id, created_by_username)
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'complete', NOW(), ?, ?)";
            $stmt = $dev_mysqli->prepare($sql);

            if ($stmt) {
                $user_id = (int) ($user->data['user_id'] ?? 0);
                $username = (string) ($user->data['username'] ?? 'Admin');

                $stmt->bind_param('sssssssis', $session_date, $title, $phase_key, $focus, $completed, $files_changed, $next_steps, $user_id, $username);
                $stmt->execute();
                $stmt->close();

                $messages[] = 'Development session added.';
                $tab = 'sessions';
            } else {
                $errors[] = 'Unable to prepare session insert.';
            }
        }
    }
}

$phase_rows = devcenter_query_all(
    $dev_mysqli,
    "SELECT *
     FROM project_roadmap_phases
     ORDER BY sort_order ASC, id ASC"
);

$item_rows = devcenter_query_all(
    $dev_mysqli,
    "SELECT i.*, p.phase_key
     FROM project_roadmap_items i
     INNER JOIN project_roadmap_phases p ON p.id = i.phase_id
     ORDER BY p.sort_order ASC, i.sort_order ASC, i.id ASC"
);

$items_by_phase = [];

foreach ($item_rows as $item) {
    $items_by_phase[(int) $item['phase_id']][] = $item;
}

$log_rows = devcenter_query_all(
    $dev_mysqli,
    "SELECT *
     FROM project_changelog_entries
     ORDER BY entry_date DESC, id DESC
     LIMIT 25"
);

$idea_rows = devcenter_query_all(
    $dev_mysqli,
    "SELECT *
     FROM project_ideas
     ORDER BY FIELD(priority, 'critical', 'high', 'normal', 'low'), id DESC
     LIMIT 60"
);

$architecture_rows = devcenter_query_all(
    $dev_mysqli,
    "SELECT *
     FROM project_architecture_notes
     ORDER BY sort_order ASC, id ASC"
);

$session_rows = devcenter_query_all(
    $dev_mysqli,
    "SELECT *
     FROM project_development_sessions
     ORDER BY session_date DESC, id DESC
     LIMIT 30"
);

$metric_rows = devcenter_query_all(
    $dev_mysqli,
    "SELECT *
     FROM project_metrics
     ORDER BY metric_group ASC, sort_order ASC, id ASC"
);

$vision_rows = devcenter_query_all(
    $dev_mysqli,
    "SELECT *
     FROM project_vision_notes
     WHERE status = 'active'
     ORDER BY sort_order ASC, id ASC"
);

$total_phases = count($phase_rows);
$complete_phases = 0;
$total_progress = 0;

foreach ($phase_rows as $phase) {
    if (($phase['status'] ?? '') === 'complete') {
        $complete_phases++;
    }

    $total_progress += (int) ($phase['progress'] ?? 0);
}

$overall_progress = $total_phases > 0 ? (int) round($total_progress / $total_phases) : 0;
$current_phase = null;
$next_phase = null;

foreach ($phase_rows as $phase) {
    if (($phase['status'] ?? '') === 'in_progress') {
        $current_phase = $phase;
        break;
    }
}

foreach ($phase_rows as $phase) {
    if (($phase['status'] ?? '') === 'planned') {
        $next_phase = $phase;
        break;
    }
}

if (!$current_phase) {
    $current_phase = $next_phase;
}

$metrics_by_group = [];

foreach ($metric_rows as $metric) {
    $metrics_by_group[(string) $metric['metric_group']][] = $metric;
}

$idea_counts = [];
foreach ($idea_rows as $idea) {
    $status = (string) ($idea['status'] ?? 'new');
    $idea_counts[$status] = ($idea_counts[$status] ?? 0) + 1;
}


/*
 * Phase 4.2 Development Center extensions
 * These are intentionally lightweight and data-driven inside this page.
 * Backlog uses the existing project_ideas table.
 * Security status is a static Phase 4.2 checklist until it becomes database-backed later.
 */
$security_sections = [
    'Headers' => [
        ['label' => 'Shared security headers include', 'status' => 'complete'],
        ['label' => 'X-Content-Type-Options', 'status' => 'complete'],
        ['label' => 'Referrer-Policy', 'status' => 'complete'],
        ['label' => 'Permissions-Policy', 'status' => 'complete'],
        ['label' => 'X-Frame-Options / frame policy', 'status' => 'complete'],
        ['label' => 'Content-Security-Policy asset inventory', 'status' => 'complete'],
        ['label' => 'Content-Security-Policy Report-Only rollout', 'status' => 'complete'],
        ['label' => 'Guild CMS owns application-layer security headers', 'status' => 'complete'],
        ['label' => 'CSP policy tuning', 'status' => 'in_progress'],
    ],
    'Sessions & Cookies' => [
        ['label' => 'Cookie creation audit: no Guild CMS setcookie() usage found', 'status' => 'complete'],
        ['label' => 'Session creation audit: no Guild CMS session_start() usage found', 'status' => 'complete'],
        ['label' => 'phpBB cookie domain/path verified: .theregs.org / /', 'status' => 'complete'],
        ['label' => 'Secure and HttpOnly cookie flags verified', 'status' => 'complete'],
        ['label' => 'Session lifetime and garbage collection verified: 3600 seconds', 'status' => 'complete'],
        ['label' => 'Browser/IP validation reviewed', 'status' => 'complete'],
        ['label' => 'SameSite explicit configuration verification', 'status' => 'planned'],
        ['label' => 'Plan future Guild CMS native session service', 'status' => 'planned'],
    ],
    'Forms & Requests' => [
        ['label' => 'Admin CSRF audit', 'status' => 'complete'],
        ['label' => 'POST action review', 'status' => 'complete'],
        ['label' => 'Public contact form CSRF protection verified', 'status' => 'complete'],
        ['label' => 'Navigation Manager CSRF protection verified', 'status' => 'complete'],
        ['label' => 'Development Center CSRF protection verified', 'status' => 'complete'],
        ['label' => 'Request-class-only standard', 'status' => 'complete'],
    ],
    'Uploads & Files' => [
        ['label' => 'Upload extension allow-list review', 'status' => 'complete'],
        ['label' => 'MIME/type validation review', 'status' => 'complete'],
        ['label' => 'Image verification review', 'status' => 'complete'],
        ['label' => 'Gallery upload hardening remediation', 'status' => 'complete'],
        ['label' => 'Legacy phpBB Gallery remnants removed', 'status' => 'complete'],
        ['label' => 'Legacy 777 directory permissions corrected', 'status' => 'complete'],
        ['label' => 'Legacy 777 file permissions corrected', 'status' => 'complete'],
        ['label' => 'Final Phase 4.2 security review', 'status' => 'complete'],
    ],
];

$security_total = 0;
$security_complete = 0;

foreach ($security_sections as $security_items) {
    foreach ($security_items as $security_item) {
        $security_total++;
        if (($security_item['status'] ?? '') === 'complete') {
            $security_complete++;
        }
    }
}

$security_progress = $security_total > 0 ? (int) round(($security_complete / $security_total) * 100) : 0;

$cookie_session_audit = [
    'Audit Result' => 'PASS - Cookie and session ownership verified for the current phpBB-backed flagship installation.',
    'Current Provider' => 'phpBB owns authentication, session state, and authentication cookies for the flagship installation.',
    'Guild CMS Native Cookies' => 'Not implemented yet. Guild CMS does not call setcookie() and should not issue auth/session cookies while phpBB remains the active provider.',
    'Guild CMS Native Sessions' => 'Not implemented yet. Guild CMS does not call session_start() outside phpBB.',
    'Cookie Configuration' => 'phpBB cookie name phpbb3_regs, domain .theregs.org, path /, Secure enabled, HttpOnly observed on phpBB cookies.',
    'Session Configuration' => 'phpBB session lifetime is 3600 seconds and session garbage collection is 3600 seconds.',
    'Validation Configuration' => 'Browser validation is enabled, IP validation is configured at A.B level, and Forwarded-For checking is disabled.',
    'Header Verification' => 'Security headers are present on the forums response and X-Powered-By is absent.',
    'Remaining Follow-up' => 'SameSite is not explicitly shown in phpBB config and should remain noted for later verification.',
    'Future Requirement' => 'Create a Guild CMS cookie/session service for standalone installs and non-phpBB authentication providers.',
];

$cookie_session_future_requirements = [
    'Secure cookie support',
    'HttpOnly cookie support',
    'SameSite policy support',
    'Configurable session lifetime',
    'Session regeneration / fixation protection',
    'CSRF integration',
    'Pluggable authentication provider support',
];

$csrf_audit = [
    'Audit Result' => 'PASS - No unprotected state-changing POST handlers discovered during the Phase 4.2 audit.',
    'Public Forms' => 'The public contact form uses phpBB form key generation and validation.',
    'Admin Framework' => 'Admin forms use standardized token helpers, including admin_form_token() / admin_verify_form_token() where available.',
    'Navigation Manager' => 'Publish, restore, move, update, toggle, delete, and add forms all include admin_nav_token($nav_form_key).',
    'Development Center' => 'Sessions, Development Log, and Ideas forms all include devcenter_token_html() and verify tokens before processing.',
    'Processing Order' => 'POST handlers determine the action, verify the CSRF token, and only then process the requested state change.',
    'Architecture Note' => 'Current token helpers are phpBB-backed but provide a clean path for a future Guild CMS-native CSRF service.',
];


$csp_audit = [
    'Audit Result' => 'PASS - CSP asset inventory complete and Report-Only policy is operational.',
    'Current Status' => 'Content-Security-Policy-Report-Only is active and Guild CMS is now the application-layer CSP owner. Apache enforced CSP for TheRegs.org has been retired/commented out.',
    'Primary Internal Sources' => 'self, www.theregs.org, cdn.theregs.org',
    'Script Sources Identified' => 'self, cdn.theregs.org, cdn.jsdelivr.net, code.jquery.com, ajax.googleapis.com, and SortableJS from jsDelivr.',
    'Style Sources Identified' => 'self, cdn.theregs.org, cdn.jsdelivr.net, inline styles, and legacy external font CSS references.',
    'Image Sources Identified' => 'self, cdn.theregs.org, lcmaps.anarchy-online.com, i.ytimg.com, img.youtube.com, and data: for browser/UI images where needed.',
    'Frame Sources Identified' => 'youtube-nocookie.com and youtube.com are currently used for embedded videos.',
    'Findings' => 'No unexpected third-party asset sources were identified. Initial YouTube thumbnail reports were resolved by adding i.ytimg.com and img.youtube.com to img-src. Inline scripts/styles remain long-term cleanup items.',
    'Next Step' => 'Continue CSP policy tuning while completing the Upload & File Security Audit. Keep Report-Only until final security review.',
    'Long-Term Goal' => 'Move inline scripts/styles into assets, standardize video embeds on youtube-nocookie.com, reduce legacy external dependencies, and eventually move to an enforcing CSP.',
];

$csp_sources = [
    'self / www.theregs.org',
    'cdn.theregs.org',
    'cdn.jsdelivr.net',
    'code.jquery.com',
    'ajax.googleapis.com',
    'youtube-nocookie.com',
    'youtube.com',
    'i.ytimg.com',
    'img.youtube.com',
    'lcmaps.anarchy-online.com',
    'aoitems.com links only; not currently an asset source',
];

$phase42_summary = [
    'Status' => '100% - Phase 4.2 Security Hardening is complete.',
    'Security Headers' => 'Complete - Application-layer security headers are managed by The Guild CMS.',
    'Cookie & Session Audit' => 'Complete - phpBB remains the current session/authentication provider; Guild CMS issues no native auth/session cookies yet.',
    'CSRF Audit' => 'Complete - No unprotected state-changing POST handlers were discovered during the Phase 4.2 audit.',
    'CSP Audit' => 'Complete - CSP asset inventory is complete and Report-Only CSP is operational.',
    'Upload & File Security Audit' => 'Complete - Current upload locations were audited and the active Guild CMS Gallery upload path was identified.',
    'Filesystem & Permissions Audit' => 'Complete - Legacy 777 permissions were reduced to standard least-privilege permissions.',
    'Gallery Upload Hardening' => 'Complete - Gallery uploads now use stronger validation, MIME checking, and upload-directory execution protection.',
    'Remaining' => 'Phase 4.2 is complete. Ongoing CSP tuning continues as normal maintenance while Phase 4.3 begins.',
];

$phase42_remediation = [
    'Centralized Security Headers',
    'CSP Report-Only Deployment',
    'Gallery Upload Hardening',
    'Legacy phpBB Gallery Cleanup',
    'Legacy Permission Cleanup',
    'Upload Directory Execution Protection',
];


$engineering_publications_base_url = 'https://guildcms.theregs.org/engineering/';
$engineering_publications = [
    [
        'id' => 'GCMS-ENG-000',
        'publication' => 'Publication 0',
        'volume' => 'Founder\'s Note',
        'title' => 'Founder\'s Note',
        'status' => 'published',
        'version' => '1.0',
        'phase' => '4.3.0-3',
        'url' => $engineering_publications_base_url . 'founders-note.php',
    ],
    [
        'id' => 'GCMS-ENG-001',
        'publication' => 'Publication 1',
        'volume' => 'Volume I',
        'title' => 'Guild CMS Constitution',
        'status' => 'published',
        'version' => '1.0',
        'phase' => '4.3.0-4',
        'url' => $engineering_publications_base_url . 'constitution.php',
    ],
    [
        'id' => 'GCMS-ENG-002',
        'publication' => 'Publication 2',
        'volume' => 'Volume II',
        'title' => 'Vision & Mission',
        'status' => 'published',
        'version' => '1.0',
        'phase' => '4.3.0-4',
        'url' => $engineering_publications_base_url . 'vision-mission.php',
    ],
    [
        'id' => 'GCMS-ENG-003',
        'publication' => 'Publication 3',
        'volume' => 'Volume III',
        'title' => 'Engineering Principles',
        'status' => 'published',
        'version' => '1.0',
        'phase' => '4.3.0-5',
        'url' => $engineering_publications_base_url . 'principles.php',
    ],
    [
        'id' => 'GCMS-ENG-004',
        'publication' => 'Publication 4',
        'volume' => 'Volume IV',
        'title' => 'Architecture Standards',
        'status' => 'published',
        'version' => '1.0',
        'phase' => '4.3.0-6',
        'url' => $engineering_publications_base_url . 'architecture-standards.php',
    ],
    [
        'id' => 'GCMS-ENG-005',
        'publication' => 'Publication 5',
        'volume' => 'Volume V',
        'title' => 'Developer Handbook',
        'status' => 'published',
        'version' => '1.0',
        'phase' => '4.3.0-7',
        'url' => $engineering_publications_base_url . 'developer-handbook.php',
    ],
    [
        'id' => 'GCMS-ENG-006',
        'publication' => 'Publication 6',
        'volume' => 'Volume VI',
        'title' => 'Contribution Guide',
        'status' => 'published',
        'version' => '1.0',
        'phase' => '4.3.0-8',
        'url' => $engineering_publications_base_url . 'contribution-guide.php',
    ],
    [
        'id' => 'GCMS-ENG-007',
        'publication' => 'Publication 7',
        'volume' => 'Volume VII',
        'title' => 'Coding Standards',
        'status' => 'published',
        'version' => '1.0',
        'phase' => '4.3.0-8',
        'url' => $engineering_publications_base_url . 'coding-standards.php',
    ],
    [
        'id' => 'GCMS-ENG-008',
        'publication' => 'Publication 8',
        'volume' => 'Volume VIII',
        'title' => 'Security Standards',
        'status' => 'published',
        'version' => '1.0',
        'phase' => '4.3.0-9',
        'url' => $engineering_publications_base_url . 'security-standards.php',
    ],
    [
        'id' => 'GCMS-ENG-009',
        'publication' => 'Publication 9',
        'volume' => 'Volume IX',
        'title' => 'Architecture Decision Records',
        'status' => 'published',
        'version' => '1.0',
        'phase' => '4.3.0-10',
        'url' => $engineering_publications_base_url . 'adr.php',
    ],
    [
        'id' => 'GCMS-ENG-010',
        'publication' => 'Publication 10',
        'volume' => 'Volume X',
        'title' => 'Engineering Roadmap & Publication Framework',
        'status' => 'published',
        'version' => '1.0',
        'phase' => '4.3.0-11',
        'url' => $engineering_publications_base_url . 'future.php',
    ],
    [
        'id' => 'GCMS-ENG-011',
        'publication' => 'Publication 11',
        'volume' => 'Volume XI',
        'title' => 'User Experience & Educational Design Principles',
        'status' => 'published',
        'version' => '1.0',
        'phase' => '4.4.0-3',
        'url' => $engineering_publications_base_url . 'user-experience.php',
    ],
];

$engineering_publication_counts = [];
foreach ($engineering_publications as $publication) {
    $publication_status = (string) ($publication['status'] ?? 'planned');
    $engineering_publication_counts[$publication_status] = ($engineering_publication_counts[$publication_status] ?? 0) + 1;
}

$backlog_counts_by_category = [];
$backlog_counts_by_priority = [];

foreach ($idea_rows as $idea) {
    $category = trim((string) ($idea['category'] ?? ''));
    if ($category === '') {
        $category = 'Uncategorized';
    }

    $priority = trim((string) ($idea['priority'] ?? 'normal'));
    if ($priority === '') {
        $priority = 'normal';
    }

    $backlog_counts_by_category[$category] = ($backlog_counts_by_category[$category] ?? 0) + 1;
    $backlog_counts_by_priority[$priority] = ($backlog_counts_by_priority[$priority] ?? 0) + 1;
}

arsort($backlog_counts_by_category);
ksort($backlog_counts_by_priority);

?>
<div class="container-fluid py-3 devcenter-admin">
    <style>
        .devcenter-admin .devcenter-hero {
            background: linear-gradient(135deg, rgba(13,110,253,.18), rgba(25,135,84,.12));
            border: 1px solid rgba(255,255,255,.12);
            border-radius: .75rem;
        }

        .devcenter-admin .metric-card {
            min-height: 118px;
        }

        .devcenter-admin .phase-line {
            border-left: 3px solid rgba(13,110,253,.75);
            padding-left: 1rem;
        }

        .devcenter-admin .phase-line.complete {
            border-left-color: rgba(25,135,84,.9);
        }

        .devcenter-admin .phase-line.planned {
            border-left-color: rgba(108,117,125,.9);
        }

        .devcenter-admin .timeline-item {
            border-left: 3px solid rgba(13,110,253,.65);
            padding-left: 1rem;
            margin-left: .4rem;
        }

        .devcenter-admin .timeline-dot {
            width: .7rem;
            height: .7rem;
            border-radius: 50%;
            background: #0d6efd;
            margin-left: -1.45rem;
            margin-bottom: -.7rem;
        }

        .devcenter-admin .kanban-column {
            min-height: 160px;
        }
    </style>

    <div class="devcenter-hero p-4 mb-3">
        <div class="row g-3 align-items-center">
            <div class="col-lg-8">
                <h1 class="h2 mb-1">Development Center <span class="badge bg-info text-dark">v0.9.2</span></h1>
                <div class="text-secondary mb-3">The Guild CMS roadmap, changelog, backlog, security status, sessions, architecture tracker, and engineering governance center. Volume I of the Engineering Library is now complete.</div>
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="small text-secondary">Current Phase</div>
                        <div class="h5 mb-0"><?= devcenter_h($current_phase['title'] ?? 'None') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-secondary">Next Phase</div>
                        <div class="h5 mb-0"><?= devcenter_h($next_phase['title'] ?? 'None') ?></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="small text-secondary">Overall Roadmap Progress</div>
                <?= devcenter_progress($overall_progress) ?>
                <div class="small text-secondary mt-2"><?= (int) $complete_phases ?> of <?= (int) $total_phases ?> phases complete</div>
            </div>
        </div>
    </div>

    <div class="card bg-dark border-info text-light mb-3">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <strong>Phase 4.3 Project Realignment</strong>
            <span class="badge bg-info text-dark">Engineering Foundation &amp; Governance</span>
        </div>
        <div class="card-body">
            <p class="mb-3">Guild CMS has entered Phase 4.3. This phase establishes the public Engineering Library and makes project governance, architecture standards, engineering principles, and documentation standards first-class parts of the platform.</p>
            <div class="row g-3">
                <div class="col-md-3"><div class="border border-secondary rounded p-3 h-100"><div class="small text-secondary">Current Phase</div><div class="h5 mb-0">4.3</div></div></div>
                <div class="col-md-3"><div class="border border-secondary rounded p-3 h-100"><div class="small text-secondary">Current Milestone</div><div class="h5 mb-0">4.3.0</div></div></div>
                <div class="col-md-3"><div class="border border-secondary rounded p-3 h-100"><div class="small text-secondary">Status</div><div class="h5 mb-0">Project Realignment</div></div></div>
                <div class="col-md-3"><div class="border border-secondary rounded p-3 h-100"><div class="small text-secondary">Library</div><div class="h5 mb-0">Public</div></div></div>
            </div>
        </div>
    </div>

    <?php foreach ($messages as $message): ?>
        <div class="alert alert-success"><?= devcenter_h($message) ?></div>
    <?php endforeach; ?>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger"><?= devcenter_h($error) ?></div>
    <?php endforeach; ?>

    <ul class="nav nav-tabs mb-3">
        <?php
        $tabs = [
            'dashboard' => 'Dashboard',
            'roadmap' => 'Roadmap',
            'timeline' => 'Timeline',
            'sessions' => 'Sessions',
            'log' => 'Development Log',
            'ideas' => 'Backlog',
            'security' => 'Security',
            'architecture' => 'Architecture',
            'vision' => 'Vision',
            'engineering_library' => 'Engineering Library',
        ];
        ?>
        <?php foreach ($tabs as $key => $label): ?>
            <li class="nav-item">
                <a class="nav-link <?= $tab === $key ? 'active' : '' ?>" href="?page=development&amp;tab=<?= devcenter_h($key) ?>">
                    <?= devcenter_h($label) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($tab === 'dashboard'): ?>
        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-light metric-card">
                    <div class="card-body">
                        <div class="text-secondary small">Phases Complete</div>
                        <div class="display-6"><?= (int) $complete_phases ?>/<?= (int) $total_phases ?></div>
                        <div><?= devcenter_badge((string) ($current_phase['status'] ?? 'planned')) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-light metric-card">
                    <div class="card-body">
                        <div class="text-secondary small">Roadmap Items</div>
                        <div class="display-6"><?= count($item_rows) ?></div>
                        <div class="small text-secondary">tracked milestones</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-light metric-card">
                    <div class="card-body">
                        <div class="text-secondary small">Security Progress</div>
                        <div class="display-6"><?= (int) $security_progress ?>%</div>
                        <div><span class="badge bg-primary"><?= (int) $security_complete ?>/<?= (int) $security_total ?> items</span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-light metric-card">
                    <div class="card-body">
                        <div class="text-secondary small">Backlog</div>
                        <div class="display-6"><?= count($idea_rows) ?></div>
                        <div class="small text-secondary">tracked ideas</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-7">
                <div class="card bg-dark border-secondary text-light h-100">
                    <div class="card-header">Project Roadmap</div>
                    <div class="card-body">
                        <?php foreach ($phase_rows as $phase): ?>
                            <div class="phase-line <?= devcenter_h((string) $phase['status']) ?> mb-3">
                                <div class="d-flex justify-content-between gap-3 mb-1">
                                    <div>
                                        <strong><?= devcenter_h($phase['title']) ?></strong>
                                        <?= devcenter_badge((string) $phase['status']) ?>
                                    </div>
                                    <div class="text-secondary"><?= (int) $phase['progress'] ?>%</div>
                                </div>
                                <?= devcenter_progress((int) $phase['progress'], false) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card bg-dark border-secondary text-light h-100">
                    <div class="card-header">Project Health Metrics</div>
                    <div class="card-body">
                        <?php foreach ($metrics_by_group as $group => $metrics): ?>
                            <div class="mb-3">
                                <div class="text-secondary small mb-2"><?= devcenter_h($group) ?></div>
                                <div class="row g-2">
                                    <?php foreach ($metrics as $metric): ?>
                                        <div class="col-6">
                                            <div class="border border-secondary rounded p-2">
                                                <div class="small text-secondary"><?= devcenter_h($metric['metric_label']) ?></div>
                                                <div class="h4 mb-0"><?= devcenter_h($metric['metric_value']) ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-xl-6">
                <div class="card bg-dark border-secondary text-light h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Security Status</span>
                        <a class="btn btn-sm btn-outline-info" href="?page=development&amp;tab=security">View Details</a>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between small text-secondary mb-1">
                            <span>Phase 4.2 hardening completion</span>
                            <span><?= (int) $security_progress ?>%</span>
                        </div>
                        <?= devcenter_progress((int) $security_progress) ?>

                        <div class="row g-2 mt-3">
                            <?php foreach ($security_sections as $section_name => $security_items): ?>
                                <?php
                                $section_total = count($security_items);
                                $section_done = 0;
                                foreach ($security_items as $security_item) {
                                    if (($security_item['status'] ?? '') === 'complete') {
                                        $section_done++;
                                    }
                                }
                                ?>
                                <div class="col-md-6">
                                    <div class="border border-secondary rounded p-2 h-100">
                                        <div class="d-flex justify-content-between gap-2">
                                            <strong><?= devcenter_h($section_name) ?></strong>
                                            <span class="badge bg-secondary"><?= (int) $section_done ?>/<?= (int) $section_total ?></span>
                                        </div>
                                        <div class="small text-secondary mt-1">
                                            <?= (int) round($section_total > 0 ? ($section_done / $section_total) * 100 : 0) ?>% complete
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card bg-dark border-secondary text-light h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Backlog Summary</span>
                        <a class="btn btn-sm btn-outline-info" href="?page=development&amp;tab=ideas">View Backlog</a>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-3">
                            <?php foreach (['new', 'accepted', 'planned', 'in_progress', 'complete', 'deferred'] as $summary_status): ?>
                                <div class="col-6 col-md-4">
                                    <div class="border border-secondary rounded p-2">
                                        <div class="small text-secondary"><?= devcenter_h(ucwords(str_replace('_', ' ', $summary_status))) ?></div>
                                        <div class="h4 mb-0"><?= (int) ($idea_counts[$summary_status] ?? 0) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($backlog_counts_by_category): ?>
                            <div class="small text-secondary mb-2">Top Categories</div>
                            <?php foreach (array_slice($backlog_counts_by_category, 0, 6, true) as $category => $count): ?>
                                <div class="d-flex justify-content-between border-bottom border-secondary py-1 small">
                                    <span><?= devcenter_h($category) ?></span>
                                    <span class="badge bg-secondary"><?= (int) $count ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-secondary">No backlog items yet.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-6">
                <div class="card bg-dark border-secondary text-light h-100">
                    <div class="card-header">Recent Sessions</div>
                    <div class="card-body">
                        <?php foreach (array_slice($session_rows, 0, 4) as $session): ?>
                            <div class="mb-3 pb-3 border-bottom border-secondary">
                                <div class="text-secondary small"><?= devcenter_h($session['session_date']) ?> · <?= devcenter_h($session['phase_key']) ?></div>
                                <strong><?= devcenter_h($session['title']) ?></strong>
                                <?php if (!empty($session['completed'])): ?>
                                    <div class="small mt-1"><?= nl2br(devcenter_h($session['completed'])) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card bg-dark border-secondary text-light h-100">
                    <div class="card-header">Recent Changelog</div>
                    <div class="card-body">
                        <?php foreach (array_slice($log_rows, 0, 4) as $entry): ?>
                            <div class="mb-3 pb-3 border-bottom border-secondary">
                                <div class="text-secondary small"><?= devcenter_h($entry['entry_date']) ?> · <?= devcenter_h($entry['phase_key']) ?></div>
                                <strong><?= devcenter_h($entry['title']) ?></strong>
                                <div class="small mt-1"><?= nl2br(devcenter_h($entry['body'])) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($tab === 'roadmap'): ?>
        <?php foreach ($phase_rows as $phase): ?>
            <div class="card bg-dark border-secondary text-light mb-3">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div>
                        <strong><?= devcenter_h($phase['title']) ?></strong>
                        <?= devcenter_badge((string) $phase['status']) ?>
                    </div>
                    <div style="min-width: 210px;"><?= devcenter_progress((int) $phase['progress']) ?></div>
                </div>
                <div class="card-body">
                    <?php if (!empty($phase['description'])): ?>
                        <p class="text-secondary"><?= nl2br(devcenter_h($phase['description'])) ?></p>
                    <?php endif; ?>

                    <?php $items = $items_by_phase[(int) $phase['id']] ?? []; ?>
                    <?php if ($items): ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Status</th>
                                        <th style="width: 200px;">Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <strong><?= devcenter_h($item['title']) ?></strong>
                                                <?php if (!empty($item['description'])): ?>
                                                    <div class="small text-secondary"><?= devcenter_h($item['description']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= devcenter_badge((string) $item['status']) ?></td>
                                            <td><?= devcenter_progress((int) $item['progress']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-secondary">No roadmap items yet.</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php elseif ($tab === 'timeline'): ?>
        <div class="card bg-dark border-secondary text-light">
            <div class="card-header">Project Timeline</div>
            <div class="card-body">
                <?php foreach ($log_rows as $entry): ?>
                    <div class="timeline-item mb-4">
                        <div class="timeline-dot"></div>
                        <div class="text-secondary small"><?= devcenter_h($entry['entry_date']) ?> · <?= devcenter_h($entry['phase_key']) ?></div>
                        <h3 class="h6 mb-1"><?= devcenter_h($entry['title']) ?></h3>
                        <div><?= nl2br(devcenter_h($entry['body'])) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php elseif ($tab === 'sessions'): ?>
        <div class="card bg-dark border-secondary text-light mb-3">
            <div class="card-header">Add Development Session</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="devcenter_action" value="add_session">
                    <?= devcenter_token_html('devcenter_add_session') ?>
                    <div class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label">Date</label>
                            <input class="form-control" type="date" name="session_date" value="<?= devcenter_h(date('Y-m-d')) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Phase</label>
                            <input class="form-control" type="text" name="phase_key" placeholder="4.2">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Title</label>
                            <input class="form-control" type="text" name="title" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Focus</label>
                            <textarea class="form-control" name="focus" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Completed</label>
                            <textarea class="form-control" name="completed" rows="4"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Files Changed</label>
                            <textarea class="form-control" name="files_changed" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Next Steps</label>
                            <textarea class="form-control" name="next_steps" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Add Session</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php foreach ($session_rows as $session): ?>
            <div class="card bg-dark border-secondary text-light mb-3">
                <div class="card-header">
                    <span class="text-secondary"><?= devcenter_h($session['session_date']) ?></span>
                    · <strong><?= devcenter_h($session['title']) ?></strong>
                    <?= devcenter_badge((string) $session['status']) ?>
                </div>
                <div class="card-body">
                    <div class="small text-secondary mb-2">Phase: <?= devcenter_h($session['phase_key'] ?: 'n/a') ?></div>
                    <?php if (!empty($session['focus'])): ?>
                        <h4 class="h6">Focus</h4>
                        <p><?= nl2br(devcenter_h($session['focus'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($session['completed'])): ?>
                        <h4 class="h6">Completed</h4>
                        <p><?= nl2br(devcenter_h($session['completed'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($session['files_changed'])): ?>
                        <h4 class="h6">Files Changed</h4>
                        <p><?= nl2br(devcenter_h($session['files_changed'])) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($session['next_steps'])): ?>
                        <h4 class="h6">Next Steps</h4>
                        <p class="mb-0"><?= nl2br(devcenter_h($session['next_steps'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php elseif ($tab === 'log'): ?>
        <div class="card bg-dark border-secondary text-light mb-3">
            <div class="card-header">Add Development Log Entry</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="devcenter_action" value="add_log">
                    <?= devcenter_token_html('devcenter_add_log') ?>
                    <div class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label">Date</label>
                            <input class="form-control" type="date" name="entry_date" value="<?= devcenter_h(date('Y-m-d')) ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Phase</label>
                            <input class="form-control" type="text" name="phase_key" placeholder="4.2">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="entry_type">
                                <option value="session">Session</option>
                                <option value="milestone">Milestone</option>
                                <option value="release">Release</option>
                                <option value="note">Note</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input class="form-control" type="text" name="title" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Body</label>
                            <textarea class="form-control" name="body" rows="5" required></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Add Entry</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php foreach ($log_rows as $entry): ?>
            <div class="card bg-dark border-secondary text-light mb-3">
                <div class="card-header">
                    <span class="text-secondary"><?= devcenter_h($entry['entry_date']) ?></span>
                    · <strong><?= devcenter_h($entry['title']) ?></strong>
                    <?= devcenter_badge((string) $entry['entry_type']) ?>
                </div>
                <div class="card-body">
                    <div class="small text-secondary mb-2">Phase: <?= devcenter_h($entry['phase_key'] ?: 'n/a') ?></div>
                    <?= nl2br(devcenter_h($entry['body'])) ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php elseif ($tab === 'ideas'): ?>
        <div class="card bg-dark border-secondary text-light mb-3">
            <div class="card-header">Add Idea</div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="devcenter_action" value="add_idea">
                    <?= devcenter_token_html('devcenter_add_idea') ?>
                    <div class="row g-2">
                        <div class="col-md-5">
                            <label class="form-label">Title</label>
                            <input class="form-control" type="text" name="title" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <input class="form-control" type="text" name="category">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Priority</label>
                            <select class="form-select" name="priority">
                                <option value="normal">Normal</option>
                                <option value="low">Low</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4"></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Add Idea</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php $idea_statuses = ['new', 'accepted', 'planned', 'in_progress', 'complete', 'deferred']; ?>
        <div class="row g-3 mb-3">
            <?php foreach ($idea_statuses as $idea_status): ?>
                <div class="col-md-2">
                    <div class="card bg-dark border-secondary text-light kanban-column">
                        <div class="card-header small">
                            <?= devcenter_h(ucwords(str_replace('_', ' ', $idea_status))) ?>
                            <span class="badge bg-secondary float-end"><?= (int) ($idea_counts[$idea_status] ?? 0) ?></span>
                        </div>
                        <div class="card-body small">
                            <?php foreach ($idea_rows as $idea): ?>
                                <?php if (($idea['status'] ?? '') !== $idea_status) { continue; } ?>
                                <div class="border border-secondary rounded p-2 mb-2">
                                    <strong><?= devcenter_h($idea['title']) ?></strong>
                                    <div class="text-secondary"><?= devcenter_h($idea['category']) ?></div>
                                    <?= devcenter_badge((string) $idea['priority']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>


    <?php elseif ($tab === 'security'): ?>
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card bg-dark border-secondary text-light metric-card">
                    <div class="card-body">
                        <div class="text-secondary small">Phase 4.2 Completion</div>
                        <div class="display-6"><?= (int) $security_progress ?>%</div>
                        <div><span class="badge bg-primary"><?= (int) $security_complete ?>/<?= (int) $security_total ?> complete</span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark border-secondary text-light metric-card">
                    <div class="card-body">
                        <div class="text-secondary small">Current Focus</div>
                        <div class="h4 mb-1">Complete</div>
                        <div><?= devcenter_badge('in_progress') ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark border-secondary text-light metric-card">
                    <div class="card-body">
                        <div class="text-secondary small">Next Security Step</div>
                        <div class="h4 mb-1">Final Security Review</div>
                        <div class="small text-secondary">Phase 4.3 now begins with Engineering Foundation & Governance</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-dark border-secondary text-light mb-3">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <strong>Security Status</strong>
                <span class="badge bg-primary">Phase 4.2</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between small text-secondary mb-1">
                    <span>Hardening progress</span>
                    <span><?= (int) $security_progress ?>%</span>
                </div>
                <?= devcenter_progress((int) $security_progress) ?>
            </div>
        </div>

        <div class="card bg-dark border-secondary text-light mb-3">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <strong>Phase 4.2 Summary</strong>
                <span class="badge bg-success">Complete</span>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <strong>Phase status:</strong>
                    Security Hardening is complete. Phase 4.3 now begins the Engineering Foundation & Governance workstream.
                </div>

                <div class="row g-3">
                    <?php foreach ($phase42_summary as $summary_label => $summary_value): ?>
                        <div class="col-lg-6">
                            <div class="border border-secondary rounded p-3 h-100">
                                <div class="small text-secondary mb-1"><?= devcenter_h($summary_label) ?></div>
                                <div><?= devcenter_h($summary_value) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr class="border-secondary">

                <h3 class="h6">Completed Remediation</h3>
                <ul class="mb-0">
                    <?php foreach ($phase42_remediation as $remediation_item): ?>
                        <li><?= devcenter_h($remediation_item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="card bg-dark border-secondary text-light mb-3">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <strong>Cookie &amp; Session Audit</strong>
                <span class="badge bg-success">PASS</span>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <strong>Current architecture:</strong>
                    The Guild CMS flagship installation currently delegates login, session state, and authentication cookies to phpBB.
                    This audit verified that behavior and confirmed Guild CMS currently does not issue native auth/session cookies or start native PHP sessions outside phpBB.
                </div>

                <div class="row g-3">
                    <?php foreach ($cookie_session_audit as $audit_label => $audit_value): ?>
                        <div class="col-lg-6">
                            <div class="border border-secondary rounded p-3 h-100">
                                <div class="small text-secondary mb-1"><?= devcenter_h($audit_label) ?></div>
                                <div><?= devcenter_h($audit_value) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr class="border-secondary">

                <div class="row g-3">
                    <div class="col-lg-6">
                        <h3 class="h6">Completed 4.2 Audit Findings</h3>
                        <ul class="mb-0">
                            <li>PASS: phpBB is the current authentication/session provider.</li>
                            <li>PASS: Guild CMS does not call setcookie().</li>
                            <li>PASS: Guild CMS does not call session_start().</li>
                            <li>PASS: phpBB cookie domain is .theregs.org and path is /.</li>
                            <li>PASS: Secure and HttpOnly cookie flags are enabled/observed.</li>
                            <li>PASS: Session lifetime and garbage collection are both 3600 seconds.</li>
                            <li>PASS: Browser validation is enabled and IP validation is set to A.B.</li>
                            <li>FOLLOW-UP: SameSite is not explicitly shown in phpBB config and should be verified later.</li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <h3 class="h6">Future Native Session Service Requirements</h3>
                        <ul class="mb-0">
                            <?php foreach ($cookie_session_future_requirements as $requirement): ?>
                                <li><?= devcenter_h($requirement) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-dark border-secondary text-light mb-3">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <strong>CSRF Audit</strong>
                <span class="badge bg-success">PASS</span>
            </div>
            <div class="card-body">
                <div class="alert alert-success mb-3">
                    <strong>Result:</strong>
                    No unprotected state-changing POST handlers were discovered during the Phase 4.2 CSRF audit.
                </div>

                <div class="row g-3">
                    <?php foreach ($csrf_audit as $audit_label => $audit_value): ?>
                        <div class="col-lg-6">
                            <div class="border border-secondary rounded p-3 h-100">
                                <div class="small text-secondary mb-1"><?= devcenter_h($audit_label) ?></div>
                                <div><?= devcenter_h($audit_value) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr class="border-secondary">

                <h3 class="h6">Verified Coverage</h3>
                <ul class="mb-0">
                    <li>PASS: Public contact form uses phpBB form keys.</li>
                    <li>PASS: Admin framework provides standardized CSRF helpers.</li>
                    <li>PASS: Navigation Manager POST forms include tokens.</li>
                    <li>PASS: Development Center POST forms include tokens.</li>
                    <li>PASS: POST handlers verify submitted tokens before processing actions.</li>
                    <li>PASS: Current implementation remains compatible with future Guild CMS-native CSRF service design.</li>
                </ul>
            </div>
        </div>


        <div class="card bg-dark border-secondary text-light mb-3">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <strong>Content Security Policy Audit</strong>
                <span class="badge bg-success">REPORT-ONLY OPERATIONAL</span>
            </div>
            <div class="card-body">
                <div class="alert alert-warning mb-3">
                    <strong>Rollout note:</strong>
                    CSP is now active in <code>Content-Security-Policy-Report-Only</code> mode and owned by The Guild CMS. Enforcement should wait until remaining security audits and full-section testing are complete.
                </div>

                <div class="row g-3">
                    <?php foreach ($csp_audit as $audit_label => $audit_value): ?>
                        <div class="col-lg-6">
                            <div class="border border-secondary rounded p-3 h-100">
                                <div class="small text-secondary mb-1"><?= devcenter_h($audit_label) ?></div>
                                <div><?= devcenter_h($audit_value) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <hr class="border-secondary">

                <div class="row g-3">
                    <div class="col-lg-6">
                        <h3 class="h6">Identified Source Allow-List Candidates</h3>
                        <ul class="mb-0">
                            <?php foreach ($csp_sources as $source): ?>
                                <li><?= devcenter_h($source) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <h3 class="h6">Known CSP Constraints</h3>
                        <ul class="mb-0">
                            <li>Inline styles are common and currently require <code>'unsafe-inline'</code> for style-src during the first rollout.</li>
                            <li>Inline scripts exist and must be reviewed before removing <code>'unsafe-inline'</code> from script-src.</li>
                            <li>Legacy AO pages still use older external libraries that should be modernized later.</li>
                            <li>YouTube embeds are split between youtube.com and youtube-nocookie.com; standardize later.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <?php foreach ($security_sections as $section_name => $security_items): ?>
                <div class="col-xl-6">
                    <div class="card bg-dark border-secondary text-light h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong><?= devcenter_h($section_name) ?></strong>
                            <span class="badge bg-secondary"><?= count($security_items) ?> items</span>
                        </div>
                        <div class="card-body">
                            <?php foreach ($security_items as $security_item): ?>
                                <?php $is_complete = (($security_item['status'] ?? '') === 'complete'); ?>
                                <div class="d-flex align-items-start gap-2 border-bottom border-secondary py-2">
                                    <span class="<?= $is_complete ? 'text-success' : 'text-secondary' ?>"><?= $is_complete ? '✓' : '☐' ?></span>
                                    <div class="flex-grow-1">
                                        <div><?= devcenter_h($security_item['label']) ?></div>
                                    </div>
                                    <?= devcenter_badge((string) $security_item['status']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php elseif ($tab === 'architecture'): ?>
        <div class="card bg-dark border-info text-light mb-3">
            <div class="card-header">
                <strong>Engineering Governance</strong>
                <span class="text-secondary">· Phase 4.3 Architecture</span>
                <span class="badge bg-info text-dark">active</span>
            </div>
            <div class="card-body">
                <p><strong>The Engineering Library is now the authoritative public source for Guild CMS engineering governance.</strong></p>
                <p>Beginning with Phase 4.3, architecture standards, engineering principles, coding standards, security standards, developer guidance, and Architecture Decision Records should be documented in the Engineering Library rather than existing only in source code or private notes.</p>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <h3 class="h6">Authoritative Areas</h3>
                        <ul class="mb-0">
                            <li>Architecture Standards</li>
                            <li>Engineering Principles</li>
                            <li>Coding Standards</li>
                            <li>Security Standards</li>
                            <li>Architecture Decision Records</li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <h3 class="h6">Governance Purpose</h3>
                        <ul class="mb-0">
                            <li>Make decisions reviewable</li>
                            <li>Keep contributors aligned</li>
                            <li>Preserve long-term project knowledge</li>
                            <li>Prepare Guild CMS for SDK, theme, CLI, API, and provider expansion</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-dark border-secondary text-light mb-3">
            <div class="card-header">
                <strong>Security Ownership Principle</strong>
                <span class="text-secondary">· Phase 4.2 Architecture</span>
                <span class="badge bg-success">active</span>
            </div>
            <div class="card-body">
                <p><strong>Application-layer security is owned by The Guild CMS.</strong></p>
                <p>Infrastructure-level security remains the responsibility of the web server. This keeps The Guild CMS portable across Apache, Nginx, shared hosting, containers, and future standalone installations.</p>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <h3 class="h6">Guild CMS Responsibilities</h3>
                        <ul class="mb-0">
                            <li>Content Security Policy</li>
                            <li>Permissions-Policy</li>
                            <li>Referrer-Policy</li>
                            <li>X-Frame-Options / frame policy</li>
                            <li>X-Content-Type-Options</li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <h3 class="h6">Web Server Responsibilities</h3>
                        <ul class="mb-0">
                            <li>TLS configuration</li>
                            <li>HSTS</li>
                            <li>Compression</li>
                            <li>Static asset caching</li>
                            <li>Transport-level security</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php foreach ($architecture_rows as $note): ?>
            <div class="card bg-dark border-secondary text-light mb-3">
                <div class="card-header">
                    <strong><?= devcenter_h($note['title']) ?></strong>
                    <span class="text-secondary">· <?= devcenter_h($note['category']) ?></span>
                    <?= devcenter_badge((string) $note['status']) ?>
                </div>
                <div class="card-body">
                    <?= nl2br(devcenter_h($note['body'])) ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php elseif ($tab === 'vision'): ?>
        <?php foreach ($vision_rows as $note): ?>
            <div class="card bg-dark border-secondary text-light mb-3">
                <div class="card-header">
                    <strong><?= devcenter_h($note['title']) ?></strong>
                    <span class="text-secondary">· <?= devcenter_h($note['category']) ?></span>
                </div>
                <div class="card-body">
                    <?= nl2br(devcenter_h($note['body'])) ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php elseif ($tab === 'engineering_library'): ?>
        <div class="card bg-dark border-info text-light mb-3">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <strong>Guild CMS Engineering Publications</strong>
                    <div class="small text-success mt-1">GCMS-ENG-000 through GCMS-ENG-011 are published. Volume I is complete and Volume XI now defines Guild CMS product experience principles.</div>
                <span class="badge bg-info text-dark">Knowledge &bull; Architecture &bull; Standards</span>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-8">
                        <h2 class="h4">Guild CMS<br><span class="text-info">Engineering Library</span></h2>
                        <p class="lead mb-3">Knowledge &bull; Architecture &bull; Standards</p>
                        <p class="mb-0">The Development Center tracks Engineering Library publications as project metadata. The public Guild CMS website remains the authoritative home for the actual published documents.</p>
                    </div>
                    <div class="col-lg-4">
                        <div class="border border-info rounded p-3 h-100">
                            <div class="small text-secondary mb-1">Public Library</div>
                            <a class="btn btn-outline-info w-100" href="<?= devcenter_h($engineering_publications_base_url) ?>" target="_blank" rel="noopener">Open Engineering Library</a>
                            <div class="small text-secondary mt-2">Documents open on guildcms.theregs.org.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-light metric-card">
                    <div class="card-body">
                        <div class="text-secondary small">Tracked Publications</div>
                        <div class="display-6"><?= count($engineering_publications) ?></div>
                        <div class="small text-secondary">public library entries</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-light metric-card">
                    <div class="card-body">
                        <div class="text-secondary small">Published</div>
                        <div class="display-6"><?= (int) ($engineering_publication_counts['published'] ?? 0) ?></div>
                        <div class="small text-secondary">available now</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-light metric-card">
                    <div class="card-body">
                        <div class="text-secondary small">Placeholders</div>
                        <div class="display-6"><?= (int) ($engineering_publication_counts['placeholder'] ?? 0) ?></div>
                        <div class="small text-secondary">reserved public URLs</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-secondary text-light metric-card">
                    <div class="card-body">
                        <div class="text-secondary small">Canonical Host</div>
                        <div class="h5 mb-1">guildcms.theregs.org</div>
                        <div class="small text-secondary">single source of published content</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-dark border-success text-light mb-3">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <strong>Volume I Completion Audit</strong>
                <span class="badge bg-success">Complete</span>
            </div>
            <div class="card-body">
                <p class="mb-3">Volume I of the Guild CMS Engineering Library has been reviewed as a complete foundational publication set. GCMS-ENG-011 extends the library into product experience, installer education, accessibility, and administrator confidence. The Development Center records the publication metadata and links to the public Guild CMS site as the authoritative source for each document.</p>
                <div class="row g-3">
                    <div class="col-md-3"><div class="border border-secondary rounded p-3 h-100"><div class="small text-secondary">Published Publications</div><div class="h4 mb-0"><?= (int) ($engineering_publication_counts['published'] ?? 0) ?>/<?= count($engineering_publications) ?></div></div></div>
                    <div class="col-md-3"><div class="border border-secondary rounded p-3 h-100"><div class="small text-secondary">Placeholder Count</div><div class="h4 mb-0"><?= (int) ($engineering_publication_counts['placeholder'] ?? 0) ?></div></div></div>
                    <div class="col-md-3"><div class="border border-secondary rounded p-3 h-100"><div class="small text-secondary">Volume</div><div class="h4 mb-0">I</div></div></div>
                    <div class="col-md-3"><div class="border border-secondary rounded p-3 h-100"><div class="small text-secondary">Status</div><div class="h4 mb-0">Complete</div></div></div>
                </div>
            </div>
        </div>

        <div class="card bg-dark border-secondary text-light mb-3">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <strong>Publication Registry</strong>
                <span class="badge bg-secondary">metadata only</span>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    The Development Center does not duplicate Engineering Library document bodies. It records publication IDs, status, version, planned phase, and links to the public Guild CMS site.
                </div>
                <div class="table-responsive">
                    <table class="table table-dark table-striped table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Publication</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Version</th>
                                <th>Phase</th>
                                <th class="text-end">Public Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($engineering_publications as $publication): ?>
                                <?php
                                    $publication_status = (string) ($publication['status'] ?? 'placeholder');
                                    $publication_badge = $publication_status === 'published' ? 'success' : ($publication_status === 'draft' ? 'warning text-dark' : 'secondary');
                                ?>
                                <tr>
                                    <td><code><?= devcenter_h($publication['id'] ?? '') ?></code></td>
                                    <td>
                                        <div><?= devcenter_h($publication['publication'] ?? '') ?></div>
                                        <div class="small text-secondary"><?= devcenter_h($publication['volume'] ?? '') ?></div>
                                    </td>
                                    <td><?= devcenter_h($publication['title'] ?? '') ?></td>
                                    <td><span class="badge bg-<?= devcenter_h($publication_badge) ?>"><?= devcenter_h(ucwords(str_replace('_', ' ', $publication_status))) ?></span></td>
                                    <td><?= devcenter_h($publication['version'] ?? '') ?></td>
                                    <td><?= devcenter_h($publication['phase'] ?? '') ?></td>
                                    <td class="text-end">
                                        <a class="btn btn-sm btn-outline-info" href="<?= devcenter_h($publication['url'] ?? '#') ?>" target="_blank" rel="noopener">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
