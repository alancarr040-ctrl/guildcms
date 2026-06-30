<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('founders-note.php');
$page_title = "Founder's Note";
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

$body = '
    <p class="lead">Guild CMS began as a content management system.</p>
    <p>Over time it became something much larger.</p>
    <p>Good software is not defined solely by the features it provides, but by the engineering discipline behind it.</p>
    <p>The Engineering Library exists to document not only how Guild CMS is built, but why it is built that way.</p>
    <p>Every architectural decision, engineering standard, coding convention, and long-term direction will be documented here so contributors, administrators, and future developers can understand the project from first principles.</p>
    <p>Knowledge should never exist only in source code.</p>
    <p>It should be accessible, reviewable, and continuously improved.</p>
    <p class="mb-0">Welcome to the Guild CMS Engineering Library.</p>
';

guildcms_engineering_publication_page($publication, $body);
require __DIR__ . '/../includes/footer.php';
