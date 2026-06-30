<?php
declare(strict_types=1);

if (!defined('IN_ADMIN')) {
    define('IN_ADMIN', true);
}

$phpbb_root_path = '/home/theregs/public_html/forums/';
$phpEx = 'php';

define('IN_PHPBB', true);

require_once $phpbb_root_path . 'common.php';

$user->session_begin();
$auth->acl($user->data);
$user->setup();

if ((int) $user->data['user_id'] === ANONYMOUS) {
    $redirect = urlencode('/admin/');
    header(
        'Location: /forums/ucp.php?mode=login&redirect=' . $redirect
    );
    exit;
}

if (!function_exists('admin_form_token')) {
    function admin_form_token(string $form_key): string
    {
        global $user;
        $creation_time = time();
        return build_hidden_fields([
            'creation_time' => $creation_time,
            'form_token' => sha1($creation_time . $user->data['user_form_salt'] . $form_key),
        ]);
    }
}

/* Temporary admin access check. */
$allowed_admin_user_ids = [
    2,
];

if (!in_array((int) $user->data['user_id'], $allowed_admin_user_ids, true)) {
    trigger_error('You do not have permission to access this admin area.');
}

$admin_user = [
    'id' => (int) $user->data['user_id'],
    'username' => (string) $user->data['username'],
];

function admin_h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
