<?php
declare(strict_types=1);

if (!function_exists('sidebar_h')) {
    function sidebar_h(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('sidebar_active_page')) {
    function sidebar_active_page(): string
    {
        global $request;

        if (isset($request)) {
            return (string) $request->variable('page', '');
        }

        $page = filter_input(INPUT_GET, 'page', FILTER_UNSAFE_RAW);

        return is_string($page) ? $page : '';
    }
}

if (!function_exists('sidebar_link')) {
    function sidebar_link(string $href, string $label, string $page_key): void
    {
        $active = sidebar_active_page() === $page_key;

        echo '<a class="sidebar-link' . ($active ? ' sidebar-link-active' : '') . '" href="' . sidebar_h($href) . '">';
        echo $active ? '→ ' : '';
        echo sidebar_h($label);
        echo '</a>';
    }
}

if (!function_exists('sidebar_heading')) {
    function sidebar_heading(string $label): void
    {
        echo '<div class="sidebar-heading">' . sidebar_h($label) . '</div>';
    }
}