<?php
declare(strict_types=1);

/*
 * The Guild CMS public project site configuration.
 *
 * Default install target:
 *   /home/theregs/domains/guildcms.theregs.org/public_html/
 *
 * This public site reads the same project roadmap tables used by:
 *   /admin/?page=development
 */

const GUILD_CMS_SITE_NAME = 'The Guild CMS';
const GUILD_CMS_SITE_TAGLINE = 'A modular community management platform built for gaming communities.';
const GUILD_CMS_FLAGSHIP_SITE = 'https://www.theregs.org/';
const GUILD_CMS_ADMIN_SITE = 'https://www.theregs.org/admin/?page=development';

/*
 * Main TheRegs config containing:
 *   $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS
 *
 * If your path differs, update this value.
 */
const GUILD_CMS_MAIN_CONFIG = '/home/theregs/public_html/includes/config.inc.php';
