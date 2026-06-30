<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('coding-standards.php');
$page_title = 'Coding Standards';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<p class="lead">The Coding Standards publication defines the implementation rules for Guild CMS. It translates the Constitution, Engineering Principles, Architecture Standards, Developer Handbook, and Contribution Guide into practical coding expectations that keep the project secure, maintainable, reviewable, and consistent.</p>

<div class="guild-card-soft p-3 mb-4">
    <h3 class="h5">Revision History</h3>
    <div class="table-responsive">
        <table class="table table-dark table-sm align-middle mb-0">
            <thead><tr><th>Version</th><th>Date</th><th>Description</th></tr></thead>
            <tbody><tr><td>1.0</td><td>June 2026</td><td>Initial publication as GCMS-ENG-007.</td></tr></tbody>
        </table>
    </div>
</div>

<h3 class="h4 mt-4">Table of Contents</h3>
<ol>
    <li><a href="#purpose">Purpose and Scope</a></li>
    <li><a href="#general-principles">General Coding Principles</a></li>
    <li><a href="#php-standards">PHP Standards</a></li>
    <li><a href="#naming">Naming Standards</a></li>
    <li><a href="#file-organization">File Organization</a></li>
    <li><a href="#database">Database and SQL Standards</a></li>
    <li><a href="#security">Security Coding Requirements</a></li>
    <li><a href="#output">HTML and Output Standards</a></li>
    <li><a href="#assets">CSS and JavaScript Standards</a></li>
    <li><a href="#errors">Errors, Logging, and Diagnostics</a></li>
    <li><a href="#documentation">Documentation Requirements</a></li>
    <li><a href="#quality">Quality Assurance</a></li>
    <li><a href="#compatibility">Compatibility and Deprecation</a></li>
    <li><a href="#references">Related Publications</a></li>
</ol>

<h3 id="purpose" class="h4 mt-5">1. Purpose and Scope</h3>
<p><strong>GCMS-ENG-007</strong> establishes the coding standards for Guild CMS core, the Development Center, the public Guild CMS site, Engineering Library pages, public modules, administrative modules, SQL scripts, and release packages.</p>
<p>These standards exist to make code predictable. A developer should be able to open a Guild CMS file, understand its purpose, identify its dependencies, verify how data is handled, and review its security posture without first learning a new local convention.</p>

<h3 id="general-principles" class="h4 mt-5">2. General Coding Principles</h3>
<ul>
    <li><strong>Clarity over cleverness.</strong> Prefer straightforward code that future maintainers can understand quickly.</li>
    <li><strong>Small focused changes.</strong> Modify the smallest practical set of files needed to complete the package.</li>
    <li><strong>One responsibility per file or helper.</strong> Avoid mixing routing, data access, rendering, and state changes in the same section unless the existing architecture requires it.</li>
    <li><strong>Consistency with nearby code.</strong> Preserve established style unless a package explicitly modernizes that area.</li>
    <li><strong>Security is not optional.</strong> A working feature is incomplete if it bypasses validation, escaping, authorization, CSRF protection, or safe SQL handling.</li>
    <li><strong>Documentation travels with implementation.</strong> Significant behavior changes require matching updates to release notes, public documentation, Development Center records, or Engineering Library publications.</li>
</ul>

<h3 id="php-standards" class="h4 mt-5">3. PHP Standards</h3>
<p>New standalone PHP files should use strict type declarations where compatible with the surrounding code:</p>
<pre><code>&lt;?php
declare(strict_types=1);</code></pre>
<p>Existing files should not be mass-reformatted merely to add modern conventions. Modernization should be deliberate, reviewed, and limited to the package scope.</p>
<ul>
    <li>Use complete PHP files in packages, not snippets.</li>
    <li>Prefer typed function parameters and return types for new helper functions.</li>
    <li>Keep helper names specific enough to avoid collisions, such as <code>guildcms_*</code> or <code>devcenter_*</code>.</li>
    <li>Do not redeclare shared helpers without checking <code>function_exists()</code> when the surrounding architecture requires compatibility.</li>
    <li>Use phpBB request helpers in phpBB-backed admin contexts rather than direct superglobal access.</li>
    <li>Do not introduce credentials, tokens, private paths, or environment-specific secrets into committed files.</li>
</ul>

<h3 id="naming" class="h4 mt-5">4. Naming Standards</h3>
<p>Names should communicate intent without requiring hidden project knowledge.</p>
<ul>
    <li>Variables should use lower camel case or the style already used by the file.</li>
    <li>Database table names should remain lower snake case.</li>
    <li>Functions should use lower snake case with a project-specific prefix where practical.</li>
    <li>Publication IDs must remain stable once published, such as <code>GCMS-ENG-007</code>.</li>
    <li>Package names should include the phase/package number and a concise deliverable title.</li>
</ul>

<h3 id="file-organization" class="h4 mt-5">5. File Organization</h3>
<p>Guild CMS separates public site concerns from Development Center concerns. Public Engineering Library documents belong on the public Guild CMS site. The Development Center tracks metadata and workflow state.</p>
<ul>
    <li>Public Engineering Library pages belong under <code>guildcms/engineering/</code>.</li>
    <li>Public Engineering Library metadata belongs in <code>guildcms/engineering/includes/library.php</code>.</li>
    <li>Archived Markdown copies belong under <code>guildcms/docs/engineering/</code>.</li>
    <li>Legacy handbook pointers belong under <code>guildcms/docs/handbook/</code> when compatibility is needed.</li>
    <li>Development Center files belong under <code>admin/pages/</code> or <code>admin/data/</code>.</li>
    <li>SQL scripts belong under <code>sql/</code> in release packages.</li>
</ul>

<h3 id="database" class="h4 mt-5">6. Database and SQL Standards</h3>
<p>SQL must be explicit, reviewable, and safe to apply.</p>
<ul>
    <li>Use prepared statements for dynamic values in PHP.</li>
    <li>Use transactions for related data updates where practical.</li>
    <li>Make data alignment scripts idempotent when possible.</li>
    <li>Include verification scripts for package SQL.</li>
    <li>Never include database credentials in release packages.</li>
    <li>Do not assume a table exists unless the package also verifies or creates it.</li>
</ul>
<p>Schema changes should be rare, named clearly, and accompanied by implementation and rollback guidance when appropriate.</p>

<h3 id="security" class="h4 mt-5">7. Security Coding Requirements</h3>
<p>Security requirements apply to every package, including content-focused packages.</p>
<ul>
    <li>Validate input before use.</li>
    <li>Escape output according to context.</li>
    <li>Use CSRF tokens for state-changing forms.</li>
    <li>Use parameterized SQL for dynamic queries.</li>
    <li>Respect existing authentication and authorization boundaries.</li>
    <li>Do not expose private server paths, credentials, tokens, or sensitive operational data.</li>
    <li>Do not weaken security headers, cookie settings, or upload protections without a documented security review.</li>
</ul>

<h3 id="output" class="h4 mt-5">8. HTML and Output Standards</h3>
<p>HTML output should be accessible, responsive, and consistent with the existing Guild CMS visual language.</p>
<ul>
    <li>Use shared escaping helpers such as <code>guildcms_h()</code>, <code>devcenter_h()</code>, or the existing site helper when outputting dynamic content.</li>
    <li>Use semantic headings in order.</li>
    <li>Prefer Bootstrap components already used by the site.</li>
    <li>Do not duplicate large page bodies between the public site and Development Center.</li>
    <li>Keep public Engineering Library pages readable without admin access.</li>
</ul>

<h3 id="assets" class="h4 mt-5">9. CSS and JavaScript Standards</h3>
<p>CSS and JavaScript should be scoped, minimal, and compatible with the current site architecture.</p>
<ul>
    <li>Prefer existing utility classes and shared CSS before adding new styles.</li>
    <li>Scope custom styles to the module or page family they support.</li>
    <li>Avoid inline JavaScript for new interactive behavior when a shared asset is more appropriate.</li>
    <li>Do not introduce external dependencies without documenting the reason and CSP impact.</li>
    <li>Ensure responsive behavior on desktop and mobile layouts.</li>
</ul>

<h3 id="errors" class="h4 mt-5">10. Errors, Logging, and Diagnostics</h3>
<p>User-facing errors should be clear without exposing internals. Diagnostic information should help administrators resolve problems while avoiding leakage of secrets or sensitive paths.</p>
<ul>
    <li>Use safe fallback messages when optional data is unavailable.</li>
    <li>Do not echo raw database errors to public pages.</li>
    <li>Development Center diagnostics may be more detailed, but still must not expose credentials.</li>
    <li>Log actionable failures where logging infrastructure exists.</li>
</ul>

<h3 id="documentation" class="h4 mt-5">11. Documentation Requirements</h3>
<p>Documentation must be updated when a package changes behavior, architecture, workflow, security posture, installation, upgrade behavior, or public publications.</p>
<ul>
    <li>Every release package must include README, Release Notes, Implementation Guide, and Security Review.</li>
    <li>Engineering publications must include metadata, revision history, body content, and publication certification.</li>
    <li>Development Center tracking must reflect published public documents.</li>
    <li>Public documents should be authoritative; admin pages should link to them rather than duplicate them.</li>
</ul>

<h3 id="quality" class="h4 mt-5">12. Quality Assurance</h3>
<p>Every package should pass a basic release QA check before testing.</p>
<div class="guild-card-soft p-3 mb-4">
    <h4 class="h6">Required QA checks</h4>
    <ul class="mb-0">
        <li>Run <code>php -l</code> on changed PHP files.</li>
        <li>Verify single-quoted PHP strings escape apostrophes.</li>
        <li>Verify publication metadata is synchronized between public site and Development Center.</li>
        <li>Verify public links resolve to the intended Engineering Library pages.</li>
        <li>Verify SQL scripts do not reference nonexistent tables.</li>
        <li>Verify release documentation matches package contents.</li>
    </ul>
</div>

<h3 id="compatibility" class="h4 mt-5">13. Compatibility and Deprecation</h3>
<p>Guild CMS should preserve compatibility unless a change is intentionally documented as a migration. Deprecation should give maintainers enough information to understand what changed, why it changed, and what replacement path exists.</p>
<ul>
    <li>Do not remove public URLs without redirects or compatibility notes.</li>
    <li>Do not rename publication IDs after publication.</li>
    <li>Do not silently change database expectations.</li>
    <li>Document compatibility impact in release notes.</li>
</ul>

<h3 id="references" class="h4 mt-5">14. Related Publications</h3>
<ul>
    <li><strong>GCMS-ENG-001</strong> &mdash; The Guild CMS Constitution</li>
    <li><strong>GCMS-ENG-003</strong> &mdash; Engineering Principles</li>
    <li><strong>GCMS-ENG-004</strong> &mdash; Architecture Standards</li>
    <li><strong>GCMS-ENG-005</strong> &mdash; Developer Handbook</li>
    <li><strong>GCMS-ENG-006</strong> &mdash; Contribution Guide</li>
    <li><strong>GCMS-ENG-008</strong> &mdash; Security Standards (planned)</li>
</ul>

<div class="guild-card-soft p-3 mt-5">
    <h3 class="h5">Publication Certification</h3>
    <p class="mb-1"><strong>Publication:</strong> GCMS-ENG-007</p>
    <p class="mb-1"><strong>Title:</strong> Coding Standards</p>
    <p class="mb-1"><strong>Status:</strong> Published</p>
    <p class="mb-0"><strong>Maintained By:</strong> Guild CMS Engineering</p>
</div>
<?php
$body = ob_get_clean();

guildcms_engineering_publication_page($publication, $body);
require __DIR__ . '/../includes/footer.php';
