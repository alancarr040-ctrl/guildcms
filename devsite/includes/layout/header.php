<?php
declare(strict_types=1);

// header.php - site-wide header
define('APP_START', microtime(true));

require_once __DIR__ . '/../config.inc.php';

global $request;

$current_uri = '/';

if (isset($request)) {
    $current_uri = $request->server('REQUEST_URI', '/');
} else {
    $server_uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_UNSAFE_RAW);
    $current_uri = is_string($server_uri) && $server_uri !== '' ? $server_uri : '/';
}

function site_header_h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function site_header_active(string $section, string $current_uri): string
{
    $current_path = (string) parse_url($current_uri, PHP_URL_PATH);
    $current_path = '/' . trim($current_path, '/');

    if ($current_path === '') {
        $current_path = '/';
    }

    if ($section === 'home') {
        return in_array(
            $current_path,
            [
                '/',
                '/index.php',
            ],
            true
        )
            ? ' active'
            : '';
    }

    return (
        $current_path === '/' . $section ||
        str_starts_with($current_path, '/' . $section . '/')
    )
        ? ' active'
        : '';
}

$nav_items = [
    'home' => [
        'label' => 'Home',
        'href' => '/',
    ],
    'ac' => [
        'label' => "Asheron's Call",
        'href' => '/ac/',
    ],
    'ao' => [
        'label' => 'Anarchy Online',
        'href' => '/ao/',
    ],
    'tsw' => [
        'label' => 'The Secret World',
        'href' => '/tsw/',
    ],
    'wow' => [
        'label' => 'World of Warcraft',
        'href' => '/wow/',
    ],
    'cod' => [
        'label' => 'Call of Duty',
        'href' => '/cod/',
    ],
    'coh' => [
        'label' => 'City of Heroes',
        'href' => '/coh/',
    ],
    'eve' => [
        'label' => 'Eve Online',
        'href' => '/eve/',
    ],
    'fo76' => [
        'label' => 'Fallout 76',
        'href' => '/fo76/',
    ],
];
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="verify-v1" content="/rovfZuIdXPw/rK/vKM5GN9MQu8ITjr6dy3rL9iZm3c=">
    <meta name="norton-safeweb-site-verification" content="xpjdkqrkxdc7ti-aolbunnad0oa4utg3bng5og05g0z7bd66b2vrhxt44prbmoyn80cmtskn23ak7efs1gkud7vjgyx61ng9yizhneyxedjj0wjdwarpxb-draqkcfyw">
    <meta name="generator" content="The Regs Site Code">

    <meta property="og:site_name" content="The Regs">
    <meta property="og:title" content="The Regs :: A Multi-Gaming Guild">
    <meta property="og:description" content="Asheron's Call, Anarchy Online, World of Warcraft, The Secret World, gaming clan, and friends.">
    <meta property="og:image" content="https://cdn.theregs.org/images/site_logo.webp">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.theregs.org/">

    <meta name="description" content="Asheron's Call, Anarchy Online, World of Warcraft, The Secret World, gaming clan, and friends.">
    <meta name="keywords" content="Gaming, Online, MMORPG, Guild, Anarchy Online, Asheron's Call, World of Warcraft, The Secret World, Friends">
    <meta name="software" content="The Regs Site Code">
    <meta name="license" content="https://www.gnu.org/licenses/gpl.txt">

    <title>TheRegs.org</title>

    <base href="https://www.theregs.org/">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="//cdn.theregs.org/assets/css/main.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-4.0.0.min.js"></script>
    <script src="//cdn.theregs.org/assets/js/nhpup.js"></script>
</head>

<body>
<nav class="navbar navbar-expand-lg bg-dark border-bottom border-secondary mb-4">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="/">
            <img
                src="//cdn.theregs.org/assets/img/site_logo.webp"
                alt="TheRegs Logo"
                height="70"
                width="70"
                class="me-2"
            >
            <span class="text-light fw-bold">The Regs</span>
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

<div class="collapse navbar-collapse" id="mainNavbar">
    <ul class="nav nav-tabs flex-wrap">
        <?php foreach ($nav_items as $section => $item): ?>
            <li class="nav-item">
                <a
                    class="nav-link text-light<?= site_header_active($section, $current_uri) ?>"
                    href="<?= site_header_h($item['href']) ?>"
                >
                    <?= site_header_h($item['label']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
    </div>
</nav>

<div class="container-fluid page-wrapper">