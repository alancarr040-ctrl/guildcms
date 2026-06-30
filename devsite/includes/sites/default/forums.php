<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/layout/framework-helpers.php';

theregs_bootstrap('ac');

$section_key = 'ac';
$site_name = "Asheron's Call";
$forum_id = 421;
include dirname(__DIR__, 2) . '/pages/forums.php';
