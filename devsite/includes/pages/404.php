<?php
declare(strict_types=1);

$error_code = 404;
$error_title = 'File Not Found';
$error_message = 'Your file cannot be found!';
$error_detail = "It's all a conspiracy.<br>We're hiding everything from you...<br>Or maybe the file doesn't actually exist.<br>hmmm....";

include __DIR__ . '/error_template.php';