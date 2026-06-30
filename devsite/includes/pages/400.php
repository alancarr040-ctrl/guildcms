<?php
declare(strict_types=1);

$error_code = 400;
$error_title = 'Bad Request';
$error_message = 'That request was malformed.';
$error_detail = 'The server could not understand the request.<br>Please check the address and try again.';

include __DIR__ . '/error_template.php';