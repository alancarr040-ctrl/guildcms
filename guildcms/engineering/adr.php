<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('adr.php');
$page_title = 'Architecture Decision Records';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<p class="lead">Architecture Decision Records preserve the reasoning behind important Guild CMS engineering decisions. They explain the context, alternatives, decision, consequences, and references that led to a durable architectural direction.</p>

<div class="guild-card-soft p-3 mb-4">
    <h3 class="h5">Revision History</h3>
    <div class="table-responsive">
        <table class="table table-dark table-sm align-middle mb-0">
            <thead><tr><th>Version</th><th>Date</th><th>Description</th></tr></thead>
            <tbody><tr><td>1.0</td><td>June 2026</td><td>Initial publication as GCMS-ENG-009.</td></tr></tbody>
        </table>
    </div>
</div>

<h3 class="h4 mt-4">Table of Contents</h3>
<ol>
    <li><a href="#purpose">Purpose and Scope</a></li>
    <li><a href="#why-adrs">Why Guild CMS Uses ADRs</a></li>
    <li><a href="#when-required">When an ADR Is Required</a></li>
    <li><a href="#lifecycle">ADR Lifecycle</a></li>
    <li><a href="#numbering">Numbering and Identification</a></li>
    <li><a href="#format">Standard ADR Format</a></li>
    <li><a href="#review">Review and Approval</a></li>
    <li><a href="#index">Initial ADR Index</a></li>
    <li><a href="#adr-0001">ADR-0001 Public Engineering Library</a></li>
    <li><a href="#adr-0002">ADR-0002 Development Center and Public Site Separation</a></li>
    <li><a href="#adr-0003">ADR-0003 Provider-Based Architecture</a></li>
    <li><a href="#adr-0004">ADR-0004 Incremental Release Package Workflow</a></li>
    <li><a href="#adr-0005">ADR-0005 Engineering Publication System</a></li>
    <li><a href="#references">Related Publications</a></li>
</ol>

<h3 id="purpose" class="h4 mt-5">1. Purpose and Scope</h3>
<p><strong>GCMS-ENG-009</strong> establishes the Architecture Decision Record standard for Guild CMS. It defines when decisions must be recorded, how records are structured, how they are reviewed, and how they remain discoverable as the project evolves.</p>
<p>This publication applies to Guild CMS core, public site architecture, Development Center architecture, provider systems, future plugin and theme frameworks, security architecture, database architecture, release workflows, and long-lived engineering governance decisions.</p>

<h3 id="why-adrs" class="h4 mt-5">2. Why Guild CMS Uses ADRs</h3>
<p>Code explains what the system currently does. Architecture Decision Records explain why the system was designed that way. Guild CMS uses ADRs to prevent architectural reasoning from being lost in chat history, commit messages, release notes, or individual memory.</p>
<ul>
    <li>ADRs preserve engineering context for future maintainers.</li>
    <li>ADRs make tradeoffs visible.</li>
    <li>ADRs reduce repeated debate over settled decisions.</li>
    <li>ADRs help contributors understand constraints before proposing changes.</li>
    <li>ADRs give the Development Center and Engineering Library a shared record of architectural intent.</li>
</ul>

<h3 id="when-required" class="h4 mt-5">3. When an ADR Is Required</h3>
<p>An ADR is required when a decision changes the structure, boundaries, long-term behavior, governance model, security posture, data model, extension model, or release process of Guild CMS.</p>
<ul>
    <li>Introducing or replacing a provider architecture.</li>
    <li>Changing authentication, authorization, session, cookie, or security header ownership.</li>
    <li>Adding a public API, plugin SDK, theme engine, CLI, installer, migration framework, or enterprise feature model.</li>
    <li>Changing public/admin separation.</li>
    <li>Changing release packaging, versioning, or migration policy.</li>
    <li>Accepting a significant dependency or removing an important dependency.</li>
    <li>Making an exception to an Engineering Library standard.</li>
</ul>
<p>An ADR is not required for routine bug fixes, copy edits, minor styling adjustments, small documentation updates, or implementation work that follows an already documented standard.</p>

<h3 id="lifecycle" class="h4 mt-5">4. ADR Lifecycle</h3>
<p>Every ADR must have one lifecycle status.</p>
<ul>
    <li><strong>Proposed:</strong> The decision is being discussed and is not yet authoritative.</li>
    <li><strong>Accepted:</strong> The decision is approved and guides implementation.</li>
    <li><strong>Superseded:</strong> A later ADR replaces the decision.</li>
    <li><strong>Deprecated:</strong> The decision remains historical but should not guide new work.</li>
    <li><strong>Rejected:</strong> The decision was considered but not adopted.</li>
</ul>
<p>Accepted ADRs are architectural commitments. They may evolve, but they should not be ignored or silently contradicted.</p>

<h3 id="numbering" class="h4 mt-5">5. Numbering and Identification</h3>
<p>Guild CMS ADRs use the identifier format <code>GCMS-ADR-NNNN</code>. Numbers are assigned sequentially and never reused. Superseded or rejected ADRs retain their identifiers.</p>
<ul>
    <li>Example: <code>GCMS-ADR-0001</code></li>
    <li>Example: <code>GCMS-ADR-0002</code></li>
    <li>Example: <code>GCMS-ADR-0003</code></li>
</ul>
<p>The publication defining ADR governance is <strong>GCMS-ENG-009</strong>. Individual decision records use the <code>GCMS-ADR</code> namespace.</p>

<h3 id="format" class="h4 mt-5">6. Standard ADR Format</h3>
<p>Each ADR should use the following structure:</p>
<ol>
    <li><strong>Identifier</strong></li>
    <li><strong>Title</strong></li>
    <li><strong>Status</strong></li>
    <li><strong>Date</strong></li>
    <li><strong>Context</strong></li>
    <li><strong>Decision</strong></li>
    <li><strong>Consequences</strong></li>
    <li><strong>Alternatives Considered</strong></li>
    <li><strong>Related Publications</strong></li>
</ol>
<p>The format is intentionally concise. ADRs should be long enough to preserve reasoning but short enough to remain useful during review.</p>

<h3 id="review" class="h4 mt-5">7. Review and Approval</h3>
<p>ADR review should happen before implementation when practical. For decisions discovered during implementation, the ADR should be written before the release package is finalized.</p>
<ul>
    <li>Architecture decisions should be discussed before they are encoded into multiple files.</li>
    <li>Security-sensitive ADRs must receive security review.</li>
    <li>Release packages should reference any ADRs they implement or introduce.</li>
    <li>The Development Center should track ADR publication status.</li>
    <li>The public Engineering Library should remain the authoritative publication location.</li>
</ul>

<h3 id="index" class="h4 mt-5">8. Initial ADR Index</h3>
<div class="table-responsive">
    <table class="table table-dark table-sm align-middle">
        <thead><tr><th>ID</th><th>Title</th><th>Status</th><th>Decision Area</th></tr></thead>
        <tbody>
            <tr><td>GCMS-ADR-0001</td><td>Public Engineering Library</td><td>Accepted</td><td>Documentation Governance</td></tr>
            <tr><td>GCMS-ADR-0002</td><td>Development Center and Public Site Separation</td><td>Accepted</td><td>System Boundaries</td></tr>
            <tr><td>GCMS-ADR-0003</td><td>Provider-Based Architecture</td><td>Accepted</td><td>Core Extensibility</td></tr>
            <tr><td>GCMS-ADR-0004</td><td>Incremental Release Package Workflow</td><td>Accepted</td><td>Release Engineering</td></tr>
            <tr><td>GCMS-ADR-0005</td><td>Engineering Publication System</td><td>Accepted</td><td>Engineering Governance</td></tr>
        </tbody>
    </table>
</div>

<h3 id="adr-0001" class="h4 mt-5">9. GCMS-ADR-0001 — Public Engineering Library</h3>
<p><strong>Status:</strong> Accepted<br><strong>Date:</strong> June 2026</p>
<h4 class="h6">Context</h4>
<p>Guild CMS required a permanent location for architecture, principles, standards, governance, and decision records. Keeping this knowledge only in code, admin pages, or private development notes would make it difficult for future contributors to understand the project.</p>
<h4 class="h6">Decision</h4>
<p>Guild CMS will maintain a public Engineering Library at the public Guild CMS site. The Engineering Library is a first-class public section, not an admin-only feature.</p>
<h4 class="h6">Consequences</h4>
<ul>
    <li>Engineering knowledge is visible and reviewable.</li>
    <li>Public documentation must be maintained with release packages.</li>
    <li>The project gains a durable publication model for future standards.</li>
</ul>
<h4 class="h6">Alternatives Considered</h4>
<p>Keeping documentation inside the Development Center was rejected because it would hide engineering standards from contributors and duplicate content with the public site.</p>

<h3 id="adr-0002" class="h4 mt-5">10. GCMS-ADR-0002 — Development Center and Public Site Separation</h3>
<p><strong>Status:</strong> Accepted<br><strong>Date:</strong> June 2026</p>
<h4 class="h6">Context</h4>
<p>The Development Center manages engineering state, while the public site publishes project information. Duplicating full documents in both places would create drift.</p>
<h4 class="h6">Decision</h4>
<p>The Development Center will track publication metadata, workflow state, roadmap alignment, journal entries, and release history. The public Guild CMS site will host authoritative published Engineering Library documents.</p>
<h4 class="h6">Consequences</h4>
<ul>
    <li>The Development Center becomes the engineering management system.</li>
    <li>The public site becomes the publishing platform.</li>
    <li>Publication metadata must remain synchronized.</li>
</ul>
<h4 class="h6">Alternatives Considered</h4>
<p>Maintaining full document copies in both systems was rejected because it would create inconsistent versions.</p>

<h3 id="adr-0003" class="h4 mt-5">11. GCMS-ADR-0003 — Provider-Based Architecture</h3>
<p><strong>Status:</strong> Accepted<br><strong>Date:</strong> June 2026</p>
<h4 class="h6">Context</h4>
<p>Guild CMS must support current phpBB-backed installations while preserving a path toward native authentication, future providers, plugins, and broader deployment models.</p>
<h4 class="h6">Decision</h4>
<p>Guild CMS will use provider-based architecture for replaceable services such as authentication, storage, cache, search, notification, logging, and future integrations.</p>
<h4 class="h6">Consequences</h4>
<ul>
    <li>Core services can evolve without rewriting every feature.</li>
    <li>Provider boundaries must be documented and tested.</li>
    <li>Future SDK work must respect provider contracts.</li>
</ul>
<h4 class="h6">Alternatives Considered</h4>
<p>Hard-coding phpBB ownership into all future Guild CMS components was rejected because it would block standalone deployments and future authentication work.</p>

<h3 id="adr-0004" class="h4 mt-5">12. GCMS-ADR-0004 — Incremental Release Package Workflow</h3>
<p><strong>Status:</strong> Accepted<br><strong>Date:</strong> June 2026</p>
<h4 class="h6">Context</h4>
<p>Guild CMS development uses small release packages that are easy to review, upload, test, and roll back. Repackaging the entire project for every update does not scale.</p>
<h4 class="h6">Decision</h4>
<p>Guild CMS release packages will contain only changed files, required SQL scripts, release documentation, implementation guidance, and security review notes.</p>
<h4 class="h6">Consequences</h4>
<ul>
    <li>Packages remain focused and reviewable.</li>
    <li>Installers must apply SQL explicitly when included.</li>
    <li>Release notes must clearly identify changed files and testing steps.</li>
</ul>
<h4 class="h6">Alternatives Considered</h4>
<p>Full project ZIP releases for every small package were rejected because they increase upload size and overwrite risk.</p>

<h3 id="adr-0005" class="h4 mt-5">13. GCMS-ADR-0005 — Engineering Publication System</h3>
<p><strong>Status:</strong> Accepted<br><strong>Date:</strong> June 2026</p>
<h4 class="h6">Context</h4>
<p>Engineering documents need stable identifiers, version metadata, publication status, revision history, and cross-reference capability.</p>
<h4 class="h6">Decision</h4>
<p>Guild CMS will publish formal Engineering Library documents using stable publication identifiers such as <code>GCMS-ENG-001</code> and decision identifiers such as <code>GCMS-ADR-0001</code>.</p>
<h4 class="h6">Consequences</h4>
<ul>
    <li>Future standards can cite earlier publications precisely.</li>
    <li>Documents can evolve while preserving their original identity.</li>
    <li>The Engineering Library becomes an interconnected reference system.</li>
</ul>
<h4 class="h6">Alternatives Considered</h4>
<p>Generic documentation pages without identifiers were rejected because they do not provide durable references for architecture and release work.</p>

<h3 id="references" class="h4 mt-5">14. Related Publications</h3>
<ul>
    <li><a href="constitution.php">GCMS-ENG-001 — The Guild CMS Constitution</a></li>
    <li><a href="principles.php">GCMS-ENG-003 — Engineering Principles</a></li>
    <li><a href="architecture-standards.php">GCMS-ENG-004 — Architecture Standards</a></li>
    <li><a href="coding-standards.php">GCMS-ENG-007 — Coding Standards</a></li>
    <li><a href="security-standards.php">GCMS-ENG-008 — Security Standards</a></li>
</ul>
<?php
$content = ob_get_clean();
guildcms_engineering_publication_page($publication, $body_html);
require __DIR__ . '/../includes/footer.php';
