<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('contribution-guide.php');
$page_title = 'Contribution Guide';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<p class="lead">The Contribution Guide defines how people contribute to Guild CMS without weakening the engineering discipline established by the Constitution, Vision &amp; Mission, Engineering Principles, Architecture Standards, and Developer Handbook. It exists to make contribution expectations clear, repeatable, respectful, and sustainable.</p>

<div class="guild-card-soft p-3 mb-4">
    <h3 class="h5">Revision History</h3>
    <div class="table-responsive">
        <table class="table table-dark table-sm align-middle mb-0">
            <thead><tr><th>Version</th><th>Date</th><th>Description</th></tr></thead>
            <tbody><tr><td>1.0</td><td>June 2026</td><td>Initial publication as GCMS-ENG-006.</td></tr></tbody>
        </table>
    </div>
</div>

<h3 class="h4 mt-4">Table of Contents</h3>
<ol>
    <li><a href="#purpose">Purpose</a></li>
    <li><a href="#who-can-contribute">Who Can Contribute</a></li>
    <li><a href="#contribution-types">Contribution Types</a></li>
    <li><a href="#workflow">Contribution Workflow</a></li>
    <li><a href="#proposal-quality">Proposal Quality</a></li>
    <li><a href="#implementation-expectations">Implementation Expectations</a></li>
    <li><a href="#documentation-expectations">Documentation Expectations</a></li>
    <li><a href="#review-process">Review Process</a></li>
    <li><a href="#security-and-privacy">Security and Privacy</a></li>
    <li><a href="#release-packages">Release Package Requirements</a></li>
    <li><a href="#community-conduct">Community Conduct</a></li>
    <li><a href="#stewardship">Stewardship</a></li>
</ol>

<h3 id="purpose" class="h4 mt-5">1. Purpose</h3>
<p><strong>GCMS-ENG-006</strong> establishes the contribution model for Guild CMS. The goal is not to create unnecessary process. The goal is to protect the project from accidental drift, undocumented changes, inconsistent release packages, and changes that solve a short-term problem while creating long-term maintenance cost.</p>
<p>Contributions should strengthen Guild CMS as a platform, not merely change code. A good contribution improves the implementation, the engineering record, and the future maintainability of the project.</p>

<h3 id="who-can-contribute" class="h4 mt-5">2. Who Can Contribute</h3>
<p>Guild CMS welcomes contributions from maintainers, administrators, developers, designers, documentation writers, testers, security reviewers, community operators, and users who can clearly describe a problem or improvement.</p>
<p>Contributors are not required to understand the entire system before participating. They are expected to respect the existing architecture, ask questions when uncertain, and follow the standards that apply to the work they are changing.</p>

<h3 id="contribution-types" class="h4 mt-5">3. Contribution Types</h3>
<p>Contributions may include code, documentation, security findings, test results, bug reports, feature proposals, accessibility feedback, usability improvements, design suggestions, release verification, and operational notes.</p>
<ul>
    <li><strong>Bug reports</strong> should describe what happened, what was expected, where it occurred, and how to reproduce it when possible.</li>
    <li><strong>Feature proposals</strong> should explain the user problem, the proposed behavior, affected areas, and likely documentation impact.</li>
    <li><strong>Code changes</strong> should follow the existing structure and include SQL, documentation, and security review notes when applicable.</li>
    <li><strong>Documentation changes</strong> should improve clarity without duplicating authoritative content across multiple locations.</li>
    <li><strong>Security reports</strong> should be handled carefully and should avoid public disclosure until reviewed.</li>
</ul>

<h3 id="workflow" class="h4 mt-5">4. Contribution Workflow</h3>
<p>Contributions should follow the standard Guild CMS workflow whenever the change is significant:</p>
<ol>
    <li>Architecture discussion</li>
    <li>Development Center alignment</li>
    <li>Implementation</li>
    <li>Security review</li>
    <li>Public site update when applicable</li>
    <li>Release package</li>
</ol>
<p>Small corrections may use a lighter process, but they should still preserve the same principle: the code, documentation, Development Center, SQL, and release notes must not contradict each other.</p>

<h3 id="proposal-quality" class="h4 mt-5">5. Proposal Quality</h3>
<p>A strong proposal explains the reason for the change before describing the implementation. Contributors should identify the affected audience, the current limitation, the proposed outcome, the files or systems likely to change, and any security or compatibility concerns.</p>
<p>When a proposal changes architecture, plugin behavior, authentication, permissions, database structure, installation, upgrade behavior, or public documentation, it should be reviewed against the Engineering Library before implementation begins.</p>

<h3 id="implementation-expectations" class="h4 mt-5">6. Implementation Expectations</h3>
<p>Implementations should preserve existing coding style and project structure. Contributors should avoid creating parallel frameworks, duplicating logic, bypassing shared helpers, or introducing private conventions that are not documented.</p>
<ul>
    <li>Modify the smallest practical set of files.</li>
    <li>Use complete files in release packages, not loose snippets.</li>
    <li>Preserve public/admin separation.</li>
    <li>Respect phpBB-backed authentication where it remains the active provider.</li>
    <li>Use parameterized database access for dynamic queries.</li>
    <li>Escape output according to context.</li>
    <li>Do not include credentials, private tokens, or local-only secrets.</li>
</ul>

<h3 id="documentation-expectations" class="h4 mt-5">7. Documentation Expectations</h3>
<p>Documentation is part of the contribution, not an optional extra. A contribution that changes behavior should update the relevant user-facing or engineering-facing documentation.</p>
<p>The public Guild CMS site is the authoritative home for published Engineering Library documents. The Development Center should track metadata and workflow state, not duplicate public document bodies.</p>
<p>When a contribution introduces a new concept, the contributor should consider whether the change also requires updates to the roadmap, timeline, changelog, Development Journal, Architecture Standards, Developer Handbook, Coding Standards, Security Standards, or future ADRs.</p>

<h3 id="review-process" class="h4 mt-5">8. Review Process</h3>
<p>Review should be constructive, evidence-based, and tied to project standards. A reviewer should explain the reason for a requested change and, where possible, cite the relevant Engineering Library publication or architecture decision.</p>
<p>Review should consider correctness, security, maintainability, consistency, documentation, installation impact, upgrade impact, and release package quality. A technically working change can still be incomplete if it leaves the engineering record inconsistent.</p>

<h3 id="security-and-privacy" class="h4 mt-5">9. Security and Privacy</h3>
<p>Security-sensitive contributions require extra care. Changes involving authentication, authorization, session behavior, cookies, CSRF protection, uploads, file handling, SQL, headers, logging, private data, or admin actions must include explicit security review notes.</p>
<p>Security findings should be reported with enough detail to verify the issue while avoiding unnecessary public exposure. Credentials, private keys, passwords, access tokens, and sensitive operational details must never be included in release packages or public issue text.</p>

<h3 id="release-packages" class="h4 mt-5">10. Release Package Requirements</h3>
<p>Guild CMS release packages should contain only the changed files required for that package, any required SQL scripts, verification scripts, and release documentation.</p>
<div class="guild-card-soft p-3 mb-4">
    <h4 class="h6">Required package materials</h4>
    <ul class="mb-0">
        <li><code>README.md</code></li>
        <li><code>RELEASE_NOTES.md</code></li>
        <li><code>IMPLEMENTATION_GUIDE.md</code></li>
        <li><code>SECURITY_REVIEW.md</code></li>
        <li><code>docs/PACKAGE_MANIFEST.md</code></li>
        <li><code>sql/</code> scripts when database changes or data alignment are required</li>
    </ul>
</div>
<p>Packages should be understandable by someone who did not participate in the development conversation. The README and implementation guide should explain what changed, where files go, and how to verify success.</p>

<h3 id="community-conduct" class="h4 mt-5">11. Community Conduct</h3>
<p>Guild CMS contributors should communicate with respect, patience, and clarity. Disagreement is expected in engineering work, but it should focus on evidence, tradeoffs, project principles, and maintainability rather than personal preference or personal criticism.</p>
<p>Contributors should assume good intent, ask clarifying questions, document decisions, and help make the project easier for the next contributor to understand.</p>

<h3 id="stewardship" class="h4 mt-5">12. Stewardship</h3>
<p>Every contributor is a steward of Guild CMS. Stewardship means leaving the project better documented, more secure, easier to maintain, and more understandable than it was before the contribution.</p>
<p>A contribution should serve current users while respecting future maintainers. The best contributions solve the immediate problem and reduce the chance that the same class of problem will return later.</p>

<div class="guild-card-soft p-3 mt-5">
    <h3 class="h5">Publication Certification</h3>
    <p class="mb-1"><strong>Publication:</strong> GCMS-ENG-006</p>
    <p class="mb-1"><strong>Title:</strong> Contribution Guide</p>
    <p class="mb-1"><strong>Status:</strong> Published</p>
    <p class="mb-0"><strong>Maintained By:</strong> Guild CMS Engineering</p>
</div>
<?php
$body = ob_get_clean();

guildcms_engineering_publication_page($publication, $body);
require __DIR__ . '/../includes/footer.php';
