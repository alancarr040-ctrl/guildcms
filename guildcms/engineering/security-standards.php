<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('security-standards.php');
$page_title = 'Security Standards';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<p class="lead">The Security Standards publication defines the minimum security expectations for Guild CMS development. It converts the security foundation established during Phases 4.1 and 4.2 into ongoing engineering requirements for every future feature, module, provider, package, and publication.</p>

<div class="guild-card-soft p-3 mb-4">
    <h3 class="h5">Revision History</h3>
    <div class="table-responsive">
        <table class="table table-dark table-sm align-middle mb-0">
            <thead><tr><th>Version</th><th>Date</th><th>Description</th></tr></thead>
            <tbody><tr><td>1.0</td><td>June 2026</td><td>Initial publication as GCMS-ENG-008.</td></tr></tbody>
        </table>
    </div>
</div>

<h3 class="h4 mt-4">Table of Contents</h3>
<ol>
    <li><a href="#purpose">Purpose and Scope</a></li>
    <li><a href="#principles">Security Principles</a></li>
    <li><a href="#boundaries">Trust Boundaries</a></li>
    <li><a href="#input">Input Validation</a></li>
    <li><a href="#output">Output Escaping</a></li>
    <li><a href="#requests">Forms, Requests, and CSRF</a></li>
    <li><a href="#auth">Authentication and Authorization</a></li>
    <li><a href="#database">Database Security</a></li>
    <li><a href="#uploads">File Upload and Filesystem Security</a></li>
    <li><a href="#sessions">Sessions, Cookies, and Identity State</a></li>
    <li><a href="#headers">Security Headers and CSP</a></li>
    <li><a href="#logging">Logging, Diagnostics, and Error Handling</a></li>
    <li><a href="#privacy">Privacy and Sensitive Data</a></li>
    <li><a href="#dependencies">Dependencies and Third-Party Assets</a></li>
    <li><a href="#review">Release Security Review</a></li>
    <li><a href="#incidents">Security Defects and Incident Response</a></li>
    <li><a href="#references">Related Publications</a></li>
</ol>

<h3 id="purpose" class="h4 mt-5">1. Purpose and Scope</h3>
<p><strong>GCMS-ENG-008</strong> establishes the security standards for Guild CMS. These standards apply to the public site, Development Center, installable CMS code, future plugins, provider integrations, admin workflows, SQL migrations, release packages, and Engineering Library implementation work.</p>
<p>Security is not a final review activity. It is an engineering requirement that must be considered during architecture discussion, implementation, testing, documentation, and release packaging.</p>

<h3 id="principles" class="h4 mt-5">2. Security Principles</h3>
<ul>
    <li><strong>Secure by default.</strong> New features should require the least risky configuration by default.</li>
    <li><strong>Least privilege.</strong> Code should only access the data, files, and permissions required for its purpose.</li>
    <li><strong>Defense in depth.</strong> Validation, escaping, authorization, CSRF protection, safe SQL, and headers work together.</li>
    <li><strong>Fail safely.</strong> Errors should not expose secrets, credentials, private paths, or sensitive implementation details.</li>
    <li><strong>Reviewability.</strong> Security-sensitive behavior must be easy to identify and review.</li>
    <li><strong>Documentation.</strong> Security decisions, exceptions, and tradeoffs must be documented in release notes, security reviews, or Engineering Library publications.</li>
</ul>

<h3 id="boundaries" class="h4 mt-5">3. Trust Boundaries</h3>
<p>Guild CMS must clearly separate trusted and untrusted inputs. Browser input, query strings, form data, uploaded files, third-party API responses, remote media, database content created by users, and integration payloads are not trusted merely because they arrive through expected paths.</p>
<ul>
    <li>Public requests are untrusted.</li>
    <li>Authenticated requests still require authorization checks.</li>
    <li>Admin-only routes must not assume authorization from URL structure alone.</li>
    <li>Data retrieved from the database must still be escaped before output.</li>
    <li>External assets and providers must be treated as separate trust zones.</li>
</ul>

<h3 id="input" class="h4 mt-5">4. Input Validation</h3>
<p>Input validation must happen before data is used for routing decisions, database writes, filesystem operations, security decisions, or external requests.</p>
<ul>
    <li>Validate expected type, length, format, range, and allowed values.</li>
    <li>Use allow-lists for enumerated values such as statuses, section keys, tabs, and actions.</li>
    <li>Normalize values before comparison when appropriate.</li>
    <li>Reject or safely default unexpected values.</li>
    <li>Do not rely on client-side validation as a security control.</li>
</ul>
<p>In phpBB-backed admin contexts, Guild CMS code should continue using phpBB request helpers instead of direct superglobal access.</p>

<h3 id="output" class="h4 mt-5">5. Output Escaping</h3>
<p>All dynamic output must be escaped for its output context. Database content, user-generated content, route parameters, labels, metadata, and external data must not be printed directly into HTML.</p>
<ul>
    <li>Use existing escaping helpers such as <code>guildcms_h()</code>, <code>devcenter_h()</code>, or the site-level <code>h()</code> helper where available.</li>
    <li>Escape HTML text, attributes, URLs, JavaScript contexts, and CSS contexts appropriately.</li>
    <li>Do not concatenate unescaped values into HTML attributes.</li>
    <li>Preserve intentional markup only when the content source is trusted and documented.</li>
</ul>

<h3 id="requests" class="h4 mt-5">6. Forms, Requests, and CSRF</h3>
<p>Every state-changing request must include CSRF protection. This includes create, update, delete, publish, restore, upload, reorder, toggle, import, export configuration, and administrative maintenance actions.</p>
<ul>
    <li>Verify CSRF tokens before processing state-changing POST actions.</li>
    <li>Use existing admin token helpers where available.</li>
    <li>Do not process dangerous actions through GET requests.</li>
    <li>Validate the requested action against an allow-list.</li>
    <li>Return clear but safe failure messages when token validation fails.</li>
</ul>

<h3 id="auth" class="h4 mt-5">7. Authentication and Authorization</h3>
<p>Authentication identifies a user. Authorization determines whether that user may perform a specific action. Guild CMS code must not confuse the two.</p>
<ul>
    <li>Admin pages must require an authenticated administrative context.</li>
    <li>Action-level authorization must be enforced for sensitive operations.</li>
    <li>Future authentication providers must expose clear capability checks.</li>
    <li>Public pages must not expose admin-only data merely because the user is logged in.</li>
    <li>Permission checks must be server-side.</li>
</ul>

<h3 id="database" class="h4 mt-5">8. Database Security</h3>
<p>Database access must prevent injection, accidental data exposure, and destructive migrations.</p>
<ul>
    <li>Use prepared statements for dynamic values.</li>
    <li>Do not build SQL by concatenating untrusted input.</li>
    <li>Use transactions for related updates where practical.</li>
    <li>Make data migration scripts idempotent when possible.</li>
    <li>Include verification queries for release SQL.</li>
    <li>Do not assume optional tables exist without checking or documenting the requirement.</li>
</ul>

<h3 id="uploads" class="h4 mt-5">9. File Upload and Filesystem Security</h3>
<p>Uploads and filesystem writes require extra scrutiny because they can affect server integrity, public content, and application execution.</p>
<ul>
    <li>Use extension allow-lists.</li>
    <li>Validate MIME type and file structure where practical.</li>
    <li>Generate safe server-side filenames.</li>
    <li>Prevent executable content in upload directories.</li>
    <li>Store uploads only in intended directories.</li>
    <li>Apply least-privilege filesystem permissions.</li>
    <li>Never trust original filenames as safe paths.</li>
</ul>

<h3 id="sessions" class="h4 mt-5">10. Sessions, Cookies, and Identity State</h3>
<p>For the current flagship installation, phpBB owns authentication, sessions, and authentication cookies. Guild CMS must not introduce parallel session ownership accidentally.</p>
<ul>
    <li>Do not add native authentication cookies until the native authentication provider is designed and documented.</li>
    <li>Cookies used for security-sensitive state must be Secure and HttpOnly where applicable.</li>
    <li>SameSite behavior should be explicit where the provider supports it.</li>
    <li>Session lifetime and regeneration behavior must be documented for future native providers.</li>
    <li>Provider boundaries must be clear during the transition from phpBB-backed authentication to native authentication.</li>
</ul>

<h3 id="headers" class="h4 mt-5">11. Security Headers and CSP</h3>
<p>Guild CMS owns application-layer security headers for the flagship implementation. Header changes must be reviewed because they affect browser enforcement, embedding behavior, asset loading, and cross-origin exposure.</p>
<ul>
    <li>Maintain <code>X-Content-Type-Options</code>.</li>
    <li>Maintain a deliberate referrer policy.</li>
    <li>Maintain a deliberate frame or embedding policy.</li>
    <li>Maintain a Permissions-Policy appropriate to the application.</li>
    <li>Use Content-Security-Policy or Report-Only CSP during tuning.</li>
    <li>Document third-party asset sources and CSP impact.</li>
</ul>

<h3 id="logging" class="h4 mt-5">12. Logging, Diagnostics, and Error Handling</h3>
<p>Logs should support investigation without leaking sensitive information.</p>
<ul>
    <li>Do not log passwords, secrets, tokens, session identifiers, or private keys.</li>
    <li>Do not expose raw stack traces or database errors on public pages.</li>
    <li>Admin diagnostics may be more detailed but still must avoid secrets.</li>
    <li>Security-relevant failures should be logged where logging infrastructure exists.</li>
    <li>Error messages should guide administrators without revealing implementation details to attackers.</li>
</ul>

<h3 id="privacy" class="h4 mt-5">13. Privacy and Sensitive Data</h3>
<p>Guild CMS should collect, store, and expose only the information required for the feature being implemented.</p>
<ul>
    <li>Avoid storing unnecessary personal data.</li>
    <li>Restrict access to administrative or private data.</li>
    <li>Document new sensitive data flows.</li>
    <li>Do not expose private operational details in public pages, release notes, or documentation.</li>
    <li>Respect the separation between public site content and Development Center metadata.</li>
</ul>

<h3 id="dependencies" class="h4 mt-5">14. Dependencies and Third-Party Assets</h3>
<p>New dependencies must have a clear purpose, maintenance expectation, and security review.</p>
<ul>
    <li>Prefer existing dependencies when they satisfy the requirement.</li>
    <li>Document why new external assets are needed.</li>
    <li>Review licensing, update expectations, and CSP requirements.</li>
    <li>Avoid adding unnecessary remote scripts or styles.</li>
    <li>Keep dependency installation notes accurate in implementation documentation.</li>
</ul>

<h3 id="review" class="h4 mt-5">15. Release Security Review</h3>
<p>Every Guild CMS release package must include a Security Review. The review should be proportional to the package scope, but it must not be omitted.</p>
<div class="guild-card-soft p-3 mb-4">
    <h4 class="h6">Minimum release security review checklist</h4>
    <ul class="mb-0">
        <li>Changed PHP files pass syntax validation.</li>
        <li>Dynamic output is escaped.</li>
        <li>Dynamic SQL uses prepared statements or safe static statements.</li>
        <li>State-changing requests use CSRF protection.</li>
        <li>Authentication and authorization boundaries are preserved.</li>
        <li>Uploads, filesystem writes, and permissions are reviewed when present.</li>
        <li>Header, CSP, cookie, and session impacts are reviewed when present.</li>
        <li>SQL scripts do not reference nonexistent tables.</li>
        <li>Release documentation accurately describes the security impact.</li>
    </ul>
</div>

<h3 id="incidents" class="h4 mt-5">16. Security Defects and Incident Response</h3>
<p>Security defects must be treated as engineering priorities. Severity, exposure, affected versions, remediation, and follow-up documentation should be recorded clearly.</p>
<ul>
    <li>Confirm the affected component and versions.</li>
    <li>Minimize public disclosure of exploit details until remediation is available.</li>
    <li>Prepare a corrective package with security review.</li>
    <li>Document the remediation and verification steps.</li>
    <li>Update standards or checklists if the issue reveals a process gap.</li>
</ul>

<h3 id="references" class="h4 mt-5">17. Related Publications</h3>
<ul>
    <li><strong>GCMS-ENG-001</strong> &mdash; The Guild CMS Constitution</li>
    <li><strong>GCMS-ENG-003</strong> &mdash; Engineering Principles</li>
    <li><strong>GCMS-ENG-004</strong> &mdash; Architecture Standards</li>
    <li><strong>GCMS-ENG-006</strong> &mdash; Contribution Guide</li>
    <li><strong>GCMS-ENG-007</strong> &mdash; Coding Standards</li>
    <li><strong>GCMS-ENG-009</strong> &mdash; Architecture Decision Records (planned)</li>
</ul>

<div class="guild-card-soft p-3 mt-5">
    <h3 class="h5">Publication Certification</h3>
    <p class="mb-1"><strong>Publication:</strong> GCMS-ENG-008</p>
    <p class="mb-1"><strong>Title:</strong> Security Standards</p>
    <p class="mb-1"><strong>Version:</strong> 1.0</p>
    <p class="mb-1"><strong>Status:</strong> Published</p>
    <p class="mb-1"><strong>Approved During:</strong> Phase 4.3 &mdash; Engineering Foundation &amp; Governance</p>
    <p class="mb-0"><strong>Maintained By:</strong> Guild CMS Engineering</p>
</div>
<?php
$content = ob_get_clean();

guildcms_engineering_publication_page($publication, $body_html);
require __DIR__ . '/../includes/footer.php';
