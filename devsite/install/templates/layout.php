<?php
declare(strict_types=1);

use GuildCMS\Installer\Installer;
use GuildCMS\Installer\InstallerStepInterface;
use GuildCMS\Installer\InstallerView;

/** @var Installer $installer */
/** @var InstallerStepInterface $step */
$currentKey = $step->key();
$previousKey = $installer->previousStepKey($currentKey);
$nextKey = $installer->nextStepKey($currentKey);
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Guild CMS Installer - <?= InstallerView::escape($step->title()) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/install.css">
</head>
<body>
<div class="install-shell">
    <header class="install-header">
        <div class="install-kicker">Guild CMS Installation</div>
        <h1><?= InstallerView::escape($step->title()) ?></h1>
        <p><?= InstallerView::escape($step->summary()) ?></p>
    </header>

    <div class="install-layout">
        <nav class="install-steps" aria-label="Installer steps">
            <?php foreach ($installer->steps() as $registeredStep): ?>
                <a class="install-step <?= $registeredStep->key() === $currentKey ? 'is-active' : '' ?>" href="?step=<?= InstallerView::escape($registeredStep->key()) ?>">
                    <strong><?= InstallerView::escape($registeredStep->title()) ?></strong>
                    <span><?= InstallerView::escape($registeredStep->status()) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <main class="install-content">
            <?= $step->render($installer) ?>

            <div class="install-actions">
                <?php if ($previousKey !== null): ?>
                    <a class="button button-secondary" href="?step=<?= InstallerView::escape($previousKey) ?>">Previous</a>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>

                <?php if ($nextKey !== null): ?>
                    <a class="button" href="?step=<?= InstallerView::escape($nextKey) ?>">Next</a>
                <?php else: ?>
                    <a class="button" href="../">Return to site</a>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
</body>
</html>
