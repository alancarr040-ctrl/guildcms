<?php
declare(strict_types=1);

/*
 * AC History
 *
 * This page displays the main site History article from site_articles.
 *
 * Expected article:
 *   section_key = site
 *   category    = history
 *   slug        = history
 */

$theregs_articles_section_key = 'site';
$theregs_articles_category = 'history';
$theregs_articles_slug = 'history';
$theregs_articles_limit = 1;

include dirname(__DIR__) . '/pages/articles.php';
