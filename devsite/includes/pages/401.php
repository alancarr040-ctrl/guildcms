<?php
declare(strict_types=1);

$error_code = 401;
$error_title = 'Unauthorized';
$error_message = 'You need to be logged in to view this.';
$error_detail = 'This page requires authentication.<br>Please log in and try again.';

include __DIR__ . '/error_template.php';