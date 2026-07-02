<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('development-release-standard.php');
$page_title = 'Development & Release Standard';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<p class="lead">GCMS-ENG-013 defines the official Guild CMS development and release standard. It captures the package workflow, source baseline rules, folder layout, SQL migration expectations, release documentation requirements, roadmap semantics, validation gates, package manifests, and Git baseline policy used for Guild CMS development.</p>

<div class="guild-card-soft p-3 mb-4">
    <h3 class="h5">Revision History</h3>
    <div class="table-responsive">
        <table class="table table-dark table-sm align-middle mb-0">
            <thead><tr><th>Version</th><th>Date</th><th>Description</th></tr></thead>
            <tbody><tr><td>1.0</td><td>July 2026</td><td>Published as part of Phase 4.4 package 4.4.0-9.</td></tr></tbody>
        </table>
    </div>
</div>

<h3 class="h4 mt-4">Authoritative Source Baseline</h3>
<p>Package work is built against the current tested repository export containing the established root layout:</p>
<pre class="guild-card-soft p-3"><code>admin/
guildcms/
devsite/</code></pre>
<p>The <code>admin/</code> tree is the Development Center. The <code>guildcms/</code> tree is the public Guild CMS project and documentation website. The <code>devsite/</code> tree is the installable Guild CMS product.</p>

<h3 class="h4 mt-4">Changed-Files Package Layout</h3>
<p>Release packages must contain only changed files and preserve repository-root layout. Packages must not include extra nesting such as <code>admin/admin/</code>, <code>guildcms/guildcms/</code>, <code>devsite/devsite/</code>, or an unnecessary package folder above the project layout.</p>

<h3 class="h4 mt-4">Development Workflow</h3>
<ol>
    <li>Confirm phase, package number, and deliverable.</li>
    <li>Build against the current authoritative baseline.</li>
    <li>Modify actual source files.</li>
    <li>Update Development Center and public documentation when applicable.</li>
    <li>Create schema-compatible SQL migrations when database records must change.</li>
    <li>Run PHP syntax validation on modified PHP files.</li>
    <li>Package only changed files using the standard layout.</li>
    <li>Test on the server before committing to Git.</li>
</ol>

<h3 class="h4 mt-4">Release Documentation</h3>
<p>Every package must include README, release notes, implementation guidance, security review notes, and a package manifest. Release documentation belongs at the package root unless it is intentionally being published to a site.</p>

<h3 class="h4 mt-4">Roadmap and History Semantics</h3>
<ul>
    <li><strong>Roadmap:</strong> what exists.</li>
    <li><strong>Timeline:</strong> when it happened.</li>
    <li><strong>Journal:</strong> why it happened.</li>
    <li><strong>Changelog:</strong> what changed.</li>
    <li><strong>Release documentation:</strong> what shipped.</li>
</ul>

<h3 class="h4 mt-4">Validation Gates</h3>
<ul>
    <li>Modified PHP files pass syntax checks.</li>
    <li>SQL is schema-compatible.</li>
    <li>Public site and Development Center references are synchronized.</li>
    <li>Installer packages are tested on development and clean certification environments when applicable.</li>
    <li>Packages avoid raw errors, broken links, duplicate roadmap entries, and accidental release-document publication.</li>
</ul>

<h3 class="h4 mt-4">Git Baseline Policy</h3>
<p>Git is the authoritative project history. Generated packages are applied to local working copies and test systems first. Only tested and accepted results should be committed and pushed.</p>
<?php
$body = ob_get_clean();
guildcms_engineering_publication_page($publication, $body);
require __DIR__ . '/../includes/footer.php';
