<?php
declare(strict_types=1);

/*
 * Only allow methods the public site actually supports.
 * This prevents unsupported verbs such as PUT, PATCH, DELETE, TRACE, etc.
 * from rendering a normal 200 OK page through the router.
 */
$allowed_methods = ['GET', 'POST', 'HEAD'];

if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', $allowed_methods, true)) {
    http_response_code(405);
    header('Allow: GET, POST, HEAD');
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Method Not Allowed';
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

require_once __DIR__ . '/includes/config.inc.php';
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
            <img
                src="//cdn.theregs.org/images/404.webp"
                alt="Error 404 - File Not Found"
                class="img-fluid rounded mb-3"
                style="max-width:260px;"
            >

            <p><strong>Your file cannot be found!</strong></p>

            <p class="mb-0">
                It's all a conspiracy.<br>
                We're hiding everything from you...<br>
                Or maybe the file doesn't actually exist.<br>
                hmmm....
            </p>
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