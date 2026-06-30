<?php
define('IN_PHPBB', true);
$phpbb_root_path = './forums/';  // Replace with your phpBB root directory path
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

// Check if the request method is POST using phpBB's request class
if ($request->is_set_post('username') && $request->is_set_post('password')) {
    // Retrieve and sanitize input using request_var
    $username = $request->variable('username', '', true);
    $password = $request->variable('password', '', true);
    $autologin = false;  // Set to true if you want "Remember Me" functionality
    $admin = false;

    // Attempt login using phpBB's login function
    $result = $auth->login($username, $password, $autologin, true, $admin);

    if ($result['status'] == LOGIN_SUCCESS) {
        // Get the referring page URL
        $referrer = $request->header('referer');
        if (empty($referrer)) {
            $referrer = '/default-page.php';  // Fallback page if no referrer is found
        }
        
        // Redirect to the referring page or fallback
        header("Location: $referrer");
        exit;
    } else {
        echo 'Login failed: ' . $user->lang($result['error_msg']);
    }
}
?>