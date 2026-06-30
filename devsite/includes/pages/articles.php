<?php
require_once __DIR__ . '/../layout/content-renderer.php';
if (!defined('IN_PHPBB')) {
    exit;
}

/*
 * TheRegs CMS - Shared Articles Engine
 * Version: Phase 1.0 Security Patch
 * Build: 2026-06-25
 *
 * Generic Database Driven Articles
 * File version: Phase 1.0 Security Patch / 2026-06-25
 *
 * Intended locations:
 * /home/theregs/public_html/includes/sites/wow/articles.php
 * /home/theregs/public_html/includes/sites/ac/articles.php
 * /home/theregs/public_html/includes/sites/ao/articles.php
 * etc.
 *
 * Direct page:
 * index.php?site=wow&page=articles&category=news
 *
 * Shared location:
 * /home/theregs/public_html/includes/pages/articles.php
 *
 * Embedded from home.php:
 *
 * $theregs_articles_embedded = true;
 * $theregs_articles_show_sidebars = false;
 * $theregs_articles_show_title = false;
 * $theregs_articles_category = 'news';
 * $theregs_articles_limit = 5;
 * include __DIR__ . '/articles.php';
 */

global $db, $request, $site_sections;

/*
 * Defense-in-depth method guard. The root router should block unsupported
 * methods first, but this prevents article rendering from returning 200 OK
 * if included directly by mistake. Uses phpBB request object; no direct server superglobal.
 */
$allowed_article_methods = ['GET', 'POST', 'HEAD'];
$article_request_method = strtoupper((string) $request->server('REQUEST_METHOD', 'GET'));

if (!in_array($article_request_method, $allowed_article_methods, true)) {
    http_response_code(405);
    header('Allow: GET, POST, HEAD');
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Method Not Allowed';
    exit;
}

if (!function_exists('theregs_escape')) {
    function theregs_escape($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('theregs_include_first_existing')) {
    function theregs_include_first_existing(array $paths)
    {
        foreach ($paths as $path) {
            if (is_file($path) && is_readable($path)) {
                include $path;
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('theregs_table_columns')) {
    function theregs_table_columns($db, $table_name)
    {
        $columns = [];
        $result = $db->sql_query('SHOW COLUMNS FROM ' . $table_name);

        while ($row = $db->sql_fetchrow($result)) {
            if (!empty($row['Field'])) {
                $columns[$row['Field']] = true;
            }
        }

        $db->sql_freeresult($result);

        return $columns;
    }
}

if (!function_exists('theregs_youtube_id_from_url')) {
    function theregs_youtube_id_from_url($url)
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '';
        }

        $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $url = trim($url, " \t\n\r\0\x0B\"'<>");

        if (preg_match('~youtu\.be/([A-Za-z0-9_-]{6,})~i', $url, $matches)) {
            return $matches[1];
        }

        if (preg_match('~[?&]v=([A-Za-z0-9_-]{6,})~i', $url, $matches)) {
            return $matches[1];
        }

        if (preg_match('~youtube\.com/(?:embed|shorts)/([A-Za-z0-9_-]{6,})~i', $url, $matches)) {
            return $matches[1];
        }

        return '';
    }
}

if (!function_exists('theregs_youtube_embed_html')) {
    function theregs_youtube_embed_html($video_id)
    {
        $video_id = preg_replace('/[^A-Za-z0-9_\-]/', '', (string) $video_id);

        if ($video_id === '') {
            return '';
        }

        return '
            <div class="theregs-video-embed">
                <iframe
                    src="https://www.youtube.com/embed/' . theregs_escape($video_id) . '"
                    title="YouTube video player"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                    loading="lazy">
                </iframe>
            </div>
        ';
    }
}

if (!function_exists('theregs_render_legacy_tables')) {
    function theregs_render_legacy_tables($text)
    {
        $text = (string) $text;

        if (stripos($text, '[table') === false) {
            return $text;
        }

        $text = preg_replace('~\[table(?:=[^\]]*)?\]~i', '<div class="table-responsive"><table class="table table-dark table-bordered table-sm theregs-legacy-table">', $text);
        $text = preg_replace('~\[/table\]~i', '</table></div>', $text);
        $text = preg_replace('~\[tr(?:=[^\]]*)?\]~i', '<tr>', $text);
        $text = preg_replace('~\[/tr\]~i', '</tr>', $text);
        $text = preg_replace('~\[td(?:=[^\]]*)?\]~i', '<td>', $text);
        $text = preg_replace('~\[/td\]~i', '</td>', $text);
        $text = preg_replace('~\[th(?:=[^\]]*)?\]~i', '<th>', $text);
        $text = preg_replace('~\[/th\]~i', '</th>', $text);

        return $text;
    }
}

if (!function_exists('theregs_render_legacy_lists_safe')) {
    function theregs_render_legacy_lists_safe($text)
    {
        $text = (string) $text;

        if (stripos($text, '[list') === false && stripos($text, '[*]') === false) {
            return $text;
        }

        $tokens = preg_split(
            '~(\[list(?:=[^\]]+)?\]|\[/list\]|\[\*\])~i',
            $text,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        if (!$tokens) {
            return $text;
        }

        $html = '';
        $stack = [];

        foreach ($tokens as $token) {
            $lower = strtolower($token);

            if (preg_match('~^\[list(?:=([^\]]+))?\]$~i', $token, $matches)) {
                $type = strtolower(trim($matches[1] ?? ''));

                $tag = 'ul';
                $attr = ' class="theregs-legacy-list"';

                if ($type !== '') {
                    $tag = 'ol';

                    if ($type === '1') {
                        $attr = ' class="theregs-legacy-list" type="1"';
                    } elseif ($type === 'a') {
                        $attr = ' class="theregs-legacy-list" type="a"';
                    } elseif ($type === 'A' || strtolower($matches[1] ?? '') === 'a') {
                        $attr = ' class="theregs-legacy-list" type="A"';
                    } elseif ($type === 'i') {
                        $attr = ' class="theregs-legacy-list" type="i"';
                    } elseif ($type === 'I' || strtolower($matches[1] ?? '') === 'i') {
                        $attr = ' class="theregs-legacy-list" type="I"';
                    }
                }

                if (!empty($stack) && $stack[count($stack) - 1]['li_open'] === false) {
                    $html .= '<li>';
                    $stack[count($stack) - 1]['li_open'] = true;
                }

                $html .= '<' . $tag . $attr . '>';
                $stack[] = [
                    'tag' => $tag,
                    'li_open' => false,
                ];

                continue;
            }

            if ($lower === '[*]') {
                if (!empty($stack)) {
                    $level = count($stack) - 1;

                    if ($stack[$level]['li_open']) {
                        $html .= '</li>';
                    }

                    $html .= '<li>';
                    $stack[$level]['li_open'] = true;
                } else {
                    $html .= '<li>';
                }

                continue;
            }

            if ($lower === '[/list]') {
                if (!empty($stack)) {
                    $level = count($stack) - 1;

                    if ($stack[$level]['li_open']) {
                        $html .= '</li>';
                    }

                    $list = array_pop($stack);
                    $html .= '</' . $list['tag'] . '>';

                    if (!empty($stack)) {
                        $parent_level = count($stack) - 1;
                        $stack[$parent_level]['li_open'] = true;
                    }
                }

                continue;
            }

            $html .= $token;
        }

        while (!empty($stack)) {
            $level = count($stack) - 1;

            if ($stack[$level]['li_open']) {
                $html .= '</li>';
            }

            $list = array_pop($stack);
            $html .= '</' . $list['tag'] . '>';
        }

        return $html;
    }
}

if (!function_exists('theregs_render_legacy_article_markup')) {
    function theregs_render_legacy_article_markup($text)
    {
        $text = (string) $text;

        $text = preg_replace('~\[align=center\](.*?)\[/align\]~is', '<div class="text-center">$1</div>', $text);
        $text = preg_replace('~\[center\](.*?)\[/center\]~is', '<div class="text-center">$1</div>', $text);
        $text = preg_replace('~\[align=left\](.*?)\[/align\]~is', '<div class="text-start">$1</div>', $text);
        $text = preg_replace('~\[align=right\](.*?)\[/align\]~is', '<div class="text-end">$1</div>', $text);

        $text = preg_replace_callback(
            '~(?<!src=")(?<!src=\')https?://(?:www\.)?(?:youtube\.com/(?:watch\?[^\s<\[]*v=|embed/|shorts/)|youtu\.be/)[^\s<\[]+~i',
            function ($matches) {
                $video_id = theregs_youtube_id_from_url($matches[0]);
                $embed = theregs_youtube_embed_html($video_id);

                return $embed !== '' ? $embed : theregs_escape($matches[0]);
            },
            $text
        );

        $text = preg_replace_callback(
            '~\[img\]\s*(https?://[^\[]+?)\s*\[/img\]~is',
            function ($matches) {
                return '<img src="' . theregs_escape(trim($matches[1])) . '" alt="" class="img-fluid rounded">';
            },
            $text
        );

        $text = preg_replace_callback(
            '~\[url=(https?://[^\]]+)\](.*?)\[/url\]~is',
            function ($matches) {
                return '<a href="' . theregs_escape(trim($matches[1])) . '" target="_blank" rel="noopener noreferrer">' . $matches[2] . '</a>';
            },
            $text
        );

        $text = preg_replace_callback(
            '~\[url\](https?://[^\[]+)\[/url\]~is',
            function ($matches) {
                $url = trim($matches[1]);
                return '<a href="' . theregs_escape($url) . '" target="_blank" rel="noopener noreferrer">' . theregs_escape($url) . '</a>';
            },
            $text
        );

        /*
         * Code blocks should be escaped and handled before other lightweight tags,
         * otherwise BBCode inside code could be converted.
         */
        $text = preg_replace_callback(
            '~\[code\](.*?)\[/code\]~is',
            function ($matches) {
                return '<pre class="theregs-code"><code>' . theregs_escape(trim($matches[1])) . '</code></pre>';
            },
            $text
        );

        /*
         * Spoilers become Bootstrap collapse blocks.
         */
        $text = preg_replace_callback(
            '~\[spoiler(?:=([^\]]+))?\](.*?)\[/spoiler\]~is',
            function ($matches) {
                static $spoiler_id = 0;
                $spoiler_id++;
                $title = trim((string)($matches[1] ?? ''));
                $title = $title !== '' ? $title : 'Spoiler';
                $id = 'theregs-spoiler-' . $spoiler_id . '-' . substr(md5($matches[2]), 0, 8);

                return '<div class="theregs-spoiler mb-3">'
                    . '<button class="btn btn-sm btn-outline-warning" type="button" data-bs-toggle="collapse" data-bs-target="#' . theregs_escape($id) . '" aria-expanded="false" aria-controls="' . theregs_escape($id) . '">'
                    . theregs_escape($title)
                    . '</button>'
                    . '<div class="collapse mt-2" id="' . theregs_escape($id) . '">'
                    . '<div class="theregs-spoiler-body">'
                    . $matches[2]
                    . '</div></div></div>';
            },
            $text
        );

        $text = preg_replace('~\[b\](.*?)\[/b\]~is', '<strong>$1</strong>', $text);
        $text = preg_replace('~\[i\](.*?)\[/i\]~is', '<em>$1</em>', $text);
        $text = preg_replace('~\[u\](.*?)\[/u\]~is', '<u>$1</u>', $text);
        $text = preg_replace('~\[s\](.*?)\[/s\]~is', '<s>$1</s>', $text);
        $text = preg_replace('~\[quote\](.*?)\[/quote\]~is', '<blockquote class="theregs-quote">$1</blockquote>', $text);
        $text = preg_replace('~\[quote=(.*?)\](.*?)\[/quote\]~is', '<blockquote class="theregs-quote"><div class="theregs-quote-author">$1 wrote:</div>$2</blockquote>', $text);
        $text = preg_replace('~\[color=([#A-Za-z0-9]+)\](.*?)\[/color\]~is', '<span style="color:$1">$2</span>', $text);
        $text = preg_replace_callback(
            '~\[size=([+\-]?[0-9]+)\](.*?)\[/size\]~is',
            function ($matches) {
                $raw_size = (string) $matches[1];
                $size = (int) $raw_size;

                /*
                 * phpBB-style relative sizes:
                 * [size=+2]Text[/size], [size=-1]Text[/size]
                 */
                if (strpos($raw_size, '+') === 0 || strpos($raw_size, '-') === 0) {
                    $base = 14;
                    $size = $base + ($size * 3);
                }

                /*
                 * Keep old imported content from accidentally creating giant/tiny text.
                 */
                $size = max(8, min(36, $size));

                return '<span style="font-size:' . $size . 'px">' . $matches[2] . '</span>';
            },
            $text
        );
        $text = preg_replace('~\[hr\]\s*\[/hr\]|\[hr\]~i', '<hr>', $text);

        $text = theregs_render_legacy_tables($text);
        $text = theregs_render_legacy_lists_safe($text);

        return $text;
    }
}


if (!function_exists('theregs_article_url')) {
    function theregs_article_url(string $section_key, string $category, string $slug = ''): string
    {
        $section_key = strtolower(trim(preg_replace('/[^a-z0-9_-]+/i', '-', $section_key), '-'));
        $category = strtolower(trim(preg_replace('/[^a-z0-9_-]+/i', '-', $category), '-'));
        $slug = strtolower(trim(preg_replace('/[^a-z0-9_-]+/i', '-', $slug), '-'));

        $section_key = $section_key !== '' ? $section_key : 'site';
        $category = $category !== '' ? $category : 'news';

/*
 * Reject unreasonable slugs/categories early.
 * Normal friendly URLs should be tiny; oversized values are almost always
 * scanner/bot traffic and should not render a soft-404 page.
 */
if (strlen($slug) > 180 || strlen($category) > 80 || strlen($section_key) > 32) {
    http_response_code(404);
    ?>
    <main class="col-12 text-light">
        <div class="card bg-dark border-secondary text-light my-4">
            <div class="card-body text-center">
                <h1 class="h4">Page Not Found</h1>
                <p class="mb-0 text-secondary">The requested article could not be found.</p>
            </div>
        </div>
    </main>
    <?php
    exit;
}

        /*
         * Root site:
         *   /articles
         *   /articles/article-slug
         *   /articles/category/history
         *   /articles/category/history/article-slug
         *
         * Game/site sections:
         *   /wow/articles
         *   /wow/articles/article-slug
         *   /wow/articles/category/introduction
         *   /wow/articles/category/introduction/article-slug
         */
        if ($section_key === 'site') {
            if ($category === 'news') {
                return $slug !== ''
                    ? '/articles/' . rawurlencode($slug)
                    : '/articles';
            }

            return $slug !== ''
                ? '/articles/category/' . rawurlencode($category) . '/' . rawurlencode($slug)
                : '/articles/category/' . rawurlencode($category);
        }

        if ($category === 'news') {
            return $slug !== ''
                ? '/' . rawurlencode($section_key) . '/articles/' . rawurlencode($slug)
                : '/' . rawurlencode($section_key) . '/articles';
        }

        return $slug !== ''
            ? '/' . rawurlencode($section_key) . '/articles/category/' . rawurlencode($category) . '/' . rawurlencode($slug)
            : '/' . rawurlencode($section_key) . '/articles/category/' . rawurlencode($category);
    }
}

if (!function_exists('theregs_render_article_body')) {
    function theregs_render_article_body($article)
    {
        $body = $article['body'] ?? '';
        $format = $article['body_format'] ?? 'html';

        if ($format === 'bbcode' && function_exists('generate_text_for_display')) {
            $body = generate_text_for_display(
                $body,
                $article['bbcode_uid'] ?? '',
                $article['bbcode_bitfield'] ?? '',
                (int)($article['bbcode_options'] ?? 7)
            );
        }

        return theregs_render_article_content(theregs_render_legacy_article_markup($body));
    }
}

$embedded = !empty($theregs_articles_embedded);
$show_sidebars = isset($theregs_articles_show_sidebars)
    ? (bool) $theregs_articles_show_sidebars
    : !$embedded;

$show_title = isset($theregs_articles_show_title)
    ? (bool) $theregs_articles_show_title
    : true;

/*
 * Determine which site section this article module belongs to.
 *
 * Priority:
 * 1. Explicit include override from home.php:
 *      $theregs_articles_section_key = 'ac';
 *
 * 2. URL router:
 *      index.php?site=wow&page=articles
 *
 * 3. Folder name:
 *      includes/sites/ac/articles.php -> ac
 *      includes/sites/site/articles.php -> site
 *
 * 4. Final fallback:
 *      site
 */
$folder_section_key = basename(__DIR__);
$folder_section_key = strtolower(preg_replace('/[^a-z0-9_-]+/i', '-', $folder_section_key));
$folder_section_key = trim($folder_section_key, '-');

if (
    $folder_section_key === ''
    || $folder_section_key === 'sites'
    || $folder_section_key === 'pages'
    || $folder_section_key === 'includes'
    || $folder_section_key === 'public_html'
    || $folder_section_key === 'root'
) {
    $folder_section_key = 'site';
}

if (isset($theregs_articles_section_key)) {
    $section_key = (string) $theregs_articles_section_key;
} else {
    $url_section_key = trim((string) $request->variable('site', ''));
    $section_key = $url_section_key !== '' ? $url_section_key : $folder_section_key;
}

$section_key = strtolower(preg_replace('/[^a-z0-9_-]+/i', '-', $section_key));
$section_key = trim($section_key, '-');
$section_key = $section_key !== '' ? $section_key : 'site';

if (isset($site_sections[$section_key]['name'])) {
    $section_name = (string) $site_sections[$section_key]['name'];
} else {
    $section_name = ucwords(str_replace(['_', '-'], ' ', $section_key));
}

$category_default = isset($theregs_articles_category)
    ? (string) $theregs_articles_category
    : 'news';

$category = $request->variable('category', $category_default);

$slug = isset($theregs_articles_slug)
    ? (string) $theregs_articles_slug
    : $request->variable('slug', '');

$category = strtolower(trim(preg_replace('/[^a-z0-9_-]+/i', '-', $category), '-'));
$slug = strtolower(trim(preg_replace('/[^a-z0-9_-]+/i', '-', $slug), '-'));

$category = $category !== '' ? $category : 'news';

$query_section_key = isset($theregs_articles_query_section_key)
    ? strtolower(trim(preg_replace('/[^a-z0-9_-]+/i', '-', (string) $theregs_articles_query_section_key), '-'))
    : $section_key;

$query_section_key = $query_section_key !== '' ? $query_section_key : $section_key;

$limit = isset($theregs_articles_limit) ? max(0, (int) $theregs_articles_limit) : 0;

$category_title = ucwords(str_replace(['_', '-'], ' ', $category));
$page_title = ($category === 'news')
    ? $section_name . ' News'
    : $section_name . ' ' . $category_title;

$table_name = 'site_articles';
$columns = theregs_table_columns($db, $table_name);

$select_columns = [];

foreach ([
    'id',
    'section_key',
    'category',
    'title',
    'title_icon',
    'slug',
    'body',
    'image_url',
    'created_at',
    'updated_at',
    'created_by_name',
    'created_by_user_id',
    'updated_by_name',
    'updated_by_user_id',
    'summary',
    'author_name',
    'author',
    'username',
    'forum_topic_id',
    'forum_post_id',
    'forum_url',
    'external_url',
    'body_format',
    'bbcode_uid',
    'bbcode_bitfield',
    'bbcode_options',
    'sort_order',
    'is_active',
] as $column) {
    if (isset($columns[$column])) {
        $select_columns[] = $column;
    }
}

if (!$select_columns) {
    $select_columns = ['*'];
}

$order_parts = [];

if ($category === 'news' || $category === 'updates') {
    if (isset($columns['created_at'])) {
        $order_parts[] = 'created_at DESC';
    }

    $order_parts[] = 'id DESC';
} else {
    if (isset($columns['sort_order'])) {
        $order_parts[] = 'sort_order ASC';
    }

    if (isset($columns['created_at'])) {
        $order_parts[] = 'created_at ASC';
    }

    $order_parts[] = 'id ASC';
}

$sql = '
    SELECT ' . implode(', ', $select_columns) . '
    FROM ' . $table_name . '
    WHERE section_key = "' . $db->sql_escape($query_section_key) . '"
      AND category = "' . $db->sql_escape($category) . '"
';

if (isset($columns['is_active'])) {
    $sql .= '
      AND is_active = 1
    ';
}

if ($slug !== '' && isset($columns['slug'])) {
    $sql .= '
      AND slug = "' . $db->sql_escape($slug) . '"
    ';
}

$sql .= '
    ORDER BY ' . implode(', ', $order_parts);

if ($limit > 0 && $slug === '') {
    $result = $db->sql_query_limit($sql, $limit);
} else {
    $result = $db->sql_query($sql);
}

$articles = [];

while ($row = $db->sql_fetchrow($result)) {
    $articles[] = $row;
}

$db->sql_freeresult($result);

$is_news = ($category === 'news' || $category === 'updates');
$is_single = ($slug !== '' && count($articles) === 1);

/*
 * If a specific article slug was requested but no active article matched,
 * return a real 404 instead of a soft 200 page.
 */
if ($slug !== '' && empty($articles)) {
    http_response_code(404);
    ?>
    <main class="<?= $show_sidebars ? 'col-md-8 text-light' : 'col-12 text-light' ?>">
        <div class="card bg-dark border-secondary text-light my-4">
            <div class="card-header text-center">
                <h1 class="h4 mb-0">Page Not Found</h1>
            </div>
            <div class="card-body text-center">
                <p class="mb-0 text-secondary">The requested article could not be found.</p>
            </div>
        </div>
    </main>
    <?php
    exit;
}

/*
 * Shared articles.php physically lives in includes/pages/.
 * For site sections like tsw/wow/ac, sidebars should come from:
 *   includes/sites/<section_key>/
 * not from:
 *   includes/pages/
 *
 * Root layout files still live in:
 *   includes/layout/
 */
$root_includes_dir = dirname(__DIR__);
$root_layout_dir = $root_includes_dir . '/layout';
$site_dir = __DIR__;

if ($section_key !== 'site') {
    $candidate_site_dir = $root_includes_dir . '/sites/' . $section_key;

    if (is_dir($candidate_site_dir)) {
        $site_dir = $candidate_site_dir;
    }
}

if (is_file($root_layout_dir . '/sidebar-helpers.php')) {
    include_once $root_layout_dir . '/sidebar-helpers.php';
}

$main_col_class = $show_sidebars
    ? 'col-md-8 text-light'
    : 'col-12 text-light';
?>

<!-- generic articles.php version: Phase 1.0 Security Patch / 2026-06-25 -->

<?php if ($show_sidebars): ?>

    <!-- Sidebar Toggle Button: visible only on small screens -->
    <button
        class="btn btn-outline-light d-md-none mb-3"
        type="button"
        data-bs-toggle="offcanvas"
        data-bs-target="#leftSidebar"
        aria-controls="leftSidebar"
    >
        ☰ Menu
    </button>

    <!-- Sidebar Offcanvas -->
    <div
        class="offcanvas offcanvas-start text-bg-dark d-md-none"
        tabindex="-1"
        id="leftSidebar"
    >
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">
                <?= theregs_escape($section_name) ?>
            </h5>
            <button
                type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="offcanvas"
            ></button>
        </div>
        <div class="offcanvas-body">
            <?php
            theregs_include_first_existing([
                    // Section-local sidebars first.
                    // This is required for shared articles.php, otherwise
                    // /includes/layout/sidebar-left.php wins before
                    // /includes/sites/<site>/sidebar-left.php.
                    $site_dir . '/sidebar-left.php',
                    $site_dir . '/sidebar_left.php',
                    $site_dir . '/left.php',
                    $site_dir . '/left.inc.php',
                    $site_dir . '/blocks/left.php',

                    // Legacy/shared section fallbacks
                    dirname($site_dir) . '/' . $section_key . '_left.php',

                    // Root site layout sidebars last
                    $root_layout_dir . '/sidebar-left.php',
                    $root_layout_dir . '/sidebar_left.php',
                    $root_layout_dir . '/left.php',
                    $root_layout_dir . '/left.inc.php',

                    // Final legacy fallbacks
                    dirname($site_dir) . '/sidebar-left.php',
                    dirname($site_dir) . '/sidebar_left.php',
                    dirname($site_dir) . '/left.php',
                    dirname($site_dir) . '/left.inc.php',
                    dirname($site_dir) . '/blocks/left.php',
                ]);
            ?>
        </div>
    </div>

    <!-- Static Sidebar -->
    <aside class="col-md-2 d-none d-md-block sidebar-nav">
        <?php
        theregs_include_first_existing([
                    // Section-local sidebars first.
                    // This is required for shared articles.php, otherwise
                    // /includes/layout/sidebar-left.php wins before
                    // /includes/sites/<site>/sidebar-left.php.
                    $site_dir . '/sidebar-left.php',
                    $site_dir . '/sidebar_left.php',
                    $site_dir . '/left.php',
                    $site_dir . '/left.inc.php',
                    $site_dir . '/blocks/left.php',

                    // Legacy/shared section fallbacks
                    dirname($site_dir) . '/' . $section_key . '_left.php',

                    // Root site layout sidebars last
                    $root_layout_dir . '/sidebar-left.php',
                    $root_layout_dir . '/sidebar_left.php',
                    $root_layout_dir . '/left.php',
                    $root_layout_dir . '/left.inc.php',

                    // Final legacy fallbacks
                    dirname($site_dir) . '/sidebar-left.php',
                    dirname($site_dir) . '/sidebar_left.php',
                    dirname($site_dir) . '/left.php',
                    dirname($site_dir) . '/left.inc.php',
                    dirname($site_dir) . '/blocks/left.php',
                ]);
        ?>
    </aside>

<?php endif; ?>

<main
            class="<?= $show_sidebars ? 'col-md-8 text-light' : 'col-12 text-light' ?>"
            style="<?= $show_sidebars ? "background:url('//cdn.theregs.org/images/tsw/pixel.webp') repeat;" : '' ?>"
        >

            <div class="card bg-dark text-light border-secondary shadow-sm theregs-main-card">

                <?php if ($show_title): ?>
                    <div class="card-header text-center">
                        <h1 class="h4 mb-0"><?= theregs_escape($page_title) ?></h1>
                    </div>
                <?php endif; ?>

                <div class="card-body">

                    <?php if (empty($articles)): ?>

                        <div class="alert alert-warning mb-0">
                            No articles found for this category yet.
                        </div>

                    <?php elseif ($is_news): ?>

                        <?php foreach ($articles as $article): ?>

                            <?php
                            $article_title = $article['title'] ?? 'Untitled';
                            $article_slug = $article['slug'] ?? '';
                            $article_date = '';

                            if (!empty($article['created_at'])) {
                                $timestamp = strtotime($article['created_at']);
                                if ($timestamp) {
                                    $article_date = date('l F j, Y \a\t g:i a', $timestamp);
                                }
                            }

                            $article_author = 'The Regs';

                            /*
                             * Prefer the CMS/admin author fields first.
                             * Older/imported content can still fall back to legacy fields.
                             */
                            if (!empty($article['created_by_name'])) {
                                $article_author = $article['created_by_name'];
                            } elseif (!empty($article['author_name'])) {
                                $article_author = $article['author_name'];
                            } elseif (!empty($article['author'])) {
                                $article_author = $article['author'];
                            } elseif (!empty($article['username'])) {
                                $article_author = $article['username'];
                            }

                            $rendered_body = theregs_render_article_body($article);

                            $read_more_url = theregs_article_url($section_key, $category, $article_slug);

                            if (!empty($article['external_url'])) {
                                $read_more_url = $article['external_url'];
                            } elseif (!empty($article['forum_url'])) {
                                $read_more_url = $article['forum_url'];
                            } elseif (!empty($article['forum_topic_id'])) {
                                $read_more_url = '/forums/viewtopic.php?t=' . urlencode($article['forum_topic_id']);
                            }
                            ?>

                            <article class="card bg-dark text-light border-secondary mb-4 theregs-news-post">

                                <div class="card-header">
                                    <h2 class="h5 mb-2">
                                        <?php if (!empty($article['title_icon'])): ?>
                                            <img
                                                src="<?= theregs_escape($article['title_icon']) ?>"
                                                alt=""
                                                class="theregs-title-icon"
                                            >
                                        <?php endif; ?>

                                        <?= theregs_escape($article_title) ?>
                                    </h2>

                                    <div class="small fw-bold">
                                        Posted by
                                        <span class="theregs-news-author"><?= theregs_escape($article_author) ?></span>
                                        <?php if ($article_date !== ''): ?>
                                            on <?= theregs_escape($article_date) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="card-body">

                                    <?php if (!empty($article['image_url'])): ?>
                                        <div class="text-center mb-3">
                                            <img
                                                src="<?= theregs_escape($article['image_url']) ?>"
                                                alt="<?= theregs_escape($article_title) ?>"
                                                class="img-fluid rounded"
                                            >
                                        </div>
                                    <?php endif; ?>

                                    <div class="<?= $is_single ? 'theregs-news-body-full' : 'theregs-news-body-preview' ?>">
                                        <?= $rendered_body ?>
                                    </div>

                                    <?php if (!$is_single): ?>
                                        <a href="<?= theregs_escape($read_more_url) ?>" class="btn btn-sm btn-outline-light mt-2">
                                            Read More
                                        </a>
                                    <?php endif; ?>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <?php foreach ($articles as $article): ?>

                            <article class="theregs-db-article mb-4">

                                <h2 class="theregs-db-title">
                                    <?php if (!empty($article['title_icon'])): ?>
                                        <img
                                            src="<?= theregs_escape($article['title_icon']) ?>"
                                            alt=""
                                            class="theregs-title-icon"
                                        >
                                    <?php endif; ?>

                                    <?= theregs_escape($article['title'] ?? 'Untitled') ?>
                                </h2>

                                <div class="theregs-db-body">

                                    <?php if (!empty($article['image_url'])): ?>
                                        <div class="text-center mb-3">
                                            <img
                                                src="<?= theregs_escape($article['image_url']) ?>"
                                                alt="<?= theregs_escape($article['title'] ?? 'Article image') ?>"
                                                class="img-fluid rounded"
                                            >
                                        </div>
                                    <?php endif; ?>

                                    <?= theregs_render_article_body($article) ?>

                                </div>

                            </article>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </div>

        </main>

<?php if ($show_sidebars): ?>
    <?php
    theregs_include_first_existing([
        // Section-local sidebars first
        $site_dir . '/sidebar-right.php',
        $site_dir . '/sidebar_right.php',
        $site_dir . '/right.php',
        $site_dir . '/right.inc.php',
        $site_dir . '/blocks/right.php',

        // Legacy/shared section fallback
        dirname($site_dir) . '/' . $section_key . '_right.php',

        // Root site layout sidebars last
        $root_layout_dir . '/sidebar-right.php',
        $root_layout_dir . '/sidebar_right.php',
        $root_layout_dir . '/right.php',
        $root_layout_dir . '/right.inc.php',

        // Final legacy fallbacks
        dirname($site_dir) . '/sidebar-right.php',
        dirname($site_dir) . '/sidebar_right.php',
        dirname($site_dir) . '/right.php',
        dirname($site_dir) . '/right.inc.php',
        dirname($site_dir) . '/blocks/right.php',
    ]);
    ?>
<?php endif; ?>

<style>
.theregs-articles-page {
    max-width: 1920px;
}

.theregs-main-card {
    background: rgba(33, 37, 41, .94) !important;
}

.theregs-main-card > .card-header {
    background: rgba(33, 37, 41, .98);
    border-bottom: 1px solid rgba(255, 255, 255, .14);
}

.theregs-db-article:last-child,
.theregs-news-post:last-child {
    margin-bottom: 0 !important;
}

.theregs-db-title {
    margin: 0 0 .85rem;
    padding: .55rem .85rem;
    color: #ffd75a;
    font-size: 1.05rem;
    font-weight: 700;
    background: linear-gradient(90deg, rgba(122, 82, 9, .88), rgba(80, 58, 18, .55), rgba(33, 37, 41, 0));
    border-left: 4px solid #d4aa2f;
    border-bottom: 1px solid rgba(212, 170, 47, .35);
    text-shadow: 1px 1px 2px #000;
}

.theregs-db-body {
    padding: 1.15rem 1.25rem;
    color: #fff;
    background: rgba(0, 0, 0, .58);
    border: 1px solid rgba(255, 255, 255, .10);
    border-radius: .35rem;
    line-height: 1.72;
    white-space: normal;
}

.theregs-news-post > .card-header {
    background: rgba(33, 37, 41, .98);
    border-bottom: 1px solid rgba(255, 255, 255, .14);
}

.theregs-news-post h2 {
    color: #fff;
    font-weight: 700;
}

.theregs-news-post .card-body {
    background: rgba(0, 0, 0, .30);
    line-height: 1.65;
}

.theregs-news-body-full,
.theregs-news-body-preview {
    line-height: 1.7;
    white-space: normal;
}

.theregs-db-body a,
.theregs-news-author,
.theregs-news-post a:not(.btn) {
    color: #78aeea;
}

.theregs-video-embed {
    position: relative;
    width: 100%;
    max-width: 640px;
    margin: 1rem auto;
    aspect-ratio: 16 / 9;
    background: #000;
}

.theregs-video-embed iframe {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: 0;
}

.theregs-legacy-table {
    margin: 1rem 0;
    color: #fff;
    vertical-align: top;
}

.theregs-legacy-table td,
.theregs-legacy-table th {
    vertical-align: top;
}

.theregs-legacy-list {
    margin: .75rem 0 .75rem 1.25rem;
    padding-left: 1.25rem;
}

.theregs-quote {
    margin: 1rem 0;
    padding: .85rem 1rem;
    background: rgba(255, 255, 255, .06);
    border-left: 4px solid rgba(212, 170, 47, .75);
    color: #eee;
}

.theregs-quote-author {
    margin-bottom: .5rem;
    color: #ffd75a;
    font-weight: 700;
}

@media (max-width: 991.98px) {
    main {
        order: 1;
    }

    aside {
        order: 2;
    }
}

.theregs-legacy-list {
    margin: .6rem 0 .8rem 1.4rem;
    padding-left: 1.4rem;
}

.theregs-legacy-list .theregs-legacy-list {
    margin-top: .45rem;
    margin-bottom: .45rem;
}

.theregs-legacy-list li {
    margin-bottom: .35rem;
}

.theregs-legacy-list li > p {
    margin-bottom: .35rem;
}


.theregs-code {
    margin: 1rem 0;
    padding: .85rem 1rem;
    color: #f8f9fa;
    background: rgba(0, 0, 0, .75);
    border: 1px solid rgba(255, 255, 255, .18);
    border-radius: .35rem;
    white-space: pre-wrap;
    overflow-x: auto;
}

.theregs-code code {
    color: inherit;
}

.theregs-spoiler-body {
    padding: .85rem 1rem;
    color: #fff;
    background: rgba(0, 0, 0, .55);
    border: 1px solid rgba(255, 255, 255, .14);
    border-radius: .35rem;
}

.theregs-title-icon {
    max-width: 40px;
    max-height: 40px;
    margin-right: .5rem;
    vertical-align: middle;
}

</style>
