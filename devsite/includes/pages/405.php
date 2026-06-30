<?php
declare(strict_types=1);

$error_code = 405;
$error_title = 'Method Not Allowed';
$error_message = 'That request method is not allowed.';
$error_detail = 'The page exists, but it does not accept that type of request.';

include __DIR__ . '/error_template.php';