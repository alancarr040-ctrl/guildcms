<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('development-release-standard.php');
$page_title = 'Guild CMS Development & Release Standard';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

$body_html = <<<'HTML'
<div class="engineering-publication-body">
    <p class="lead">Guild CMS Development &amp; Release Standard defines the official workflow used to plan, implement, validate, package, publish, and preserve Guild CMS changes.</p>

    <div class="guild-card-soft p-3 mb-4">
        <h3 class="h5 mb-3">Revision History</h3>
        <div class="table-responsive">
            <table class="table table-dark table-sm align-middle mb-0">
                <thead>
                    <tr><th>Version</th><th>Date</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td>1.0</td><td>June 2026</td><td>Initial publication establishing the Guild CMS package, release, SQL, validation, and documentation standard.</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="guild-card-soft p-3 mb-4">
        <h3 class="h5 mb-3">Table of Contents</h3>
        <ol class="mb-0">
            <li><a href="#purpose" class="link-light">Purpose</a></li>
            <li><a href="#source" class="link-light">Authoritative Source Baseline</a></li>
            <li><a href="#layout" class="link-light">Repository and Package Layout</a></li>
            <li><a href="#workflow" class="link-light">Package Workflow</a></li>
            <li><a href="#docs" class="link-light">Release Documentation</a></li>
            <li><a href="#sql" class="link-light">SQL Migration Standard</a></li>
            <li><a href="#roadmap" class="link-light">Roadmap and Historical Records</a></li>
            <li><a href="#validation" class="link-light">Validation Gates</a></li>
            <li><a href="#git" class="link-light">Git Baseline Policy</a></li>
            <li><a href="#manifest" class="link-light">Package Manifest</a></li>
        </ol>
    </div>

    <h3 id="purpose" class="mt-5">Purpose</h3>
    <p>Guild CMS packages must be repeatable, reviewable, testable, and safe to apply. This standard reduces package ambiguity by defining what every package must contain, how changed files are organized, how database migrations are written, and how package history is recorded.</p>
    <p>The standard exists so future package requests can describe what needs to be built while GCMS-ENG-013 defines how the work is delivered.</p>

    <h3 id="source" class="mt-5">Authoritative Source Baseline</h3>
    <p>The authoritative source baseline for package work is the current tested repository export containing <code>admin/</code>, <code>guildcms/</code>, and <code>devsite/</code>. The Development Center and public site are reference sites. The <code>devsite/</code> tree is the installable product.</p>
    <p>Packages must modify actual project files rather than producing standalone examples. If a file already exists, it should be edited in place unless the package intentionally introduces a new artifact.</p>

    <h3 id="layout" class="mt-5">Repository and Package Layout</h3>
    <p>Release packages must preserve the project root layout and contain only changed files.</p>
    <pre class="guild-card-soft p-3"><code>admin/
guildcms/
devsite/
sql/
README.md
RELEASE_NOTES.md
IMPLEMENTATION_GUIDE.md
SECURITY_REVIEW.md
PACKAGE_MANIFEST.md</code></pre>
    <p>No package may introduce extra nesting such as <code>admin/admin/</code>, <code>guildcms/guildcms/</code>, or a package root folder above the project layout. The package should be ready to extract over a local working copy or test server tree.</p>

    <h3 id="workflow" class="mt-5">Package Workflow</h3>
    <ol>
        <li>Confirm the phase, package number, and deliverable.</li>
        <li>Build against the current authoritative baseline.</li>
        <li>Modify actual source files.</li>
        <li>Update Development Center and public documentation when applicable.</li>
        <li>Create schema-compatible SQL migrations when database records must change.</li>
        <li>Run syntax validation on modified PHP files.</li>
        <li>Package only changed files using the standard layout.</li>
        <li>Test on the server before committing to Git.</li>
    </ol>

    <h3 id="docs" class="mt-5">Release Documentation</h3>
    <p>Every package must include release documentation at the package root:</p>
    <ul>
        <li><strong>README.md</strong> explains package purpose and contents.</li>
        <li><strong>RELEASE_NOTES.md</strong> summarizes what changed.</li>
        <li><strong>IMPLEMENTATION_GUIDE.md</strong> explains how to apply and verify the package.</li>
        <li><strong>SECURITY_REVIEW.md</strong> records security impact and review notes.</li>
        <li><strong>PACKAGE_MANIFEST.md</strong> lists changed files, SQL, validation, and expected checks.</li>
    </ul>

    <h3 id="sql" class="mt-5">SQL Migration Standard</h3>
    <p>SQL migrations must target the current schema, use valid enum values, avoid guessing columns, and prefer idempotent insert/update patterns. Shared project tables should receive one shared SQL migration rather than separate admin and public migrations unless separate databases are intentionally involved.</p>
    <p>Roadmap item updates must use canonical deliverable identifiers such as <code>GCMS-ENG-013</code>. Package numbers belong in timeline, journal, changelog, and release history records rather than becoming duplicate roadmap deliverables.</p>

    <h3 id="roadmap" class="mt-5">Roadmap and Historical Records</h3>
    <p>Roadmap items represent deliverables. Timeline records when work happened. Journal entries record context and reasoning. Changelog entries record what changed. Release documentation records what shipped.</p>
    <p>A publication revision updates the existing publication roadmap item. It does not create a second roadmap item for the same deliverable.</p>

    <h3 id="validation" class="mt-5">Validation Gates</h3>
    <ul>
        <li>All modified PHP files must pass syntax checks.</li>
        <li>Installer packages must be tested on the development environment and clean certification environments when available.</li>
        <li>SQL migrations must be checked against the documented schema.</li>
        <li>Public site and Development Center references must remain synchronized.</li>
        <li>Packages must avoid raw errors, broken links, duplicate roadmap items, and accidental release-document publication.</li>
    </ul>

    <h3 id="git" class="mt-5">Git Baseline Policy</h3>
    <p>Git is the authoritative project history. Generated packages are applied to test systems and local working copies first. Only tested and accepted results should be committed and pushed. This keeps the repository aligned with verified code rather than untested generated output.</p>

    <h3 id="manifest" class="mt-5">Package Manifest</h3>
    <p>Each package must include a manifest that identifies package number, deliverable, changed files, SQL files, PHP syntax checks, database tables affected, expected validation results, and recommended Git commit message.</p>
</div>
HTML;

guildcms_engineering_publication_page($publication, $body_html);

require __DIR__ . '/../includes/footer.php';
