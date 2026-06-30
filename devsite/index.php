<?php
declare(strict_types=1);

/*
 * Guild CMS front controller
 * Package 4.4.0-4 introduces setup detection so a new installable tree
 * explains missing configuration instead of failing with PHP/database errors.
 */

$allowed_methods = ['GET', 'POST', 'HEAD'];

if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', $allowed_methods, true)) {
    http_response_code(405);
    header('Allow: GET, POST, HEAD');
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Method Not Allowed';
    exit;
}

$config_file = __DIR__ . '/includes/config.inc.php';
$installer_index = __DIR__ . '/install/index.php';

/**
 * The installer will eventually generate includes/config.inc.php.
 * While Guild CMS is still uninstalled, the site must teach the administrator
 * what is missing and how to continue instead of producing a fatal error.
 */
function guildcms_config_appears_unconfigured(string $config_file): bool
{
    if (!is_file($config_file) || !is_readable($config_file)) {
        return true;
    }

    $contents = (string) file_get_contents($config_file);

    $placeholders = [
        'your_db',
        'your_db_user',
        'your_db_pass',
        'www.yoursite.com',
        '/home/theregs/public_html',
    ];

    foreach ($placeholders as $placeholder) {
        if (str_contains($contents, $placeholder)) {
            return true;
        }
    }

    return false;
}

if (guildcms_config_appears_unconfigured($config_file)) {
    require __DIR__ . '/includes/setup_required.php';
    exit;
}

if (!is_file(__DIR__ . '/forums/common.php')) {
    require __DIR__ . '/includes/setup_required.php';
    exit;
}

define('IN_PHPBB', true);

$phpbb_root_path = defined('PHPBB_ROOT_PATH') ? PHPBB_ROOT_PATH : './forums/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

require_once $phpbb_root_path . 'common.' . $phpEx;
require_once $phpbb_root_path . 'includes/functions_user.' . $phpEx;
require_once $phpbb_root_path . 'includes/functions_display.' . $phpEx;
require_once $phpbb_root_path . 'includes/bbcode.' . $phpEx;

$user->session_begin(false);
$auth->acl($user->data);
$user->setup();

require_once $config_file;
require_once __DIR__ . '/includes/db_functions.php';

global $request, $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;

$site = $request->variable('site', '');
$page = $request->variable('page', 'home');

$site = $site !== '' ? basename($site) : null;
$page = basename($page);

$db_connection = mysql_init_connect($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS);

$skip_wrapper = ($site === 'ao' && $page === 'member');

if (!$skip_wrapper) {
    require_once __DIR__ . '/includes/layout/header.php';
    echo '<div class="container-fluid"><div class="row">';
}

if ($site) {
    $site_path = __DIR__ . "/includes/sites/{$site}/{$page}.php";

    if (is_file($site_path)) {
        include $site_path;
    } else {
        http_response_code(404);
        include __DIR__ . '/includes/pages/404.php';
    }
} else {
    $valid_pages = array_map(
        static fn($file) => basename($file, '.php'),
        glob(__DIR__ . '/includes/pages/*.php') ?: []
    );

    if (in_array($page, $valid_pages, true)) {
        include __DIR__ . "/includes/pages/{$page}.php";
    } else {
        ?>
        <aside class="col-md-2 d-none d-md-block sidebar-nav">
            <?php include __DIR__ . '/includes/layout/sidebar-left.php'; ?>
        </aside>

<main class="col-md-8 text-light text-center">
    <div class="card bg-dark border-secondary text-light my-4">
        <div class="card-body">
            <p><strong>Your file cannot be found.</strong></p>
            <p class="mb-0">The requested Guild CMS page does not exist.</p>
        </div>
    </div>
</main>

        <aside class="col-md-2 d-none d-md-block sidebar-right">
            <?php include __DIR__ . '/includes/layout/sidebar-right.php'; ?>
        </aside>
        <?php
    }
}

if (!$skip_wrapper) {
    echo '</div></div>';
    require_once __DIR__ . '/includes/layout/footer.php';
}
