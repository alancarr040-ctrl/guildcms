<?php
declare(strict_types=1);

/*
 * Compatibility shim.
 *
 * Main-site sidebar menu now lives at:
 *   includes/sites/site/sidebar-left.php
 *
 * This file remains so older templates that include:
 *   includes/layout/sidebar-left.php
 * continue to work.
 */

include dirname(__DIR__) . '/sites/site/sidebar-left.php';
