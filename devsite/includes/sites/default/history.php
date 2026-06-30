<?php
declare(strict_types=1);

/*
 * AC History page
 *
 * Uses the AC layout/sidebars, but pulls the article content from
 * the main site's History category.
 *
 * Layout/sidebar section:
 *   section_key = ac
 *
 * Article query:
 *   section_key = site
 *   category    = history
 *   slug        = history
 */

$site_name = "Asheron's Call";
$theregs_articles_section_key = 'ac';
$theregs_articles_query_section_key = 'site';
$theregs_articles_category = 'history';
$theregs_articles_slug = 'history';
$theregs_articles_limit = 1;

include dirname(__DIR__, 2) . '/pages/articles.php';
