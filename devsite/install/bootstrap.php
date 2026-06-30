<?php
declare(strict_types=1);

/*
 * Guild CMS Installer Bootstrap
 * Package: 4.4.0-2 Installer Framework
 */

define('GUILDCMS_INSTALLER', true);
define('GUILDCMS_INSTALLER_ROOT', __DIR__);
define('GUILDCMS_ROOT', dirname(__DIR__));

spl_autoload_register(static function (string $class): void {
    if (strncmp($class, 'GuildCMS\\Installer\\', 19) !== 0) {
        return;
    }

    $relative = substr($class, 19);
    $relative = str_replace('\\', '/', $relative);

    $paths = [
        GUILDCMS_INSTALLER_ROOT . '/classes/' . $relative . '.php',
        GUILDCMS_INSTALLER_ROOT . '/steps/' . $relative . '.php',
    ];

    if (str_starts_with($relative, 'Steps/')) {
        $paths[] = GUILDCMS_INSTALLER_ROOT . '/steps/' . substr($relative, 6) . '.php';
    }

    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});
