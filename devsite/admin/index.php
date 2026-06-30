<?php
declare(strict_types=1);

define('IN_ADMIN', true);

require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_sections.php';
require_once __DIR__ . '/includes/admin_pagination.php';

$page = $request->variable('page', 'dashboard');
$section_key = $request->variable('section', 'site');

$allowed_pages = [
    'dashboard' => [
        'file' => 'dashboard.inc.php',
        'title' => 'Admin Dashboard',
    ],
    'links' => [
        'file' => 'links.inc.php',
        'title' => 'Manage Links',
    ],
    'videos' => [
        'file' => 'videos.inc.php',
        'title' => 'Manage Videos',
    ],
    'gallery' => [
        'file' => 'gallery.inc.php',
        'title' => 'Manage Gallery',
    ],
    'kos' => [
        'file' => 'kos.inc.php',
        'title' => 'Manage KOS',
    ],
    'settings' => [
        'file' => 'settings.inc.php',
        'title' => 'Site Settings',
    ],
    'audit_log' => [
        'file' => 'audit_log.inc.php',
        'title' => 'Audit Log',
    ],
	'diplomacy' => [
		'file' => 'diplomacy.inc.php',
		'title' => 'Manage Diplomacy',
	],
	'world' => [
		'file' => 'world.inc.php',
		'title' => 'Manage WoW World',
	],
	'classes' => [
		'file' => 'classes.inc.php',
		'title' => 'Manage WoW Classes',
	],
	'factions' => [
		'file' => 'factions.inc.php',
		'title' => 'Manage WoW Factions',
	],
	'races' => [
		'file' => 'races.inc.php',
		'title' => 'Manage WoW Races',
	],
	'articles' => [
		'file' => 'articles.inc.php',
		'title' => 'Manage Site Articles',
	],
	'navigation' => [
		'file' => 'navigation.inc.php',
		'title' => 'Navigation Manager',
	],
	'content_cleanup' => [
		'file' => 'content_cleanup.inc.php',
		'title' => 'Content Cleanup',
	],
	'db_backup' => [
		'file' => 'db_backup.inc.php',
		'title' => 'Database Backup',
	],
	'db_health' => [
		'file' => 'db_health.inc.php',
		'title' => 'Database Health',
	],
	'image_checker' => [
		'file' => 'image_checker.inc.php',
		'title' => 'Image Checker',
	],
	'link_checker' => [
		'file' => 'link_checker.inc.php',
		'title' => 'Link Checker',
	],
	'development' => [
		'file' => 'development.inc.php',
		'title' => 'Development Center',
	],
	'development2' => [
		'file' => 'development_center.php',
		'title' => 'Development Center2',
	],
];

if (!isset($allowed_pages[$page])) {
    $page = 'dashboard';
}

$page_title = $allowed_pages[$page]['title'];
$page_file = __DIR__ . '/pages/' . $allowed_pages[$page]['file'];

if (!is_file($page_file)) {
    $page = 'dashboard';
    $page_title = 'Admin Dashboard';
    $page_file = __DIR__ . '/pages/dashboard.inc.php';
}

require_once __DIR__ . '/includes/admin_header.php';

require_once $page_file;

require_once __DIR__ . '/includes/admin_footer.php';