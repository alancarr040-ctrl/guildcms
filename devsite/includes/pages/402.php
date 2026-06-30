<?php
declare(strict_types=1);

$error_code = 402;
$error_title = 'Payment Required';
$error_message = 'Payment required.';
$error_detail = 'This error is rarely used, but the server returned it.<br>Please contact an administrator if you believe this is wrong.';

include __DIR__ . '/error_template.php';