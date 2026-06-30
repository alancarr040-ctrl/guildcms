<?php
declare(strict_types=1);

$error_code = 408;
$error_title = 'Request Timeout';
$error_message = 'The request timed out.';
$error_detail = 'The server waited too long for the request.<br>Please refresh and try again.';

include __DIR__ . '/error_template.php';