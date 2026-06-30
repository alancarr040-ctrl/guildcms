<?php
declare(strict_types=1);

/*
 * TheRegs Framework Helpers
 * Version: 1.1.4 Phase 3A.5 Layout Helpers
 *
 * Main entry point:
 *   theregs_bootstrap('wow');
 *
 * Sidebar/layout helpers:
 *   render_sidebar();
 *   render_right_sidebar();
 *   theregs_layout_left();
 *   theregs_layout_right();
 */

if (!function_exists('theregs_public_root')) {
    function theregs_public_root(): string
    {
        return dirname(__DIR__, 2);
    }
}

if (!function_exists('theregs_path')) {
    function theregs_path(string $relative_path): string
    {
        return theregs_public_root() . '/' . ltrim($relative_path, '/');
    }
}

if (!function_exists('theregs_normalize_section_key')) {
    function theregs_normalize_section_key(string $section_key): string
    {
        $section_key = strtolower(trim(preg_replace('/[^a-z0-9_-]+/i', '-', $section_key), '-'));

        return $section_key !== '' ? $section_key : 'site';
    }
}

if (!function_exists('theregs_set_section')) {
    function theregs_set_section(string $section_key): void
    {
        $GLOBALS['theregs_section_key'] = theregs_normalize_section_key($section_key);
    }
}

if (!function_exists('theregs_get_section')) {
    function theregs_get_section(?string $section_key = null): string
    {
        if ($section_key !== null && trim($section_key) !== '') {
            return theregs_normalize_section_key($section_key);
        }

        if (isset($GLOBALS['theregs_section_key']) && is_string($GLOBALS['theregs_section_key'])) {
            return theregs_normalize_section_key($GLOBALS['theregs_section_key']);
        }

        if (isset($GLOBALS['theregs_articles_section_key']) && is_string($GLOBALS['theregs_articles_section_key'])) {
            return theregs_normalize_section_key($GLOBALS['theregs_articles_section_key']);
        }

        if (isset($GLOBALS['theregs_page_section_key']) && is_string($GLOBALS['theregs_page_section_key'])) {
            return theregs_normalize_section_key($GLOBALS['theregs_page_section_key']);
        }

        return 'site';
    }
}

if (!function_exists('theregs_bootstrap')) {
    function theregs_bootstrap(string $section_key = 'site', array $options = []): void
    {
        $section_key = theregs_normalize_section_key($section_key);

        theregs_set_section($section_key);

        $GLOBALS['theregs_bootstrap'] = array_merge(
            [
                'section_key' => $section_key,
                'theme' => $section_key,
                'bootstrapped_at' => time(),
            ],
            $options
        );
    }
}

if (!function_exists('theregs_get_bootstrap')) {
    function theregs_get_bootstrap(?string $key = null, $default = null)
    {
        $data = $GLOBALS['theregs_bootstrap'] ?? [];

        if ($key === null) {
            return $data;
        }

        return $data[$key] ?? $default;
    }
}

if (!function_exists('theregs_include_first_existing')) {
    function theregs_include_first_existing(array $paths): bool
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

if (!function_exists('render_sidebar')) {
    function render_sidebar(?string $section_key = null): bool
    {
        $section_key = theregs_get_section($section_key);

        $paths = [
            theregs_path('includes/sites/' . $section_key . '/sidebar-left.php'),
            theregs_path('includes/sites/' . $section_key . '/sidebar_left.php'),
            theregs_path('includes/sites/' . $section_key . '/left.php'),
            theregs_path('includes/sites/' . $section_key . '/left.inc.php'),
        ];

        if ($section_key === 'site') {
            $paths[] = theregs_path('includes/layout/sidebar-left.php');
            $paths[] = theregs_path('includes/layout/sidebar_left.php');
            $paths[] = theregs_path('includes/layout/left.php');
            $paths[] = theregs_path('includes/layout/left.inc.php');
        }

        return theregs_include_first_existing($paths);
    }
}

if (!function_exists('render_right_sidebar')) {
    function render_right_sidebar(?string $section_key = null): bool
    {
        $section_key = theregs_get_section($section_key);

        $paths = [
            theregs_path('includes/sites/' . $section_key . '/sidebar-right.php'),
            theregs_path('includes/sites/' . $section_key . '/sidebar_right.php'),
            theregs_path('includes/sites/' . $section_key . '/right.php'),
            theregs_path('includes/sites/' . $section_key . '/right.inc.php'),
        ];

        if ($section_key === 'site') {
            $paths[] = theregs_path('includes/layout/sidebar-right.php');
            $paths[] = theregs_path('includes/layout/sidebar_right.php');
            $paths[] = theregs_path('includes/layout/right.php');
            $paths[] = theregs_path('includes/layout/right.inc.php');
        }

        return theregs_include_first_existing($paths);
    }
}

if (!function_exists('render_header')) {
    function render_header(?string $section_key = null): bool
    {
        $section_key = theregs_get_section($section_key);

        $paths = [
            theregs_path('includes/sites/' . $section_key . '/header.php'),
            theregs_path('includes/sites/' . $section_key . '/header.inc.php'),
        ];

        if ($section_key === 'site') {
            $paths[] = theregs_path('includes/layout/header.php');
            $paths[] = theregs_path('includes/layout/header.inc.php');
        }

        return theregs_include_first_existing($paths);
    }
}

if (!function_exists('render_footer')) {
    function render_footer(?string $section_key = null): bool
    {
        $section_key = theregs_get_section($section_key);

        $paths = [
            theregs_path('includes/sites/' . $section_key . '/footer.php'),
            theregs_path('includes/sites/' . $section_key . '/footer.inc.php'),
        ];

        if ($section_key === 'site') {
            $paths[] = theregs_path('includes/layout/footer.php');
            $paths[] = theregs_path('includes/layout/footer.inc.php');
        }

        return theregs_include_first_existing($paths);
    }
}

if (!function_exists('theregs_layout_h')) {
    function theregs_layout_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('theregs_layout_left')) {
    function theregs_layout_left(array $options = []): void
    {
        $label = (string)($options['label'] ?? 'Navigation');
        ?>
<button
    class="btn btn-outline-light d-md-none mb-3"
    type="button"
    data-bs-toggle="offcanvas"
    data-bs-target="#leftSidebar"
    aria-controls="leftSidebar"
>
    ☰ Menu
</button>

<div
    class="offcanvas offcanvas-start text-bg-dark d-md-none"
    tabindex="-1"
    id="leftSidebar"
    aria-labelledby="leftSidebarLabel"
>
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="leftSidebarLabel">
            <?= theregs_layout_h($label) ?>
        </h5>

        <button
            type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="offcanvas"
            aria-label="Close"
        ></button>
    </div>

    <div class="offcanvas-body">
        <?php render_sidebar(); ?>
    </div>
</div>

<aside class="col-md-2 d-none d-md-block sidebar-nav">
    <?php render_sidebar(); ?>
</aside>
<?php
    }
}

if (!function_exists('theregs_layout_right')) {
    function theregs_layout_right(): void
    {
        render_right_sidebar();
    }
}
