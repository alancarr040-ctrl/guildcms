<?php
declare(strict_types=1);

$error_code = 500;
$error_title = 'Internal Server Error';
$error_message = 'Something broke behind the scenes.';
$error_detail = 'The server encountered an unexpected error.<br>Please try again later or contact an administrator.';

include __DIR__ . '/error_template.php';