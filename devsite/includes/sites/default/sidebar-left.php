<?php
declare(strict_types=1);

$sidebar_section_key = 'ac';

$sidebar_menu = [
    [
        'title' => null,
        'items' => [
            ['Home', 'ac/index.php'],
            ['Links', 'ac/links.php'],
            ['The Rhinos', 'ac/rhinos.php'],
            ['Forums', 'ac/forums.php'],
        ],
    ],
    [
        'title' => 'Media',
        'items' => [
            ['Videos', 'ac/videos.php'],
            ['Gallery', 'ac/gallery.php'],
        ],
    ],
    [
        'title' => 'Allegiance Info',
        'items' => [
            ['Diplomacy', 'ac/diplomacy.php'],
            ['KoS', 'ac/kos.php'],
            ['Code of Conduct', 'ac/coc.php'],
            [
                'title'            => 'Rules',
                'article_category' => 'rules',
                'section_key'      => 'ac',
                'base_url'         => 'ac/rules.php',
            ],
            ['Allegiance Charter', 'ac/charter.php'],
            ['Allegiance Tree', 'ac/tree.php'],
            ['Join', 'ac/recruit.php'],
            [
                'article' => [
                    'section_key' => 'site',
                    'category'    => 'history',
                    'slug'        => 'history',
                    'title'       => 'History',
                    'url'         => 'ac/history.php',
                ],
            ],
        ],
    ],
    [
        'title' => 'Game Information',
        'items' => [
            ['Map', 'ac/map.php'],
        ],
    ],
    [
        'title' => 'Important',
        'items' => [
            ['Rhino Declaration', 'ac/rhino.php'],
        ],
    ],
];

require dirname(__DIR__, 2) . '/layout/sidebar-renderer.php';
