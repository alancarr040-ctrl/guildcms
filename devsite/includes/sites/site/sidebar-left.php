<?php
declare(strict_types=1);

$sidebar_section_key = 'site';

$sidebar_menu = [
    [
        'title' => null,
        'items' => [
            ['Home', '/'],
            ['Links', '/links.php'],
            ['Forums', '/forums.php'],
        ],
    ],
    [
        'title' => 'Guild Info',
        'items' => [
            ['Code of Conduct', '/coc.php'],
            ['Charter', '/charter.php'],
            ['Recruitment', '/join.php'],
            ['History', '/history.php'],
        ],
    ],
    [
        'title' => 'Site Info',
        'items' => [
            ['About', '/about.php'],
            ['Privacy', '/privacy.php'],
            ['Terms of Use', '/terms.php'],
            ['Trademarks', '/trade.php'],
            ['Contact', '/contact.php'],
            ['Site Map', '/sitemap.php'],
        ],
    ],
];

require dirname(__DIR__, 2) . '/layout/sidebar-renderer.php';
