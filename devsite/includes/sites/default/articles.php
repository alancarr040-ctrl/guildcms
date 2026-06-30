<?php
declare(strict_types=1);

$theregs_articles_section_key = 'ac';
$theregs_articles_category = 'news';
$theregs_articles_limit = 10;

if (($request ?? null) instanceof \phpbb\request\request_interface) {
    $requested_category = $request->variable('category', $theregs_articles_category);
    $theregs_articles_category = preg_replace('/[^a-z0-9_-]/i', '', $requested_category);
}

include dirname(__DIR__, 2) . '/pages/articles.php';