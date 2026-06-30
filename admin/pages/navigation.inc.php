<?php
declare(strict_types=1);

/*
 * TheRegs Admin - Navigation Manager
 * Version: 1.2.6 Phase 3B.4 Part 1
 *
 * Tree UI foundation:
 * - Left: navigation tree
 * - Middle: context properties panel
 * - Right: live preview
 *
 * This keeps the existing database schema and non-AJAX forms.
 */

global $db, $user, $request;

$nav_form_key = 'theregs_admin_navigation';

$section_options = [
    'site' => 'Main Site',
    'ac'   => "Asheron's Call",
    'ao'   => 'Anarchy Online',
    'tsw'  => 'The Secret World',
    'wow'  => 'World of Warcraft',
    'cod'  => 'Call of Duty',
    'coh'  => 'City of Heroes',
    'eve'  => 'Eve Online',
    'fo76' => 'Fallout 76',
];

$item_types = [
    'link'             => 'Static Link',
    'external'         => 'External URL',
    'article'          => 'Single Article',
    'article_category' => 'Article Category',
    'html'             => 'HTML Widget',
];

if (!function_exists('admin_nav_h')) {
    function admin_nav_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('admin_nav_token')) {
    function admin_nav_token(string $form_key): string
    {
        if (function_exists('admin_form_token')) {
            return admin_form_token($form_key);
        }

        if (function_exists('add_form_key')) {
            add_form_key($form_key);
        }

        if (function_exists('build_hidden_fields') && function_exists('generate_form_token')) {
            return build_hidden_fields([
                'creation_time' => time(),
                'form_token' => generate_form_token($form_key),
            ]);
        }

        return '';
    }
}

if (!function_exists('admin_nav_check_token')) {
    function admin_nav_check_token(string $form_key): bool
    {
        if (function_exists('admin_check_form_token')) {
            return (bool) admin_check_form_token($form_key);
        }

        if (function_exists('check_form_key')) {
            return (bool) check_form_key($form_key);
        }

        return true;
    }
}

if (!function_exists('admin_nav_table_exists')) {
    function admin_nav_table_exists($db, string $table): bool
    {
        $sql = "SHOW TABLES LIKE '" . $db->sql_escape($table) . "'";
        $result = $db->sql_query($sql);
        $exists = (bool) $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

        return $exists;
    }
}

if (!function_exists('admin_nav_public_root')) {
    function admin_nav_public_root(): string
    {
        return dirname(__DIR__, 2);
    }
}

if (!function_exists('admin_nav_sidebar_path')) {
    function admin_nav_sidebar_path(string $section): string
    {
        $section = preg_replace('/[^a-z0-9_-]+/i', '', strtolower($section)) ?: 'site';
        return admin_nav_public_root() . '/includes/sites/' . $section . '/sidebar-left.php';
    }
}

if (!function_exists('admin_nav_export_value')) {
    function admin_nav_export_value($value, int $indent = 0): string
    {
        $space = str_repeat(' ', $indent);

        if (is_array($value)) {
            $is_list = array_keys($value) === range(0, count($value) - 1);
            $out = "[\n";

            foreach ($value as $key => $item) {
                $out .= $space . '    ';

                if (!$is_list) {
                    $out .= var_export((string) $key, true) . ' => ';
                }

                $out .= admin_nav_export_value($item, $indent + 4) . ",\n";
            }

            return $out . $space . ']';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return var_export((string) $value, true);
    }
}

if (!function_exists('admin_nav_menu_item')) {
    function admin_nav_menu_item(array $item): array
    {
        $type = (string) ($item['item_type'] ?? 'link');
        $section = (string) ($item['section_key'] ?? 'site');
        $title = (string) ($item['title'] ?? '');

        if ($type === 'html') {
            return [
                'type' => 'html',
                'html' => (string) ($item['html'] ?? ''),
            ];
        }

        if ($type === 'article_category') {
            return [
                'type' => 'article_category',
                'title' => $title,
                'section' => (string) ($item['article_section_key'] ?: $section),
                'category' => (string) ($item['article_category'] ?? ''),
                'url' => (string) ($item['url'] ?: ($section . '/' . ($item['article_category'] ?? '') . '.php')),
            ];
        }

        $url = (string) ($item['url'] ?? '');

        if ($type === 'article' && $url === '') {
            $article_section = (string) ($item['article_section_key'] ?: $section);
            $category = (string) ($item['article_category'] ?? 'articles');
            $slug = (string) ($item['article_slug'] ?? '');

            $url = $article_section . '/' . $category . '.php';

            if ($slug !== '') {
                $url .= '?slug=' . rawurlencode($slug);
            }
        }

        $out = [
            'type' => 'link',
            'title' => $title,
            'url' => $url,
        ];

        if ((int) ($item['target_blank'] ?? 0) === 1) {
            $out['target'] = '_blank';
        }

        return $out;
    }
}

if (!function_exists('admin_nav_load_data')) {
    function admin_nav_load_data($db, string $section): array
    {
        $groups = [];
        $items_by_group = [];

        $sql = "SELECT *
                FROM site_navigation_groups
                WHERE section_key = '" . $db->sql_escape($section) . "'
                ORDER BY sort_order ASC, id ASC";
        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result)) {
            $groups[(int) $row['id']] = $row;
        }

        $db->sql_freeresult($result);

        if ($groups) {
            $ids = array_map('intval', array_keys($groups));

            $sql = 'SELECT *
                    FROM site_navigation_items
                    WHERE group_id IN (' . implode(',', $ids) . ')
                    ORDER BY group_id ASC, parent_id ASC, sort_order ASC, id ASC';
            $result = $db->sql_query($sql);

            while ($row = $db->sql_fetchrow($result)) {
                $items_by_group[(int) $row['group_id']][] = $row;
            }

            $db->sql_freeresult($result);
        }

        return [$groups, $items_by_group];
    }
}

if (!function_exists('admin_nav_load_menu')) {
    function admin_nav_load_menu($db, string $section): array
    {
        [$groups, $items_by_group] = admin_nav_load_data($db, $section);
        $menu = [];

        foreach ($groups as $group_id => $group) {
            if ((int) $group['is_active'] !== 1) {
                continue;
            }

            $items = [];

            foreach ($items_by_group[$group_id] ?? [] as $item) {
                if ((int) $item['is_active'] !== 1 || trim((string) $item['title']) === '') {
                    continue;
                }

                $items[] = admin_nav_menu_item($item);
            }

            $menu[] = [
                'title' => (string) ($group['title'] ?? ''),
                'items' => $items,
            ];
        }

        return $menu;
    }
}

if (!function_exists('admin_nav_generate_sidebar_php')) {
    function admin_nav_generate_sidebar_php($db, string $section): string
    {
        $export = admin_nav_export_value(admin_nav_load_menu($db, $section));

        return "<?php\n"
            . "declare(strict_types=1);\n\n"
            . "/*\n"
            . " * Auto-generated by TheRegs Navigation Manager.\n"
            . " * Section: {$section}\n"
            . " * Generated: " . date('Y-m-d H:i:s') . "\n"
            . " * Manual edits may be overwritten the next time navigation is published.\n"
            . " */\n\n"
            . "\$menu = " . $export . ";\n\n"
            . "require dirname(__DIR__, 2) . '/layout/sidebar-renderer.php';\n";
    }
}

if (!function_exists('admin_nav_render_preview')) {
    function admin_nav_render_preview($db, string $section): void
    {
        $menu = admin_nav_load_menu($db, $section);

        if (!$menu) {
            echo '<div class="text-secondary small">No active navigation items to preview.</div>';
            return;
        }

        echo '<div class="theregs-nav-preview">';

        foreach ($menu as $group) {
            $title = trim((string) ($group['title'] ?? ''));

            if ($title !== '') {
                echo '<div class="preview-group-title">' . admin_nav_h($title) . '</div>';
            }

            foreach (($group['items'] ?? []) as $item) {
                if (($item['type'] ?? '') === 'html') {
                    echo '<div class="preview-html small">' . ($item['html'] ?? '') . '</div>';
                    continue;
                }

                $item_title = trim((string) ($item['title'] ?? ''));

                if ($item_title === '') {
                    continue;
                }

                echo '<div class="preview-item">';
                echo admin_nav_h($item_title);

                if (!empty($item['url'])) {
                    echo '<div class="preview-url">' . admin_nav_h($item['url']) . '</div>';
                }

                echo '</div>';
            }
        }

        echo '</div>';
    }
}

if (!function_exists('admin_nav_swap_sort')) {
    function admin_nav_swap_sort($db, string $table, int $id, string $section, string $direction, ?int $group_id = null): bool
    {
        $where = "section_key = '" . $db->sql_escape($section) . "'";

        if ($group_id !== null) {
            $where .= ' AND group_id = ' . (int) $group_id;
        }

        $sql = "SELECT id, sort_order
                FROM {$table}
                WHERE id = " . (int) $id . "
                AND {$where}";
        $result = $db->sql_query($sql);
        $current = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

        if (!$current) {
            return false;
        }

        $operator = $direction === 'up' ? '<' : '>';
        $order = $direction === 'up' ? 'DESC' : 'ASC';

        $sql = "SELECT id, sort_order
                FROM {$table}
                WHERE {$where}
                AND sort_order {$operator} " . (int) $current['sort_order'] . "
                ORDER BY sort_order {$order}, id {$order}";
        $result = $db->sql_query_limit($sql, 1);
        $other = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

        if (!$other) {
            return false;
        }

        $db->sql_query("UPDATE {$table} SET sort_order = " . (int) $other['sort_order'] . ' WHERE id = ' . (int) $current['id']);
        $db->sql_query("UPDATE {$table} SET sort_order = " . (int) $current['sort_order'] . ' WHERE id = ' . (int) $other['id']);

        return true;
    }
}


if (!function_exists('admin_nav_publish_log_ready')) {
    function admin_nav_publish_log_ready($db): bool
    {
        return admin_nav_table_exists($db, 'site_navigation_publish_log');
    }
}

if (!function_exists('admin_nav_log_publish')) {
    function admin_nav_log_publish($db, string $section, string $target, ?string $backup, string $checksum, string $notes = 'Published from Navigation Manager'): void
    {
        if (!admin_nav_publish_log_ready($db)) {
            return;
        }

        global $user;

        $user_id = isset($user->data['user_id']) ? (int) $user->data['user_id'] : null;
        $username = isset($user->data['username']) ? (string) $user->data['username'] : null;

        $sql = 'INSERT INTO site_navigation_publish_log ' . $db->sql_build_array('INSERT', [
            'section_key' => $section,
            'published_by_user_id' => $user_id,
            'published_by_username' => $username,
            'target_file' => $target,
            'backup_file' => $backup,
            'checksum' => $checksum,
            'notes' => $notes,
        ]);

        $db->sql_query($sql);
    }
}

if (!function_exists('admin_nav_get_publish_log')) {
    function admin_nav_get_publish_log($db, string $section, int $limit = 20): array
    {
        if (!admin_nav_publish_log_ready($db)) {
            return [];
        }

        $rows = [];

        $sql = "SELECT *
                FROM site_navigation_publish_log
                WHERE section_key = '" . $db->sql_escape($section) . "'
                ORDER BY published_at DESC, id DESC";
        $result = $db->sql_query_limit($sql, $limit);

        while ($row = $db->sql_fetchrow($result)) {
            $rows[] = $row;
        }

        $db->sql_freeresult($result);

        return $rows;
    }
}

if (!function_exists('admin_nav_restore_backup')) {
    function admin_nav_restore_backup($db, string $section, int $log_id, string &$message, string &$error): bool
    {
        if (!admin_nav_publish_log_ready($db)) {
            $error = 'Publish log table is not installed.';
            return false;
        }

        $sql = "SELECT *
                FROM site_navigation_publish_log
                WHERE id = " . (int) $log_id . "
                AND section_key = '" . $db->sql_escape($section) . "'";
        $result = $db->sql_query($sql);
        $log = $db->sql_fetchrow($result);
        $db->sql_freeresult($result);

        if (!$log) {
            $error = 'Publish log entry not found.';
            return false;
        }

        $backup = (string) ($log['backup_file'] ?? '');
        $target = admin_nav_sidebar_path($section);

        if ($backup === '' || !is_file($backup)) {
            $error = 'Backup file not found: ' . $backup;
            return false;
        }

        $before_restore = null;

        if (is_file($target)) {
            $before_restore = $target . '.before-restore-' . date('Ymd-His') . '.bak';

            if (!copy($target, $before_restore)) {
                $error = 'Unable to create pre-restore backup: ' . $before_restore;
                return false;
            }
        }

        if (!copy($backup, $target)) {
            $error = 'Unable to restore backup file.';
            return false;
        }

        $checksum = hash_file('sha256', $target) ?: null;
        admin_nav_log_publish($db, $section, $target, $before_restore, (string) $checksum, 'Restored from backup log #' . $log_id);

        $message = 'Navigation restored from backup.';
        return true;
    }
}

if (!function_exists('admin_nav_publish')) {
    function admin_nav_publish($db, string $section, string &$message, string &$error): bool
    {
        $target = admin_nav_sidebar_path($section);
        $dir = dirname($target);

        if (!is_dir($dir) || !is_writable($dir)) {
            $error = 'Target directory is not writable: ' . $dir;
            return false;
        }

        $php = admin_nav_generate_sidebar_php($db, $section);

        if (is_file($target)) {
            $backup = $target . '.nav-publish-' . date('Ymd-His') . '.bak';

            if (!copy($target, $backup)) {
                $error = 'Unable to create backup: ' . $backup;
                return false;
            }
        }

        if (file_put_contents($target, $php) === false) {
            $error = 'Unable to write sidebar file.';
            return false;
        }

        $checksum = hash_file('sha256', $target) ?: hash('sha256', $php);
        admin_nav_log_publish($db, $section, $target, $backup ?? null, (string) $checksum);

        $message = 'Navigation published.';
        return true;
    }
}



if (!function_exists('admin_nav_json_response')) {
    function admin_nav_json_response(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload);
        exit;
    }
}


if (!function_exists('admin_nav_parse_order_csv')) {
    function admin_nav_parse_order_csv(string $raw): array
    {
        $raw = trim($raw);

        if ($raw === '') {
            return [];
        }

        $parts = explode(',', $raw);
        $ids = [];

        foreach ($parts as $part) {
            $id = (int) trim($part);

            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return $ids;
    }
}

if (!function_exists('admin_nav_update_order')) {
    function admin_nav_update_order($db, string $section, string $type, array $order, ?int $group_id = null): void
    {
        $table = $type === 'group' ? 'site_navigation_groups' : 'site_navigation_items';
        $ids = array_values(array_filter(array_map('intval', $order)));

        $sort = 10;

        foreach ($ids as $id) {
            if ($type === 'group') {
                $sql = "UPDATE {$table}
                        SET sort_order = {$sort}
                        WHERE id = {$id}
                        AND section_key = '" . $db->sql_escape($section) . "'";
            } else {
                $sql = "UPDATE {$table}
                        SET sort_order = {$sort},
                            group_id = " . (int) $group_id . "
                        WHERE id = {$id}
                        AND section_key = '" . $db->sql_escape($section) . "'";
            }

            $db->sql_query($sql);
            $sort += 10;
        }
    }
}

$tables_ready =
    admin_nav_table_exists($db, 'site_navigation_groups')
    && admin_nav_table_exists($db, 'site_navigation_items');

$publish_log_ready = admin_nav_table_exists($db, 'site_navigation_publish_log');

$section = preg_replace('/[^a-z0-9_-]+/i', '', strtolower($request->variable('section', 'site'))) ?: 'site';

if (!array_key_exists($section, $section_options)) {
    $section = 'site';
}

$mode = $request->variable('mode', 'tree');
$panel = $request->variable('panel', 'none');
$edit_group_id = $request->variable('edit_group', 0);
$edit_item_id = $request->variable('edit_item', 0);
$message = '';
$error = '';


if ($tables_ready && $request->is_set_post('action')) {
    $action = $request->variable('action', '');

    if (!admin_nav_check_token($nav_form_key)) {
        $error = 'Invalid form submission. Please try again.';
    } elseif ($action === 'reorder_groups') {
        $raw_order = $request->variable('order', '');
        $order = admin_nav_parse_order_csv($raw_order);

        if (!$order) {
            $error = 'Invalid group order payload: ' . admin_nav_h($raw_order);
        } else {
            admin_nav_update_order($db, $section, 'group', $order);
            $message = 'Group order saved.';
        }
    } elseif ($action === 'reorder_items') {
        $group_id = $request->variable('group_id', 0);
        $raw_order = $request->variable('order', '');
        $order = admin_nav_parse_order_csv($raw_order);

        if ($group_id <= 0 || !$order) {
            $error = 'Invalid item order payload: group=' . (int) $group_id . ' order=' . admin_nav_h($raw_order);
        } else {
            admin_nav_update_order($db, $section, 'item', $order, $group_id);
            $message = 'Item order saved.';
        }
    } elseif ($action === 'add_group') {
        $title = trim($request->variable('title', '', true));
        $sort = $request->variable('sort_order', 0);

        $sql = 'INSERT INTO site_navigation_groups ' . $db->sql_build_array('INSERT', [
            'section_key' => $section,
            'title' => $title !== '' ? $title : null,
            'sort_order' => $sort,
            'is_active' => 1,
        ]);
        $db->sql_query($sql);
        $message = 'Group added.';
    } elseif ($action === 'update_group') {
        $group_id = $request->variable('group_id', 0);
        $title = trim($request->variable('title', '', true));
        $sort = $request->variable('sort_order', 0);

        if ($group_id > 0) {
            $sql = 'UPDATE site_navigation_groups
                    SET ' . $db->sql_build_array('UPDATE', [
                        'title' => $title !== '' ? $title : null,
                        'sort_order' => $sort,
                    ]) . '
                    WHERE id = ' . (int) $group_id . "
                    AND section_key = '" . $db->sql_escape($section) . "'";
            $db->sql_query($sql);
            $message = 'Group updated.';
        }
    } elseif ($action === 'delete_group') {
        $group_id = $request->variable('group_id', 0);

        if ($group_id > 0) {
            $db->sql_query('DELETE FROM site_navigation_groups
                WHERE id = ' . (int) $group_id . "
                AND section_key = '" . $db->sql_escape($section) . "'");
            $message = 'Group deleted.';
        }
    } elseif ($action === 'move_group') {
        admin_nav_swap_sort($db, 'site_navigation_groups', $request->variable('group_id', 0), $section, $request->variable('direction', 'up'));
        $message = 'Group moved.';
    } elseif ($action === 'add_item') {
        $group_id = $request->variable('group_id', 0);
        $type = $request->variable('item_type', 'link');
        $title = trim($request->variable('title', '', true));

        if ($group_id <= 0 || $title === '') {
            $error = 'Group and title are required.';
        } elseif (!array_key_exists($type, $item_types)) {
            $error = 'Invalid item type.';
        } else {
            $sql = 'INSERT INTO site_navigation_items ' . $db->sql_build_array('INSERT', [
                'group_id' => $group_id,
                'parent_id' => null,
                'section_key' => $section,
                'item_type' => $type,
                'title' => $title,
                'url' => trim($request->variable('url', '', true)) ?: null,
                'article_section_key' => trim($request->variable('article_section_key', '', true)) ?: null,
                'article_category' => trim($request->variable('article_category', '', true)) ?: null,
                'article_slug' => trim($request->variable('article_slug', '', true)) ?: null,
                'html' => trim($request->variable('html', '', true)) ?: null,
                'target_blank' => $request->variable('target_blank', 0) ? 1 : 0,
                'sort_order' => $request->variable('sort_order', 0),
                'is_active' => 1,
            ]);
            $db->sql_query($sql);
            $message = 'Item added.';
        }
    } elseif ($action === 'update_item') {
        $item_id = $request->variable('item_id', 0);
        $group_id = $request->variable('group_id', 0);
        $type = $request->variable('item_type', 'link');
        $title = trim($request->variable('title', '', true));

        if ($item_id <= 0 || $group_id <= 0 || $title === '') {
            $error = 'Item, group, and title are required.';
        } elseif (!array_key_exists($type, $item_types)) {
            $error = 'Invalid item type.';
        } else {
            $sql = 'UPDATE site_navigation_items
                    SET ' . $db->sql_build_array('UPDATE', [
                        'group_id' => $group_id,
                        'item_type' => $type,
                        'title' => $title,
                        'url' => trim($request->variable('url', '', true)) ?: null,
                        'article_section_key' => trim($request->variable('article_section_key', '', true)) ?: null,
                        'article_category' => trim($request->variable('article_category', '', true)) ?: null,
                        'article_slug' => trim($request->variable('article_slug', '', true)) ?: null,
                        'html' => trim($request->variable('html', '', true)) ?: null,
                        'target_blank' => $request->variable('target_blank', 0) ? 1 : 0,
                        'sort_order' => $request->variable('sort_order', 0),
                    ]) . '
                    WHERE id = ' . (int) $item_id . "
                    AND section_key = '" . $db->sql_escape($section) . "'";
            $db->sql_query($sql);
            $message = 'Item updated.';
        }
    } elseif ($action === 'delete_item') {
        $item_id = $request->variable('item_id', 0);

        if ($item_id > 0) {
            $db->sql_query('DELETE FROM site_navigation_items
                WHERE id = ' . (int) $item_id . "
                AND section_key = '" . $db->sql_escape($section) . "'");
            $message = 'Item deleted.';
        }
    } elseif ($action === 'move_item') {
        admin_nav_swap_sort(
            $db,
            'site_navigation_items',
            $request->variable('item_id', 0),
            $section,
            $request->variable('direction', 'up'),
            $request->variable('group_id', 0)
        );
        $message = 'Item moved.';
    } elseif ($action === 'toggle_group') {
        $id = $request->variable('group_id', 0);
        $active = $request->variable('is_active', 0);

        if ($id > 0) {
            $db->sql_query('UPDATE site_navigation_groups
                SET is_active = ' . (int) $active . '
                WHERE id = ' . (int) $id . "
                AND section_key = '" . $db->sql_escape($section) . "'");
            $message = 'Group updated.';
        }
    } elseif ($action === 'toggle_item') {
        $id = $request->variable('item_id', 0);
        $active = $request->variable('is_active', 0);

        if ($id > 0) {
            $db->sql_query('UPDATE site_navigation_items
                SET is_active = ' . (int) $active . '
                WHERE id = ' . (int) $id . "
                AND section_key = '" . $db->sql_escape($section) . "'");
            $message = 'Item updated.';
        }
    } elseif ($action === 'restore_backup') {
        $log_id = $request->variable('log_id', 0);
        admin_nav_restore_backup($db, $section, $log_id, $message, $error);
        $mode = 'history';
    } elseif ($action === 'publish') {
        admin_nav_publish($db, $section, $message, $error);
        $mode = 'history';
    }
}

if (function_exists('add_form_key')) {
    add_form_key($nav_form_key);
}

$groups = [];
$items_by_group = [];

if ($tables_ready) {
    [$groups, $items_by_group] = admin_nav_load_data($db, $section);
}

$edit_group = null;
$edit_item = null;

if ($edit_group_id > 0 && isset($groups[$edit_group_id])) {
    $edit_group = $groups[$edit_group_id];
    $panel = 'edit_group';
}

if ($edit_item_id > 0) {
    foreach ($items_by_group as $items) {
        foreach ($items as $item) {
            if ((int) $item['id'] === $edit_item_id) {
                $edit_item = $item;
                $panel = 'edit_item';
                break 2;
            }
        }
    }
}

$preview_php = $tables_ready ? admin_nav_generate_sidebar_php($db, $section) : '';
$target_file = admin_nav_sidebar_path($section);
$publish_logs = $tables_ready ? admin_nav_get_publish_log($db, $section, 20) : [];
?>

<style>
.nav-tree-board {
    min-height: 620px;
}
.nav-tree-group {
    border: 1px solid rgba(255,255,255,.18);
    border-radius: .5rem;
    margin-bottom: .75rem;
    background: rgba(0,0,0,.12);
}
.nav-tree-group-header {
    padding: .75rem;
    border-bottom: 1px solid rgba(255,255,255,.12);
}
.nav-tree-item {
    display: flex;
    justify-content: space-between;
    gap: .5rem;
    align-items: center;
    padding: .5rem .75rem;
    border-top: 1px solid rgba(255,255,255,.08);
}
.nav-tree-item:first-child {
    border-top: 0;
}
.nav-tree-item-title {
    font-weight: 600;
}
.nav-tree-meta,
.preview-url {
    color: #9aa4af;
    font-size: .75rem;
}
.nav-tree-actions form {
    display: inline-block;
}
.theregs-nav-preview {
    background: rgba(0, 0, 0, .25);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: .5rem;
    padding: .75rem;
}
.theregs-nav-preview .preview-group-title {
    background: #000;
    color: #fff;
    font-weight: 700;
    text-align: center;
    text-decoration: underline;
    padding: .35rem .5rem;
    margin: .85rem 0 .45rem;
    border-radius: .2rem;
}
.theregs-nav-preview .preview-group-title:first-child {
    margin-top: 0;
}
.theregs-nav-preview .preview-item {
    border: 1px solid rgba(255,255,255,.65);
    border-radius: .35rem;
    padding: .35rem .5rem;
    margin-bottom: .35rem;
    background: rgba(33,37,41,.95);
    color: #fff;
}

.nav-sortable-handle {
    cursor: grab;
    color: #adb5bd;
    margin-right: .35rem;
}
.nav-sortable-handle:active {
    cursor: grabbing;
}
.nav-sortable-ghost {
    opacity: .45;
    background: rgba(13,110,253,.25);
}
.nav-drop-saving {
    outline: 2px dashed #ffc107;
}
.nav-sort-status {
    min-height: 1.25rem;
}


.nav-tree-click {
    display: block;
    padding: .15rem .25rem;
    border-radius: .25rem;
}
.nav-tree-click:hover {
    background: rgba(255,255,255,.08);
}
.nav-tree-click.active {
    background: rgba(13,110,253,.25);
    outline: 1px solid rgba(13,110,253,.65);
}
.nav-tree-toggle {
    border: 0;
    background: transparent;
    color: #adb5bd;
    padding: 0 .25rem 0 0;
}
.nav-tree-toggle:hover {
    color: #fff;
}
.nav-tree-group.is-collapsed .navItemList,
.nav-tree-group.is-collapsed .nav-tree-add-item-row {
    display: none;
}
.nav-tree-group.is-collapsed .nav-tree-toggle-icon {
    display: inline-block;
    transform: rotate(-90deg);
}
.nav-tree-drag-note {
    color: #9aa4af;
}
.nav-tree-actions .legacy-move-control {
    display: none;
}
.no-js .nav-tree-actions .legacy-move-control {
    display: inline-block;
}
.properties-empty {
    border: 1px dashed rgba(255,255,255,.25);
    border-radius: .5rem;
    padding: 1rem;
    background: rgba(0,0,0,.18);
}

</style>

<div class="container-fluid text-light">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h1 class="h3 mb-1">Navigation Manager</h1>
            <p class="text-secondary mb-0">Phase 3B.4 Part 3: cleaner tree selection, collapsible groups, and focused properties.</p>
        </div>

        <?php if ($tables_ready): ?>
            <form method="post" onsubmit="return confirm('Publish this navigation to the live sidebar-left.php file? A backup will be created first.');">
                <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                <input type="hidden" name="action" value="publish">
                <?= admin_nav_token($nav_form_key) ?>
                <button class="btn btn-warning" type="submit">Publish Navigation</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if ($message !== ''): ?>
        <div class="alert alert-success"><?= admin_nav_h($message) ?></div>
    <?php endif; ?>

    <?php if ($tables_ready && !$publish_log_ready): ?>
        <div class="alert alert-secondary">
            Publish history table is not installed yet. Run:
            <code>sql/phase3b_navigation_publish_log.sql</code>
        </div>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?= admin_nav_h($error) ?></div>
    <?php endif; ?>

    <form method="get" class="card bg-dark border-secondary text-light mb-3">
        <input type="hidden" name="page" value="navigation">

        <div class="card-body row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label" for="section">Section</label>
                <select class="form-select" id="section" name="section">
                    <?php foreach ($section_options as $key => $label): ?>
                        <option value="<?= admin_nav_h($key) ?>"<?= $section === $key ? ' selected' : '' ?>>
                            <?= admin_nav_h($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label" for="mode">View</label>
                <select class="form-select" id="mode" name="mode">
                    <option value="tree"<?= $mode === 'tree' ? ' selected' : '' ?>>Tree Editor</option>
                    <option value="preview"<?= $mode === 'preview' ? ' selected' : '' ?>>Generated PHP</option>
                    <option value="history"<?= $mode === 'history' ? ' selected' : '' ?>>Publish History</option>
                </select>
            </div>

            <div class="col-md-2">
                <button class="btn btn-primary w-100" type="submit">View</button>
            </div>
        </div>
    </form>

    <?php if (!$tables_ready): ?>
        <div class="alert alert-warning">Navigation tables are not installed.</div>
    <?php elseif ($mode === 'preview'): ?>
        <div class="card bg-dark border-secondary text-light">
            <div class="card-header"><strong>Generated PHP Preview</strong></div>
            <div class="card-body">
                <p class="text-secondary mb-2">Target: <code><?= admin_nav_h($target_file) ?></code></p>
                <pre class="bg-black text-light border border-secondary rounded p-3" style="white-space: pre-wrap; max-height: 650px; overflow:auto;"><code><?= admin_nav_h($preview_php) ?></code></pre>
            </div>
        </div>
    <?php elseif ($mode === 'history'): ?>
        <div class="card bg-dark border-secondary text-light">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Publish History</strong>
                <a class="btn btn-sm btn-outline-light" href="?page=navigation&amp;section=<?= admin_nav_h($section) ?>&amp;mode=preview">View Generated PHP</a>
            </div>

            <div class="card-body">
                <?php if (!$publish_log_ready): ?>
                    <div class="alert alert-secondary mb-0">
                        Publish log table is not installed. Run <code>sql/phase3b_navigation_publish_log.sql</code>.
                    </div>
                <?php elseif (!$publish_logs): ?>
                    <div class="alert alert-secondary mb-0">
                        No publish history has been recorded for this section yet.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-striped table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Published</th>
                                    <th>User</th>
                                    <th>Notes</th>
                                    <th>Backup</th>
                                    <th>Checksum</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($publish_logs as $log): ?>
                                    <tr>
                                        <td><?= admin_nav_h($log['published_at']) ?></td>
                                        <td><?= admin_nav_h($log['published_by_username']) ?></td>
                                        <td><?= admin_nav_h($log['notes']) ?></td>
                                        <td class="small">
                                            <?php if (!empty($log['backup_file'])): ?>
                                                <code><?= admin_nav_h(basename((string) $log['backup_file'])) ?></code>
                                                <?php if (!is_file((string) $log['backup_file'])): ?>
                                                    <div class="text-warning">Missing on disk</div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-secondary">None</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small">
                                            <code><?= admin_nav_h(substr((string) $log['checksum'], 0, 16)) ?></code>
                                        </td>
                                        <td class="text-end">
                                            <?php if (!empty($log['backup_file']) && is_file((string) $log['backup_file'])): ?>
                                                <form method="post" onsubmit="return confirm('Restore this backup to the live sidebar file? The current live file will be backed up first.');">
                                                    <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                                    <input type="hidden" name="action" value="restore_backup">
                                                    <input type="hidden" name="log_id" value="<?= (int) $log['id'] ?>">
                                                    <?= admin_nav_token($nav_form_key) ?>
                                                    <button class="btn btn-sm btn-outline-warning" type="submit">Restore</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-secondary small">No restore</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="small text-secondary mt-2">
                        Restore copies the selected backup over the live sidebar file and creates a pre-restore backup first.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <div class="col-xl-4">
                <div class="card bg-dark border-secondary text-light nav-tree-board">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong><?= admin_nav_h($section_options[$section]) ?> Tree</strong>
                        <a class="btn btn-sm btn-outline-success" href="?page=navigation&amp;section=<?= admin_nav_h($section) ?>&amp;panel=add_group">+ Group</a>
                    </div>

                    <div class="card-body">
                        <div id="navSortStatus" class="nav-sort-status small nav-tree-drag-note mb-2">Drag groups or items by the ☰ handle to reorder.</div>
                        <?php if (!$groups): ?>
                            <div class="alert alert-secondary mb-0">No navigation groups have been created for this section yet.</div>
                        <?php else: ?>
                            <div id="navGroupList">
                            <?php foreach ($groups as $group_id => $group): ?>
                                <div class="nav-tree-group" data-group-id="<?= (int) $group_id ?>">
                                    <div class="nav-tree-group-header">
                                        <div class="d-flex justify-content-between gap-2">
                                            <div>
                                                <div class="d-flex align-items-start gap-1">
                                                    <button class="nav-tree-toggle" type="button" aria-label="Collapse group">
                                                        <span class="nav-tree-toggle-icon">▾</span>
                                                    </button>

                                                    <a class="text-light text-decoration-none nav-tree-click<?= $edit_group_id === (int) $group_id ? ' active' : '' ?>" href="?page=navigation&amp;section=<?= admin_nav_h($section) ?>&amp;edit_group=<?= (int) $group_id ?>">
                                                        <span class="nav-sortable-handle" title="Drag group">☰</span>
                                                        <strong>📁 <?= admin_nav_h($group['title'] ?: '(Untitled Group)') ?></strong>
                                                    </a>
                                                </div>
                                                <div class="nav-tree-meta">
                                                    Sort <?= (int) $group['sort_order'] ?> ·
                                                    <?= (int) $group['is_active'] === 1 ? 'Active' : 'Hidden' ?>
                                                </div>
                                            </div>

                                            <div class="nav-tree-actions text-end">
                                                <form method="post" class="legacy-move-control">
                                                    <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                                    <input type="hidden" name="action" value="move_group">
                                                    <input type="hidden" name="group_id" value="<?= (int) $group_id ?>">
                                                    <input type="hidden" name="direction" value="up">
                                                    <?= admin_nav_token($nav_form_key) ?>
                                                    <button class="btn btn-sm btn-outline-light" type="submit">↑</button>
                                                </form>
                                                <form method="post" class="legacy-move-control">
                                                    <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                                    <input type="hidden" name="action" value="move_group">
                                                    <input type="hidden" name="group_id" value="<?= (int) $group_id ?>">
                                                    <input type="hidden" name="direction" value="down">
                                                    <?= admin_nav_token($nav_form_key) ?>
                                                    <button class="btn btn-sm btn-outline-light" type="submit">↓</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="navItemList" data-group-id="<?= (int) $group_id ?>">
                                    <?php foreach (($items_by_group[$group_id] ?? []) as $item): ?>
                                        <div class="nav-tree-item" data-item-id="<?= (int) $item['id'] ?>">
                                            <div>
                                                <a class="text-light text-decoration-none nav-tree-click<?= $edit_item_id === (int) $item['id'] ? ' active' : '' ?>" href="?page=navigation&amp;section=<?= admin_nav_h($section) ?>&amp;edit_item=<?= (int) $item['id'] ?>">
                                                    <span class="nav-sortable-handle" title="Drag item">☰</span>
                                                    <span class="nav-tree-item-title">• <?= admin_nav_h($item['title'] ?: '(Untitled Item)') ?></span>
                                                </a>
                                                <div class="nav-tree-meta">
                                                    <?= admin_nav_h($item_types[$item['item_type']] ?? $item['item_type']) ?> ·
                                                    Sort <?= (int) $item['sort_order'] ?> ·
                                                    <?= (int) $item['is_active'] === 1 ? 'Active' : 'Hidden' ?>
                                                </div>
                                            </div>

                                            <div class="nav-tree-actions text-end">
                                                <form method="post" class="legacy-move-control">
                                                    <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                                    <input type="hidden" name="action" value="move_item">
                                                    <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                                    <input type="hidden" name="group_id" value="<?= (int) $group_id ?>">
                                                    <input type="hidden" name="direction" value="up">
                                                    <?= admin_nav_token($nav_form_key) ?>
                                                    <button class="btn btn-sm btn-outline-light" type="submit">↑</button>
                                                </form>
                                                <form method="post" class="legacy-move-control">
                                                    <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                                    <input type="hidden" name="action" value="move_item">
                                                    <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
                                                    <input type="hidden" name="group_id" value="<?= (int) $group_id ?>">
                                                    <input type="hidden" name="direction" value="down">
                                                    <?= admin_nav_token($nav_form_key) ?>
                                                    <button class="btn btn-sm btn-outline-light" type="submit">↓</button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    </div>

                                    <div class="p-2 nav-tree-add-item-row">
                                        <a class="btn btn-sm btn-outline-success w-100" href="?page=navigation&amp;section=<?= admin_nav_h($section) ?>&amp;panel=add_item&amp;group_id=<?= (int) $group_id ?>">
                                            + Add item to <?= admin_nav_h($group['title'] ?: 'group') ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card bg-dark border-secondary text-light sticky-top" style="top: 1rem;">
                    <div class="card-header"><strong>Properties</strong></div>
                    <div class="card-body">
                        <?php if ($panel === 'edit_group' && $edit_group): ?>
                            <form method="post">
                                <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                <input type="hidden" name="action" value="update_group">
                                <input type="hidden" name="group_id" value="<?= (int) $edit_group['id'] ?>">
                                <?= admin_nav_token($nav_form_key) ?>

                                <h2 class="h5">Edit Group</h2>

                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input class="form-control" name="title" maxlength="150" value="<?= admin_nav_h($edit_group['title']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input class="form-control" name="sort_order" type="number" value="<?= (int) $edit_group['sort_order'] ?>">
                                </div>

                                <button class="btn btn-primary" type="submit">Save Group</button>
                                <a class="btn btn-outline-light" href="?page=navigation&amp;section=<?= admin_nav_h($section) ?>">Cancel</a>
                            </form>

                            <hr>

                            <div class="d-flex gap-2">
                                <form method="post">
                                    <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                    <input type="hidden" name="action" value="toggle_group">
                                    <input type="hidden" name="group_id" value="<?= (int) $edit_group['id'] ?>">
                                    <input type="hidden" name="is_active" value="<?= (int) $edit_group['is_active'] === 1 ? 0 : 1 ?>">
                                    <?= admin_nav_token($nav_form_key) ?>
                                    <button class="btn btn-outline-warning" type="submit">
                                        <?= (int) $edit_group['is_active'] === 1 ? 'Hide Group' : 'Show Group' ?>
                                    </button>
                                </form>

                                <form method="post" onsubmit="return confirm('Delete this group and all items inside it?');">
                                    <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                    <input type="hidden" name="action" value="delete_group">
                                    <input type="hidden" name="group_id" value="<?= (int) $edit_group['id'] ?>">
                                    <?= admin_nav_token($nav_form_key) ?>
                                    <button class="btn btn-outline-danger" type="submit">Delete Group</button>
                                </form>
                            </div>
                        <?php elseif ($panel === 'edit_item' && $edit_item): ?>
                            <form method="post">
                                <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                <input type="hidden" name="action" value="update_item">
                                <input type="hidden" name="item_id" value="<?= (int) $edit_item['id'] ?>">
                                <?= admin_nav_token($nav_form_key) ?>

                                <h2 class="h5">Edit Item</h2>
                                <?php include __DIR__ . '/navigation_item_form_fields.inc.php'; ?>
                            </form>

                            <hr>

                            <div class="d-flex gap-2">
                                <form method="post">
                                    <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                    <input type="hidden" name="action" value="toggle_item">
                                    <input type="hidden" name="item_id" value="<?= (int) $edit_item['id'] ?>">
                                    <input type="hidden" name="is_active" value="<?= (int) $edit_item['is_active'] === 1 ? 0 : 1 ?>">
                                    <?= admin_nav_token($nav_form_key) ?>
                                    <button class="btn btn-outline-warning" type="submit">
                                        <?= (int) $edit_item['is_active'] === 1 ? 'Hide Item' : 'Show Item' ?>
                                    </button>
                                </form>

                                <form method="post" onsubmit="return confirm('Delete this navigation item?');">
                                    <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                    <input type="hidden" name="action" value="delete_item">
                                    <input type="hidden" name="item_id" value="<?= (int) $edit_item['id'] ?>">
                                    <?= admin_nav_token($nav_form_key) ?>
                                    <button class="btn btn-outline-danger" type="submit">Delete Item</button>
                                </form>
                            </div>
                        <?php elseif ($panel === 'add_group'): ?>
                            <form method="post">
                                <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                <input type="hidden" name="action" value="add_group">
                                <?= admin_nav_token($nav_form_key) ?>

                                <h2 class="h5">Add Group</h2>

                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input class="form-control" name="title" maxlength="150">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Sort Order</label>
                                    <input class="form-control" name="sort_order" type="number" value="0">
                                </div>

                                <button class="btn btn-success" type="submit">Add Group</button>
                            </form>
                        <?php elseif ($panel === 'add_item'): ?>
                            <?php if (!$groups): ?>
                                <div class="alert alert-secondary mb-0">Create a group first.</div>
                            <?php else: ?>
                                <form method="post">
                                    <input type="hidden" name="section" value="<?= admin_nav_h($section) ?>">
                                    <input type="hidden" name="action" value="add_item">
                                    <?= admin_nav_token($nav_form_key) ?>

                                    <h2 class="h5">Add Item</h2>
                                    <?php $edit_item = null; include __DIR__ . '/navigation_item_form_fields.inc.php'; ?>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="properties-empty">
                                <h2 class="h5">Nothing Selected</h2>
                                <p class="text-secondary mb-3">
                                    Select a group or item from the tree to edit it, or create a new navigation entry.
                                </p>

                                <div class="d-grid gap-2">
                                    <a class="btn btn-outline-success" href="?page=navigation&amp;section=<?= admin_nav_h($section) ?>&amp;panel=add_group">+ Add Group</a>
                                    <?php if ($groups): ?>
                                        <a class="btn btn-outline-success" href="?page=navigation&amp;section=<?= admin_nav_h($section) ?>&amp;panel=add_item">+ Add Item</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card bg-dark border-secondary text-light sticky-top" style="top: 1rem;">
                    <div class="card-header"><strong>Live Sidebar Preview</strong></div>
                    <div class="card-body">
                        <div class="small text-secondary mb-2">
                            Active items only. Publish writes this menu to the live sidebar file.
                        </div>
                        <?php admin_nav_render_preview($db, $section); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>




<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
    const section = <?= json_encode($section) ?>;

    document.querySelectorAll('.nav-tree-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            const group = button.closest('.nav-tree-group');
            if (group) {
                group.classList.toggle('is-collapsed');
            }
        });
    });

    function submitOrder(action, order, groupId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = window.location.href;

        function add(name, value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }

        add('section', section);
        add('action', action);
        add('order', order.join(','));

        if (groupId) {
            add('group_id', groupId);
        }

        const token = document.querySelector('input[name="form_token"]');
        const creation = document.querySelector('input[name="creation_time"]');

        if (token) {
            add('form_token', token.value);
        }

        if (creation) {
            add('creation_time', creation.value);
        }

        document.body.appendChild(form);
        form.submit();
    }

    const groupList = document.getElementById('navGroupList');

    if (groupList && window.Sortable) {
        new Sortable(groupList, {
            animation: 150,
            handle: '.nav-sortable-handle',
            ghostClass: 'nav-sortable-ghost',
            onEnd: function () {
                const order = Array.from(groupList.querySelectorAll(':scope > .nav-tree-group'))
                    .map(function (el) {
                        return el.getAttribute('data-group-id');
                    })
                    .filter(Boolean);

                submitOrder('reorder_groups', order, null);
            }
        });
    }

    document.querySelectorAll('.navItemList').forEach(function (list) {
        if (!window.Sortable) return;

        new Sortable(list, {
            animation: 150,
            handle: '.nav-sortable-handle',
            ghostClass: 'nav-sortable-ghost',
            group: 'navigation-items',
            onEnd: function (evt) {
                const targetList = evt.to || list;
                const groupId = targetList.getAttribute('data-group-id');
                const order = Array.from(targetList.querySelectorAll(':scope > .nav-tree-item'))
                    .map(function (el) {
                        return el.getAttribute('data-item-id');
                    })
                    .filter(Boolean);

                submitOrder('reorder_items', order, groupId);
            }
        });
    });
})();
</script>
