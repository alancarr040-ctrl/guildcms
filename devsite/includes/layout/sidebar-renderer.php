<?php
declare(strict_types=1);

/*
 * TheRegs Shared Sidebar Renderer
 * Version: 1.0 Stable
 * Build: Phase 2 Final
 *
 * Location:
 *   includes/layout/sidebar-renderer.php
 *
 * Purpose:
 *   One shared renderer for all left sidebars across the site.
 *
 * Important:
 *   Sidebar files should include this file with require, not require_once.
 *   Many layouts render the sidebar twice: mobile offcanvas and desktop sidebar.
 *
 * Expected variable from sidebar-left.php:
 *   $menu
 *
 * Optional variables:
 *   $sidebar_menu   Legacy alias for $menu
 *   $sidebar_debug  When true, emits HTML comments for missing/invalid menu data
 *
 * Supported menu formats:
 *
 * 1. Static link, legacy short form:
 *
 *   ['Home', 'ac/index.php']
 *
 * 2. Static link, explicit form:
 *
 *   [
 *       'type'  => 'link',
 *       'title' => 'Home',
 *       'url'   => 'ac/index.php',
 *   ]
 *
 * 3. Static group:
 *
 *   [
 *       'type'  => 'group',
 *       'title' => 'Allegiance Info',
 *       'items' => [
 *           ['Diplomacy', 'ac/diplomacy.php'],
 *           ['KoS', 'ac/kos.php'],
 *       ],
 *   ]
 *
 * 4. Dynamic article category:
 *
 *   [
 *       'type'     => 'article_category',
 *       'title'    => 'Rules',
 *       'section'  => 'ac',
 *       'category' => 'rules',
 *       'url'      => 'ac/rules.php',
 *   ]
 *
 *   Legacy aliases also supported:
 *
 *   [
 *       'title'            => 'Rules',
 *       'article_category' => 'rules',
 *       'section_key'      => 'ac',
 *       'base_url'         => 'ac/rules.php',
 *   ]
 *
 * 5. Single article/wrapper link:
 *
 *   [
 *       'type'  => 'article',
 *       'title' => 'History',
 *       'url'   => 'ac/history.php',
 *   ]
 *
 *   Legacy nested form also supported:
 *
 *   [
 *       'article' => [
 *           'title' => 'History',
 *           'url'   => 'ac/history.php',
 *       ],
 *   ]
 *
 * 6. Raw HTML block:
 *
 *   [
 *       'type' => 'html',
 *       'html' => '<form>...</form>',
 *   ]
 *
 *   Raw HTML is intended for trusted local sidebar PHP files only.
 */


global $db, $request;

$current_uri = '/';

if (isset($request)) {
    $current_uri = $request->server('REQUEST_URI', '/');
} else {
    $server_uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_UNSAFE_RAW);
    $current_uri = is_string($server_uri) && $server_uri !== '' ? $server_uri : '/';
}

if (!function_exists('theregs_sidebar_h')) {
    function theregs_sidebar_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('theregs_sidebar_normalize_path')) {
    function theregs_sidebar_normalize_path(string $path): string
    {
        $path = '/' . trim($path, '/');

        return $path === '/' ? '/' : $path;
    }
}

if (!function_exists('theregs_sidebar_active')) {
    function theregs_sidebar_active(string $url, string $current_uri, bool $match_prefix = false): bool
    {
        $current_path = theregs_sidebar_normalize_path((string) parse_url($current_uri, PHP_URL_PATH));
        $current_query = (string) parse_url($current_uri, PHP_URL_QUERY);

        $url_path = theregs_sidebar_normalize_path((string) parse_url($url, PHP_URL_PATH));
        $url_query = (string) parse_url($url, PHP_URL_QUERY);

        if ($url_path === '/index.php') {
            if ($current_path !== '/' && $current_path !== '/index.php') {
                return false;
            }
        } elseif ($url_path === '/ac/index.php') {
            if (!in_array($current_path, ['/ac', '/ac/', '/ac/index.php'], true)) {
                return false;
            }
        } elseif ($current_path !== $url_path) {
            if (
                !$match_prefix
                || $url_path === '/'
                || strpos($current_path . '/', $url_path . '/') !== 0
            ) {
                return false;
            }
        }

        if ($url_query !== '') {
            parse_str($url_query, $url_params);
            parse_str($current_query, $current_params);

            foreach ($url_params as $key => $value) {
                if (!isset($current_params[$key]) || (string) $current_params[$key] !== (string) $value) {
                    return false;
                }
            }
        }

        return true;
    }
}

if (!function_exists('theregs_sidebar_item_url')) {
    function theregs_sidebar_item_url(array $item): string
    {
        if (isset($item[1])) {
            return (string) $item[1];
        }

        if (!empty($item['url'])) {
            return (string) $item['url'];
        }

        if (!empty($item['href'])) {
            return (string) $item['href'];
        }

        if (!empty($item['article']) && is_array($item['article']) && !empty($item['article']['url'])) {
            return (string) $item['article']['url'];
        }

        return '';
    }
}

if (!function_exists('theregs_sidebar_item_title')) {
    function theregs_sidebar_item_title(array $item): string
    {
        if (isset($item[0])) {
            return (string) $item[0];
        }

        if (!empty($item['title'])) {
            return (string) $item['title'];
        }

        if (!empty($item['label'])) {
            return (string) $item['label'];
        }

        if (!empty($item['article']) && is_array($item['article']) && !empty($item['article']['title'])) {
            return (string) $item['article']['title'];
        }

        return '';
    }
}


if (!function_exists('theregs_sidebar_is_external_url')) {
    function theregs_sidebar_is_external_url(string $url): bool
    {
        return (bool) preg_match('~^(?:https?:)?//~i', $url)
            || (bool) preg_match('~^[a-z][a-z0-9+.-]*:~i', $url);
    }
}

if (!function_exists('theregs_sidebar_href')) {
    function theregs_sidebar_href(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            return '#';
        }

        if (theregs_sidebar_is_external_url($url)) {
            return $url;
        }

        return '/' . ltrim($url, '/');
    }
}

if (!function_exists('theregs_sidebar_link')) {
    function theregs_sidebar_link(string $label, string $url, string $current_uri, bool $is_child = false, bool $match_prefix = false): void
    {
        if ($label === '' || $url === '') {
            return;
        }

        $is_external = theregs_sidebar_is_external_url($url);
        $active = $is_external ? false : theregs_sidebar_active($url, $current_uri, $match_prefix);

        $classes = 'btn btn-outline-light bg-dark w-100 mb-1 text-start sidebar-link';

        if ($is_child) {
            $classes .= ' sidebar-sublink ps-4';
        }

        if ($active) {
            $classes .= ' sidebar-link-active';
        }

        $href = theregs_sidebar_href($url);

        echo '<a class="' . theregs_sidebar_h($classes) . '" href="' . theregs_sidebar_h($href) . '"';

        if ($is_external) {
            echo ' target="_blank" rel="noopener noreferrer"';
        }

        echo '>';
        echo $active ? '→ ' : ($is_child ? '• ' : '');
        echo theregs_sidebar_h($label);

        if ($is_external) {
            echo ' ↗';
        }

        echo '</a>';
    }
}

if (!function_exists('theregs_sidebar_article_items')) {
    function theregs_sidebar_article_items($db, string $section_key, string $category, string $base_url): array
    {
        if (!isset($db)) {
            return [];
        }

        $section_key = strtolower(trim(preg_replace('/[^a-z0-9_-]+/i', '-', $section_key), '-'));
        $category = strtolower(trim(preg_replace('/[^a-z0-9_-]+/i', '-', $category), '-'));

        if ($section_key === '' || $category === '') {
            return [];
        }

        $items = [];

        $sql = '
            SELECT id, title, slug
            FROM site_articles
            WHERE section_key = "' . $db->sql_escape($section_key) . '"
              AND category = "' . $db->sql_escape($category) . '"
              AND is_active = 1
            ORDER BY sort_order ASC, title ASC, id ASC
        ';

        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result)) {
            $title = trim((string)($row['title'] ?? ''));
            $slug = trim((string)($row['slug'] ?? ''));

            if ($title === '' || $slug === '') {
                continue;
            }

            $separator = (strpos($base_url, '?') === false) ? '?' : '&';

            $items[] = [
                'type' => 'link',
                'title' => $title,
                'url' => rtrim($base_url, '?&') . $separator . 'slug=' . rawurlencode($slug),
            ];
        }

        $db->sql_freeresult($result);

        return $items;
    }
}

if (!function_exists('theregs_sidebar_render_article_group')) {
    function theregs_sidebar_render_article_group(array $item, string $current_uri, $db = null): void
    {
        $title = theregs_sidebar_item_title($item);
        $section_key = (string)($item['section'] ?? $item['section_key'] ?? '');
        $category = (string)($item['category'] ?? $item['article_category'] ?? '');
        $base_url = (string)($item['url'] ?? $item['base_url'] ?? '');

        if ($title === '') {
            $title = ucwords(str_replace(['_', '-'], ' ', $category));
        }

        if ($base_url === '' && $category !== '' && $section_key !== '') {
            $base_url = $section_key . '/' . $category . '.php';
        }

        $children = theregs_sidebar_article_items($db, $section_key, $category, $base_url);
        $group_active = $base_url !== '' && theregs_sidebar_active($base_url, $current_uri, true);

        echo '<div class="sidebar-subheading mt-2 mb-1 ps-2 small fw-bold text-light' . ($group_active ? ' sidebar-subheading-active' : '') . '">';
        echo $group_active ? '▾ ' : '▸ ';
        echo theregs_sidebar_h($title);
        echo '</div>';

        if (!empty($children)) {
            theregs_sidebar_render_items($children, $current_uri, $db, true);
        }
    }
}

if (!function_exists('theregs_sidebar_render_group')) {
    function theregs_sidebar_render_group(array $item, string $current_uri, $db = null, bool $is_child = false): void
    {
        $title = theregs_sidebar_item_title($item);
        $children = $item['items'] ?? [];

        if ($title !== '') {
            echo '<div class="sidebar-subheading mt-2 mb-1 ps-2 small fw-bold text-light">';
            echo theregs_sidebar_h($title);
            echo '</div>';
        }

        if (is_array($children)) {
            theregs_sidebar_render_items($children, $current_uri, $db, true);
        }
    }
}

if (!function_exists('theregs_sidebar_render_items')) {
    function theregs_sidebar_render_items(array $items, string $current_uri, $db = null, bool $is_child = false): void
    {
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $type = (string)($item['type'] ?? '');

            /*
             * Raw HTML block.
             *
             * Used for legacy sidebar widgets such as AO character/item/nano/location searches.
             * These blocks are intentionally emitted as trusted site HTML from local PHP menu files.
             */
            if ($type === 'html' || array_key_exists('html', $item)) {
                echo (string)($item['html'] ?? '');
                continue;
            }

            if ($type === 'article_category' || !empty($item['article_category'])) {
                theregs_sidebar_render_article_group($item, $current_uri, $db);
                continue;
            }

            if ($type === 'article' || !empty($item['article'])) {
                if (!empty($item['article']) && is_array($item['article'])) {
                    $label = theregs_sidebar_item_title($item['article']);
                    $url = theregs_sidebar_item_url($item['article']);
                } else {
                    $label = theregs_sidebar_item_title($item);
                    $url = theregs_sidebar_item_url($item);
                }

                theregs_sidebar_link($label, $url, $current_uri, $is_child);
                continue;
            }

            if ($type === 'group' || (!empty($item['title']) && !empty($item['items']) && is_array($item['items']) && !isset($item[0]))) {
                theregs_sidebar_render_group($item, $current_uri, $db, $is_child);
                continue;
            }

            $label = theregs_sidebar_item_title($item);
            $url = theregs_sidebar_item_url($item);
            $match_prefix = !empty($item['match_prefix']);

            theregs_sidebar_link($label, $url, $current_uri, $is_child, $match_prefix);
        }
    }
}

if ((!isset($menu) || !is_array($menu)) && isset($sidebar_menu) && is_array($sidebar_menu)) {
    $menu = $sidebar_menu;
}

if (!isset($menu) || !is_array($menu)) {
    if (!empty($sidebar_debug)) {
        echo '<!-- TheRegs sidebar debug: $menu / $sidebar_menu is not set or is not an array. -->';
    }
    return;
}
?>

<nav class="sidebar-menu">
    <?php foreach ($menu as $section): ?>
        <?php if (!is_array($section)) { continue; } ?>

        <?php if (!empty($section['title'])): ?>
            <div class="fw-bold text-decoration-underline text-center bg-black mt-3 mb-2 py-1 sidebar-heading">
                <?= theregs_sidebar_h((string) $section['title']) ?>
            </div>
        <?php endif; ?>

        <?php
        if (!empty($section['items']) && is_array($section['items'])) {
            theregs_sidebar_render_items($section['items'], $current_uri, $db ?? null);
        } elseif (!empty($sidebar_debug)) {
            echo '<!-- TheRegs sidebar debug: section has no valid items. -->';
        }
        ?>
    <?php endforeach; ?>
</nav>
