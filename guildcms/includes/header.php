<?php
declare(strict_types=1);

$page_title = $page_title ?? GUILD_CMS_SITE_NAME;
$active_page = $active_page ?? 'home';
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= guildcms_h($page_title) ?> · <?= guildcms_h(GUILD_CMS_SITE_NAME) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= guildcms_h(GUILD_CMS_SITE_TAGLINE) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/guildcms.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark guild-nav sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">
            <span class="brand-mark">◆</span> The Guild CMS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#guildNavbar" aria-controls="guildNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="guildNavbar" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <?php
                $links = [
                    'home' => ['/', 'Home'],
                    'roadmap' => ['/roadmap.php', 'Roadmap'],
                    'installation' => ['/installation.php', 'Installation'],
                    'timeline' => ['/timeline.php', 'Timeline'],
                    'release_history' => ['/release-history.php', 'Release History'],
                    'changelog' => ['/changelog.php', 'Changelog'],
                    'vision' => ['/vision.php', 'Vision'],
                    'docs' => ['/docs.php', 'Docs'],
                    'engineering' => ['/engineering/', 'Engineering Library'],
                ];
                ?>
                <?php foreach ($links as $key => $link): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $active_page === $key ? 'active' : '' ?>" href="<?= guildcms_h($link[0]) ?>">
                            <?= guildcms_h($link[1]) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</nav>
<main>
