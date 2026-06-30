<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    exit;
}

/*
 * Defines all editable site sections
 *
 * key = URL/database section_key
 */
$site_sections = [
    'site' => [
        'name' => 'Main Site',
        'path' => '/',
        'modules' => [
			'articles',
            'links',
			'navigation',
            'videos',
            'gallery',
        ],
    ],
    'ac' => [
        'name' => "Asheron's Call",
        'path' => '/ac/',
        'modules' => [
			'articles',
            'links',
			'navigation',
            'videos',
            'gallery',
            'diplomacy',
            'kos',
        ],
    ],
    'ao' => [
        'name' => 'Anarchy Online',
        'path' => '/ao/',
        'modules' => [
			'articles',
            'links',
			'navigation',
            'videos',
            'gallery',
        ],
    ],
    'tsw' => [
        'name' => 'The Secret World',
        'path' => '/tsw/',
        'modules' => [
			'articles',
            'links',
			'navigation',
            'videos',
            'gallery',
        ],
    ],
    'wow' => [
        'name' => 'World of Warcraft',
        'path' => '/wow/',
        'modules' => [
			'articles',
            'links',
			'navigation',
            'videos',
            'gallery',
            'factions',
			'classes',
            'races',
            'world',
        ],
    ],
    'cod' => [
        'name' => 'Call of Duty',
        'path' => '/cod/',
        'modules' => [
			'articles',
            'links',
			'navigation',
            'videos',
            'gallery',
        ],
    ],
    'coh' => [
        'name' => 'City of Heroes',
        'path' => '/coh/',
        'modules' => [
			'articles',
            'links',
			'navigation',
            'videos',
            'gallery',
        ],
    ],
    'eve' => [
        'name' => 'Eve Online',
        'path' => '/eve/',
        'modules' => [
			'articles',
            'links',
			'navigation',
            'videos',
            'gallery',
        ],
    ],
    'fo76' => [
        'name' => 'Fallout 76',
        'path' => '/fo76/',
        'modules' => [
			'articles',
            'links',
			'navigation',
            'videos',
            'gallery',
        ],
    ],
    /* Administrative maintenance tools */
	'maintenance' => [
		'name' => 'Maintenance',
		'path' => '/admin/',
		'modules' => [
			'content_cleanup',
			'db_backup',
			'db_health',
			'image_checker',
			'link_checker',
		],
	],
];

/* Friendly names for buttons/titles */
$admin_module_labels = [
	'audit_log'	=>	'Audit Log',
    'links'     => 'Links',
    'videos'    => 'Videos',
    'gallery'   => 'Gallery',
    'diplomacy' => 'Diplomacy',
    'kos'       => 'KOS',
    'factions'  => 'Factions',
    'races'     => 'Races',
    'world'     => 'The World',
	'content_cleanup'	=> 'Content Cleanup',
	'db_backup' => 'Database Backup',
	'db_health' => 'Database Health',
	'image_checker' => 'Image Checker',
	'link_checker' => 'Link Checker',
];