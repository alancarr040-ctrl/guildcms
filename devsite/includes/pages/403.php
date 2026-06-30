<?php
declare(strict_types=1);

$error_code = 403;
$error_title = 'Forbidden';
$error_message = 'You do not have permission to access this.';
$error_detail = 'The page exists, but access is denied.<br>If you believe this is wrong, contact an administrator.';

include __DIR__ . '/error_template.php';