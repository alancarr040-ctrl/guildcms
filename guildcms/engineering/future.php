<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('future.php');
$page_title = 'Engineering Roadmap & Publication Framework';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<p class="lead">The Engineering Roadmap and Publication Framework defines how the Guild CMS Engineering Library grows, how publications are numbered and reviewed, and how future engineering knowledge remains organized, discoverable, and maintainable.</p>

<div class="guild-card-soft p-3 mb-4">
    <h3 class="h5">Revision History</h3>
    <div class="table-responsive">
        <table class="table table-dark table-sm align-middle mb-0">
            <thead><tr><th>Version</th><th>Date</th><th>Description</th></tr></thead>
            <tbody><tr><td>1.0</td><td>June 2026</td><td>Initial publication as GCMS-ENG-010 and completion of Volume I.</td></tr></tbody>
        </table>
    </div>
</div>

<h3 class="h4 mt-4">Table of Contents</h3>
<ol>
    <li><a href="#purpose">Purpose and Scope</a></li>
    <li><a href="#volume-one">Volume I Completion</a></li>
    <li><a href="#library-purpose">Purpose of the Engineering Library</a></li>
    <li><a href="#lifecycle">Publication Lifecycle</a></li>
    <li><a href="#numbering">Publication Numbering</a></li>
    <li><a href="#versioning">Versioning and Revision Policy</a></li>
    <li><a href="#review">Publication Review Process</a></li>
    <li><a href="#roles">Development Center and Public Site Roles</a></li>
    <li><a href="#future-volumes">Future Volumes</a></li>
    <li><a href="#roadmap">Engineering Publication Roadmap</a></li>
    <li><a href="#maintenance">Maintenance Requirements</a></li>
    <li><a href="#references">Related Publications</a></li>
</ol>

<h3 id="purpose" class="h4 mt-5">1. Purpose and Scope</h3>
<p><strong>GCMS-ENG-010</strong> establishes the long-term publication framework for the Guild CMS Engineering Library. It defines how engineering publications are planned, drafted, reviewed, published, revised, deprecated, and archived.</p>
<p>This publication applies to all public Engineering Library documents, future standards, Architecture Decision Records, developer handbooks, security guidance, release engineering documents, provider references, plugin and theme framework documents, and future operational publications.</p>

<h3 id="volume-one" class="h4 mt-5">2. Volume I Completion</h3>
<p>GCMS-ENG-010 completes <strong>Volume I — Engineering Foundation</strong>. Volume I establishes the foundational governance, standards, and publication model required before Guild CMS moves deeper into installer, migration, plugin, theme, CLI, API, provider, authentication, and enterprise work.</p>
<div class="table-responsive">
    <table class="table table-dark table-sm align-middle">
        <thead><tr><th>ID</th><th>Publication</th><th>Role</th></tr></thead>
        <tbody>
            <tr><td>GCMS-ENG-000</td><td>Founder's Note</td><td>Introduces the Engineering Library.</td></tr>
            <tr><td>GCMS-ENG-001</td><td>Guild CMS Constitution</td><td>Defines the enduring principles of the project.</td></tr>
            <tr><td>GCMS-ENG-002</td><td>Vision & Mission</td><td>Defines direction and purpose.</td></tr>
            <tr><td>GCMS-ENG-003</td><td>Engineering Principles</td><td>Defines practical engineering values.</td></tr>
            <tr><td>GCMS-ENG-004</td><td>Architecture Standards</td><td>Defines structural architecture expectations.</td></tr>
            <tr><td>GCMS-ENG-005</td><td>Developer Handbook</td><td>Defines how developers work within Guild CMS.</td></tr>
            <tr><td>GCMS-ENG-006</td><td>Contribution Guide</td><td>Defines contribution workflow.</td></tr>
            <tr><td>GCMS-ENG-007</td><td>Coding Standards</td><td>Defines implementation standards.</td></tr>
            <tr><td>GCMS-ENG-008</td><td>Security Standards</td><td>Defines secure development requirements.</td></tr>
            <tr><td>GCMS-ENG-009</td><td>Architecture Decision Records</td><td>Defines decision documentation.</td></tr>
            <tr><td>GCMS-ENG-010</td><td>Engineering Roadmap & Publication Framework</td><td>Defines publication governance and future roadmap.</td></tr>
        </tbody>
    </table>
</div>

<h3 id="library-purpose" class="h4 mt-5">3. Purpose of the Engineering Library</h3>
<p>The Engineering Library exists to make Guild CMS engineering knowledge durable, public, reviewable, and maintainable. It is not a marketing section and not a private admin feature. It is the public reference system for project architecture, standards, principles, security expectations, development guidance, and major decisions.</p>
<ul>
    <li>It preserves reasoning behind implementation choices.</li>
    <li>It gives contributors a stable engineering reference.</li>
    <li>It keeps standards visible outside the source code.</li>
    <li>It reduces repeated debate over settled decisions.</li>
    <li>It supports long-term stewardship beyond any single implementation phase.</li>
</ul>

<h3 id="lifecycle" class="h4 mt-5">4. Publication Lifecycle</h3>
<p>Every Engineering Library document should move through an explicit lifecycle.</p>
<ul>
    <li><strong>Reserved:</strong> Identifier and location are reserved, but content is not ready.</li>
    <li><strong>Draft:</strong> Initial content exists but is not authoritative.</li>
    <li><strong>Review:</strong> Content is ready for technical, security, or editorial review.</li>
    <li><strong>Published:</strong> Content is authoritative for the stated version.</li>
    <li><strong>Revised:</strong> Content has been updated while retaining the same publication identity.</li>
    <li><strong>Deprecated:</strong> Content remains available but should not guide new work.</li>
    <li><strong>Archived:</strong> Content is retained for history and no longer applies to active Guild CMS development.</li>
</ul>
<p>Published documents may be revised, but their identifier remains stable.</p>

<h3 id="numbering" class="h4 mt-5">5. Publication Numbering</h3>
<p>Guild CMS uses stable identifiers so publications can be cited from release notes, code comments, ADRs, reviews, and future standards.</p>
<ul>
    <li><code>GCMS-ENG-###</code> identifies Engineering Library publications.</li>
    <li><code>GCMS-ADR-####</code> identifies Architecture Decision Records.</li>
    <li>Future specialized series may be introduced for security, operations, API references, provider specifications, or SDK references.</li>
</ul>
<p>Publication numbers are never reused, even if a document is deprecated or archived.</p>

<h3 id="versioning" class="h4 mt-5">6. Versioning and Revision Policy</h3>
<p>Engineering publications use document versions to communicate change significance.</p>
<ul>
    <li><strong>1.0:</strong> Initial published version.</li>
    <li><strong>Patch revisions:</strong> Typographical, formatting, or clarification updates that do not change meaning.</li>
    <li><strong>Minor revisions:</strong> Additions or clarifications that preserve compatibility with the original intent.</li>
    <li><strong>Major revisions:</strong> Changes that alter meaning, requirements, or governance expectations.</li>
</ul>
<p>Every published document should include revision history. Significant revisions should be noted in the Development Center changelog.</p>

<h3 id="review" class="h4 mt-5">7. Publication Review Process</h3>
<p>Publication review depends on document type.</p>
<ul>
    <li>Governance documents require architecture review.</li>
    <li>Security documents require security review.</li>
    <li>Coding and architecture standards require implementation review.</li>
    <li>ADR documents require review of context, decision, alternatives, and consequences.</li>
    <li>Release-related publications require installation and rollback review.</li>
</ul>
<p>Review should verify that the public Engineering Library, Development Center publication tracking, SQL changelog updates, release documentation, and public navigation remain synchronized.</p>

<h3 id="roles" class="h4 mt-5">8. Development Center and Public Site Roles</h3>
<p>The Development Center and public site have distinct responsibilities.</p>
<ul>
    <li><strong>Development Center:</strong> Tracks publication metadata, roadmap alignment, journal entries, timeline records, changelog entries, and package status.</li>
    <li><strong>Public Guild CMS Site:</strong> Hosts the authoritative published Engineering Library documents.</li>
</ul>
<p>The Development Center may link to publications, summarize status, and record lifecycle information. It should not duplicate the full public document body.</p>

<h3 id="future-volumes" class="h4 mt-5">9. Future Volumes</h3>
<p>Future Engineering Library volumes should expand from foundation into implementation, operations, extension development, integration, and enterprise architecture.</p>
<div class="table-responsive">
    <table class="table table-dark table-sm align-middle">
        <thead><tr><th>Future Volume</th><th>Focus</th></tr></thead>
        <tbody>
            <tr><td>Volume II</td><td>Installation, bootstrap, upgrade, and migration standards.</td></tr>
            <tr><td>Volume III</td><td>Plugin SDK, extension lifecycle, hooks, events, and package standards.</td></tr>
            <tr><td>Volume IV</td><td>Theme engine, templates, assets, layout inheritance, and accessibility.</td></tr>
            <tr><td>Volume V</td><td>CLI, developer tools, release builder, and automation workflows.</td></tr>
            <tr><td>Volume VI</td><td>REST API, developer services, integrations, and service contracts.</td></tr>
            <tr><td>Volume VII</td><td>Provider framework expansion, authentication, storage, cache, search, logging, and notification providers.</td></tr>
            <tr><td>Volume VIII</td><td>Native authentication, identity, session, cookie, and permission systems.</td></tr>
            <tr><td>Volume IX</td><td>Enterprise, multi-site, operational architecture, and governance at scale.</td></tr>
        </tbody>
    </table>
</div>

<h3 id="roadmap" class="h4 mt-5">10. Engineering Publication Roadmap</h3>
<p>The Engineering Library should evolve alongside the Guild CMS product roadmap.</p>
<ul>
    <li><strong>Phase 4.4:</strong> Installation and Bootstrap publications.</li>
    <li><strong>Phase 4.5:</strong> Upgrade and Migration publications.</li>
    <li><strong>Phase 5.0:</strong> Plugin SDK and Extension Framework publications.</li>
    <li><strong>Phase 5.1:</strong> Theme Engine and Template System publications.</li>
    <li><strong>Phase 5.2:</strong> CLI and Developer Tooling publications.</li>
    <li><strong>Phase 5.3:</strong> REST API and Developer Services publications.</li>
    <li><strong>Phase 5.4:</strong> Provider Framework Expansion publications.</li>
    <li><strong>Phase 5.5:</strong> Native Authentication System publications.</li>
    <li><strong>Phase 6.0:</strong> Enterprise and Multi-site publications.</li>
</ul>

<h3 id="maintenance" class="h4 mt-5">11. Maintenance Requirements</h3>
<p>Every release that affects Engineering Library content should verify publication metadata, public links, Development Center status, changelog entries, timeline entries, journal entries, and release documentation.</p>
<ul>
    <li>Publication status must match between public and Development Center views.</li>
    <li>Version and phase values must be synchronized.</li>
    <li>New publications must include a public PHP page and archive Markdown copy.</li>
    <li>Publications should be linted and reviewed before release.</li>
    <li>Single-quoted PHP strings must escape apostrophes.</li>
    <li>Release packages must include implementation and rollback guidance.</li>
</ul>

<h3 id="references" class="h4 mt-5">12. Related Publications</h3>
<ul>
    <li><a href="constitution.php">GCMS-ENG-001 — The Guild CMS Constitution</a></li>
    <li><a href="vision-mission.php">GCMS-ENG-002 — Vision & Mission</a></li>
    <li><a href="principles.php">GCMS-ENG-003 — Engineering Principles</a></li>
    <li><a href="architecture-standards.php">GCMS-ENG-004 — Architecture Standards</a></li>
    <li><a href="developer-handbook.php">GCMS-ENG-005 — Developer Handbook</a></li>
    <li><a href="contribution-guide.php">GCMS-ENG-006 — Contribution Guide</a></li>
    <li><a href="coding-standards.php">GCMS-ENG-007 — Coding Standards</a></li>
    <li><a href="security-standards.php">GCMS-ENG-008 — Security Standards</a></li>
    <li><a href="adr.php">GCMS-ENG-009 — Architecture Decision Records</a></li>
</ul>
<?php
$content = ob_get_clean();
guildcms_engineering_publication_page($publication, $body_html);
require __DIR__ . '/../includes/footer.php';
