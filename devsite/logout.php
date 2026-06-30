<?php
define('IN_PHPBB', true);
$phpbb_root_path = './forums/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);

// Log the user out
$user->session_kill();
$user->session_begin();

header('Location: /index.php'); // Redirect to your website’s homepage
exit;
?>
