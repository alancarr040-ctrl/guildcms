<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('developer-handbook.php');
$page_title = 'Developer Handbook';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<p class="lead">The Developer Handbook is the practical onboarding and day-to-day engineering guide for Guild CMS. It explains how developers should understand the project, move through the established workflow, prepare packages, document changes, and preserve the engineering discipline defined by the Constitution, Vision &amp; Mission, Engineering Principles, and Architecture Standards.</p>

<div class="guild-card-soft p-3 mb-4">
    <h3 class="h5">Revision History</h3>
    <div class="table-responsive">
        <table class="table table-dark table-sm align-middle mb-0">
            <thead><tr><th>Version</th><th>Date</th><th>Description</th></tr></thead>
            <tbody><tr><td>1.0</td><td>June 2026</td><td>Initial publication as GCMS-ENG-005.</td></tr></tbody>
        </table>
    </div>
</div>

<h3 class="h4 mt-4">Table of Contents</h3>
<ol>
    <li><a href="#purpose">Purpose of the Handbook</a></li>
    <li><a href="#developer-orientation">Developer Orientation</a></li>
    <li><a href="#source-structure">Source Structure</a></li>
    <li><a href="#workflow">Engineering Workflow</a></li>
    <li><a href="#development-center">Development Center Responsibilities</a></li>
    <li><a href="#public-site">Public Site Responsibilities</a></li>
    <li><a href="#database-work">Database and SQL Work</a></li>
    <li><a href="#security-review">Security Review Expectations</a></li>
    <li><a href="#release-packages">Release Package Preparation</a></li>
    <li><a href="#maintenance">Maintenance and Stewardship</a></li>
</ol>

<h3 id="purpose" class="h4 mt-5">1. Purpose of the Handbook</h3>
<p><strong>GCMS-ENG-005</strong> exists to help developers become productive without relying on undocumented project memory. It is not a replacement for the Constitution or Architecture Standards. Instead, it translates those governing documents into practical working habits.</p>
<p>Every developer should be able to answer three questions before changing Guild CMS: what part of the system is being changed, what engineering rule governs the change, and how the change will be packaged, reviewed, documented, and installed.</p>

<h3 id="developer-orientation" class="h4 mt-5">2. Developer Orientation</h3>
<p>Guild CMS is developed as a public-facing product, an administration platform, and an engineering project. Developers must keep those responsibilities separate. The public site publishes project knowledge and user-facing information. The Development Center tracks engineering state and workflow. Release packages carry tested incremental changes.</p>
<ul>
    <li><strong>Public site:</strong> authoritative home for published Engineering Library documents.</li>
    <li><strong>Development Center:</strong> internal engineering management and project tracking interface.</li>
    <li><strong>Release package:</strong> the unit of delivery for changed files, SQL, release notes, and security review.</li>
</ul>

<h3 id="source-structure" class="h4 mt-5">3. Source Structure</h3>
<p>Developers should preserve the existing layout and avoid creating parallel systems. Public files belong in the public Guild CMS site. Admin and Development Center files belong in the admin tree. SQL migrations belong in a package-level <code>sql/</code> directory. Release documentation belongs at the package root.</p>
<div class="guild-card-soft p-3 mb-4">
<pre class="mb-0"><code>admin/
  data/
  includes/
  pages/

guildcms/
  engineering/
  docs/
  includes/

sql/
docs/
README.md
RELEASE_NOTES.md
IMPLEMENTATION_GUIDE.md
SECURITY_REVIEW.md</code></pre>
</div>
<p>When a feature touches both public and admin areas, the responsibility of each side must be explicit. The same content should not be duplicated unless the duplicate is clearly a compatibility pointer, archive copy, or generated output.</p>

<h3 id="workflow" class="h4 mt-5">4. Engineering Workflow</h3>
<p>The standard Guild CMS workflow is:</p>
<ol>
    <li>Architecture discussion</li>
    <li>Development Center alignment</li>
    <li>Implementation</li>
    <li>Security review</li>
    <li>Public site publication when applicable</li>
    <li>Release package</li>
</ol>
<p>This order protects the project from undocumented implementation drift. A change should not be treated as complete until the relevant engineering record, implementation files, SQL, public documentation, and release notes are aligned.</p>

<h3 id="development-center" class="h4 mt-5">5. Development Center Responsibilities</h3>
<p>The Development Center is the engineering management system. It should track status, phase, roadmap, publication metadata, timeline entries, journal entries, changelog entries, and package history. It should not become a duplicate public documentation site.</p>
<p>For Engineering Library documents, the Development Center should show publication metadata and link to the public Guild CMS site as the authoritative publication location.</p>

<h3 id="public-site" class="h4 mt-5">6. Public Site Responsibilities</h3>
<p>The public Guild CMS site is the authoritative publication surface for Engineering Library content. When a publication is marked Published, the public site should contain the readable publication page, metadata, revision history, table of contents, and stable URL.</p>
<p>Public pages should be stable, readable, and accessible without admin permissions. Public content must avoid exposing private development notes, credentials, local paths that are not useful to readers, or unfinished implementation details.</p>

<h3 id="database-work" class="h4 mt-5">7. Database and SQL Work</h3>
<p>Database changes must be explicit. A package that changes data should include SQL scripts and verification scripts. A package that does not require database changes should say so in the implementation guide and security review.</p>
<ul>
    <li>Use idempotent inserts where practical.</li>
    <li>Prefer transactions for related updates.</li>
    <li>Use verification queries so installation can be checked quickly.</li>
    <li>Never include credentials in release packages.</li>
</ul>

<h3 id="security-review" class="h4 mt-5">8. Security Review Expectations</h3>
<p>Every package requires a security review, even when the expected result is that no security-sensitive behavior changed. Developers should identify whether the package affects authentication, authorization, database writes, file uploads, output rendering, headers, user input, or public exposure.</p>
<p>For static publication packages, the review should still verify that no new privileged operations, new superglobal usage, unsafe output, or unnecessary database schema changes were introduced.</p>

<h3 id="release-packages" class="h4 mt-5">9. Release Package Preparation</h3>
<p>A Guild CMS release package should contain only the files changed by that package plus required SQL and documentation. It is not a full project archive unless explicitly stated.</p>
<div class="guild-card-soft p-3 mb-4">
    <h4 class="h6">Required package documentation</h4>
    <ul class="mb-0">
        <li><code>README.md</code></li>
        <li><code>RELEASE_NOTES.md</code></li>
        <li><code>IMPLEMENTATION_GUIDE.md</code></li>
        <li><code>SECURITY_REVIEW.md</code></li>
        <li><code>docs/PACKAGE_MANIFEST.md</code></li>
    </ul>
</div>
<p>Package names should include the Guild CMS version marker, publication or feature identifier, and short descriptive title.</p>

<h3 id="maintenance" class="h4 mt-5">10. Maintenance and Stewardship</h3>
<p>Developers are stewards of both the codebase and the engineering record. A correct implementation that leaves the roadmap, Development Center, SQL, changelog, or public documentation inconsistent is incomplete.</p>
<p>When conflicts are found, the preferred response is to create a focused maintenance package that corrects the source of truth and documents the correction. Small synchronization packages are part of responsible engineering practice.</p>

<div class="guild-card-soft p-3 mt-5">
    <h3 class="h5">Publication Certification</h3>
    <p class="mb-1"><strong>Publication:</strong> GCMS-ENG-005</p>
    <p class="mb-1"><strong>Title:</strong> Developer Handbook</p>
    <p class="mb-1"><strong>Status:</strong> Published</p>
    <p class="mb-0"><strong>Maintained By:</strong> Guild CMS Engineering</p>
</div>
<?php
$body = ob_get_clean();

guildcms_engineering_publication_page($publication, $body);
require __DIR__ . '/../includes/footer.php';
