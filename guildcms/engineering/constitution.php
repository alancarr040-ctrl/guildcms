<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('constitution.php');
$page_title = 'The Guild CMS Constitution';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

$body_html = <<<'HTML'
<div class="engineering-publication-body">
    <p class="lead">The Guild CMS Constitution establishes the enduring principles that guide the project, its engineering practices, its public documentation, and its long-term stewardship.</p>

    <div class="guild-card-soft p-3 mb-4">
        <h3 class="h5 mb-3">Revision History</h3>
        <div class="table-responsive">
            <table class="table table-dark table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Version</th>
                        <th>Date</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1.0</td>
                        <td>June 2026</td>
                        <td>Initial publication of GCMS-ENG-001 during Phase 4.3.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="guild-card-soft p-3 mb-4">
        <h3 class="h5 mb-3">Table of Contents</h3>
        <ol class="mb-0">
            <li><a href="#preamble" class="link-light">Preamble</a></li>
            <li><a href="#section-1" class="link-light">Purpose</a></li>
            <li><a href="#section-2" class="link-light">Core Values</a></li>
            <li><a href="#section-3" class="link-light">Engineering Governance</a></li>
            <li><a href="#section-4" class="link-light">Standards</a></li>
            <li><a href="#section-5" class="link-light">Security</a></li>
            <li><a href="#section-6" class="link-light">Documentation</a></li>
            <li><a href="#section-7" class="link-light">Architecture</a></li>
            <li><a href="#section-8" class="link-light">Releases</a></li>
            <li><a href="#section-9" class="link-light">Contributors</a></li>
            <li><a href="#section-10" class="link-light">Stewardship</a></li>
            <li><a href="#glossary" class="link-light">Glossary</a></li>
            <li><a href="#references" class="link-light">Related Publications</a></li>
            <li><a href="#certification" class="link-light">Publication Certification</a></li>
        </ol>
    </div>

    <h3 id="preamble" class="mt-5">Preamble</h3>
    <p>Guild CMS began as a practical effort to modernize and preserve a long-running community website. Over time, that effort became a broader platform: a content management system intended for guilds, gaming communities, and organizations that need durable, maintainable, extensible, and understandable software.</p>
    <p>This Constitution defines the principles that guide Guild CMS as both software and engineering practice. It is not a feature list, implementation manual, or coding standard. It is the foundation against which future architecture, standards, release decisions, documentation, and stewardship responsibilities are measured.</p>
    <p>Technologies will change. Interfaces will mature. Providers, themes, plugins, APIs, and installation systems will evolve. The principles established here are intended to remain stable even as the implementation grows.</p>

    <h3 id="section-1" class="mt-5">Section 1 — Purpose</h3>
    <h4 class="h5">§1.1 Mission</h4>
    <p>Guild CMS exists to provide a secure, modular, extensible, and maintainable content management platform for guilds, gaming communities, and related organizations.</p>
    <h4 class="h5">§1.2 Engineering Purpose</h4>
    <p>The project exists not only to provide features, but to demonstrate disciplined engineering. Guild CMS values architecture, documentation, security, testability, and long-term clarity alongside user-facing capability.</p>
    <h4 class="h5">§1.3 Long-Term Scope</h4>
    <p>Guild CMS is intended to grow beyond a single installation. The platform shall be developed so that site-specific behavior, community-specific content, and reusable CMS capabilities can be separated over time.</p>
    <h4 class="h5">§1.4 Maintainability Over Convenience</h4>
    <p>Short-term convenience shall not take priority over long-term maintainability. Temporary decisions may be made when required, but they should be documented and revisited through the roadmap, Development Center, or Architecture Decision Records.</p>

    <h3 id="section-2" class="mt-5">Section 2 — Core Values</h3>
    <h4 class="h5">§2.1 Security First</h4>
    <p>Security is a foundational requirement. Guild CMS shall prefer secure defaults, explicit authorization, careful input handling, safe output escaping, and regular review over assumptions of safety.</p>
    <h4 class="h5">§2.2 Engineering Excellence</h4>
    <p>Engineering quality is a product feature. Code should be understandable, reviewable, maintainable, and aligned with the published standards of the project.</p>
    <h4 class="h5">§2.3 Documentation as a Feature</h4>
    <p>Documentation is part of the deliverable. A change is not complete merely because code exists; the reasoning, usage, release impact, and maintenance expectations must be recorded where appropriate.</p>
    <h4 class="h5">§2.4 Transparency</h4>
    <p>Guild CMS shall favor visible decisions and public engineering rationale. Important architectural decisions should be documented so future maintainers understand why the system exists in its current form.</p>
    <h4 class="h5">§2.5 Modularity</h4>
    <p>The project shall prefer modular, replaceable, and well-bounded components over tightly coupled systems. Modules, providers, themes, plugins, and services should have clear responsibilities.</p>
    <h4 class="h5">§2.6 Continuous Improvement</h4>
    <p>Guild CMS shall improve through incremental packages, review, testing, documentation, and correction. Defects in code, architecture, documentation, or project data should be treated as opportunities to strengthen the platform.</p>

    <h3 id="section-3" class="mt-5">Section 3 — Engineering Governance</h3>
    <h4 class="h5">§3.1 Development Center</h4>
    <p>The Development Center is the engineering management system for Guild CMS. It records project state, roadmap progress, publication metadata, development history, security posture, and engineering workflow.</p>
    <h4 class="h5">§3.2 Engineering Library</h4>
    <p>The Engineering Library is the authoritative public home for published engineering knowledge. It contains the Constitution, vision documents, standards, handbooks, Architecture Decision Records, and future engineering publications.</p>
    <h4 class="h5">§3.3 Separation of Record and Publication</h4>
    <p>The Development Center tracks publication metadata and project state. The public Guild CMS website hosts published engineering documents. Document bodies should not be duplicated between the two systems.</p>
    <h4 class="h5">§3.4 Roadmap Discipline</h4>
    <p>The roadmap shall describe the intended engineering path of the project. Roadmap changes should be deliberate, recorded, and synchronized between the Development Center and the public site.</p>
    <h4 class="h5">§3.5 Architecture Decision Records</h4>
    <p>Significant architecture decisions should be preserved as Architecture Decision Records when the decision has lasting impact, introduces tradeoffs, or affects future extension points.</p>

    <h3 id="section-4" class="mt-5">Section 4 — Standards</h3>
    <h4 class="h5">§4.1 Published Standards</h4>
    <p>Guild CMS shall maintain published standards for architecture, coding, security, documentation, releases, contribution, and other recurring engineering practices.</p>
    <h4 class="h5">§4.2 Standards Compliance</h4>
    <p>Engineering work should conform to the published standards applicable to that work. When a standard is intentionally not followed, the reason should be documented.</p>
    <h4 class="h5">§4.3 Standards Evolution</h4>
    <p>Standards may evolve as the project matures. Updates should preserve backward understanding, explain the reason for change, and avoid unnecessary disruption.</p>

    <h3 id="section-5" class="mt-5">Section 5 — Security</h3>
    <h4 class="h5">§5.1 Secure Defaults</h4>
    <p>Guild CMS shall prefer secure behavior by default. Administrative access, write operations, uploads, authentication, configuration, and installation workflows require special care.</p>
    <h4 class="h5">§5.2 Defense in Depth</h4>
    <p>The project shall use multiple layers of protection rather than relying on a single control. Application checks, server configuration, database permissions, filesystem permissions, and browser security headers all contribute to the security posture.</p>
    <h4 class="h5">§5.3 Least Privilege</h4>
    <p>Users, services, scripts, files, and database accounts should have only the privileges required for their intended responsibilities.</p>
    <h4 class="h5">§5.4 Security Review</h4>
    <p>Security review is a release responsibility. Packages that affect authentication, authorization, forms, uploads, configuration, database writes, file handling, or public exposure require explicit security consideration.</p>
    <h4 class="h5">§5.5 Security Defects</h4>
    <p>Security defects shall be treated with priority. When a security issue is found, the project should document the risk, remediation, verification, and release impact.</p>

    <h3 id="section-6" class="mt-5">Section 6 — Documentation</h3>
    <h4 class="h5">§6.1 Documentation Requirement</h4>
    <p>Major changes should include documentation appropriate to their impact. This may include release notes, implementation guides, upgrade notes, security reviews, public documentation, or Engineering Library publications.</p>
    <h4 class="h5">§6.2 Knowledge Preservation</h4>
    <p>Engineering knowledge should not exist only in source code, private memory, or temporary conversation. Important rationale should be captured in durable project artifacts.</p>
    <h4 class="h5">§6.3 Public Engineering Knowledge</h4>
    <p>Engineering knowledge that affects users, administrators, contributors, architecture, standards, or project direction should be published in the Engineering Library when appropriate.</p>
    <h4 class="h5">§6.4 Documentation Maintenance</h4>
    <p>Documentation should be maintained as the system changes. Outdated documentation should be corrected, archived, or clearly marked.</p>

    <h3 id="section-7" class="mt-5">Section 7 — Architecture</h3>
    <h4 class="h5">§7.1 Modularity</h4>
    <p>Guild CMS architecture should be modular. Components should have clear boundaries, narrow responsibilities, and well-defined integration points.</p>
    <h4 class="h5">§7.2 Stable Interfaces</h4>
    <p>Interfaces that other parts of the system depend on should be stable, documented, and changed carefully. Compatibility impact should be considered before changing shared behavior.</p>
    <h4 class="h5">§7.3 Replaceable Components</h4>
    <p>Where practical, the platform should support replaceable providers, services, themes, plugins, and adapters without requiring unrelated system changes.</p>
    <h4 class="h5">§7.4 Public and Private Separation</h4>
    <p>Public-facing website behavior and administrative/development behavior should remain clearly separated. The public site publishes knowledge and user-facing content; the Development Center manages engineering state.</p>
    <h4 class="h5">§7.5 Legacy Compatibility</h4>
    <p>Guild CMS may need to preserve compatibility with long-lived installations and legacy community systems. Compatibility work should be intentional, documented, and balanced against long-term architecture.</p>

    <h3 id="section-8" class="mt-5">Section 8 — Releases</h3>
    <h4 class="h5">§8.1 Release Package Discipline</h4>
    <p>Every Guild CMS release package should contain only the files changed by that release, any required database migrations, and the documentation necessary to install, verify, and understand the change.</p>
    <h4 class="h5">§8.2 Required Release Documentation</h4>
    <p>Release packages should include a README, Release Notes, an Implementation Guide, and a Security Review. Additional documentation should be included when the package requires it.</p>
    <h4 class="h5">§8.3 Database Changes</h4>
    <p>Database changes shall be delivered through explicit SQL scripts. Scripts should be written to avoid duplicate data where practical and should be documented in the implementation guide.</p>
    <h4 class="h5">§8.4 Verification</h4>
    <p>Each release should include enough verification guidance for administrators to confirm that the package installed correctly and that the Development Center and public site remain synchronized.</p>

    <h3 id="section-9" class="mt-5">Section 9 — Contributors</h3>
    <h4 class="h5">§9.1 Respectful Collaboration</h4>
    <p>Contributors should communicate respectfully and review work constructively. Technical disagreement should focus on evidence, maintainability, security, and project principles.</p>
    <h4 class="h5">§9.2 Responsibility to Existing Work</h4>
    <p>Contributors inherit the responsibility to understand existing architecture before replacing it. Improvements should preserve what works, document what changes, and explain why.</p>
    <h4 class="h5">§9.3 Review Culture</h4>
    <p>Review is part of engineering quality. Code, documentation, SQL, security posture, and release packaging may all require review before a package is considered complete.</p>

    <h3 id="section-10" class="mt-5">Section 10 — Stewardship</h3>
    <h4 class="h5">§10.1 Long-Term Responsibility</h4>
    <p>Guild CMS is intended to outlive individual features, implementation details, and temporary technology choices. Contributors are stewards of the project, not merely authors of isolated changes.</p>
    <h4 class="h5">§10.2 Future Maintainers</h4>
    <p>Engineering decisions should consider the people who will maintain the system later. A solution that is clever but unclear may be less valuable than one that is straightforward, documented, and reliable.</p>
    <h4 class="h5">§10.3 Enduring Principles</h4>
    <p>This Constitution should remain stable while allowing the project to grow. When implementation changes, the project should preserve its commitments to security, maintainability, transparency, documentation, and responsible engineering.</p>

    <h3 id="glossary" class="mt-5">Glossary</h3>
    <dl>
        <dt>Development Center</dt>
        <dd>The administrative engineering management area used to track Guild CMS roadmap state, engineering metadata, package progress, and internal project records.</dd>
        <dt>Engineering Library</dt>
        <dd>The public Guild CMS publication area containing engineering documents, standards, governance references, and architecture records.</dd>
        <dt>Engineering Publication</dt>
        <dd>A versioned public document with a stable identifier, metadata, publication status, and maintained content.</dd>
        <dt>Release Package</dt>
        <dd>An incremental ZIP package containing changed files, SQL scripts when required, release documentation, and verification guidance.</dd>
        <dt>Architecture Decision Record</dt>
        <dd>A document that records an important architecture decision, its context, tradeoffs, outcome, and status.</dd>
    </dl>

    <h3 id="references" class="mt-5">Related Publications</h3>
    <ul>
        <li><strong>GCMS-ENG-000</strong> — Founder's Note</li>
        <li><strong>GCMS-ENG-002</strong> — Vision &amp; Mission <span class="guild-muted">(planned)</span></li>
        <li><strong>GCMS-ENG-003</strong> — Engineering Principles <span class="guild-muted">(planned)</span></li>
        <li><strong>GCMS-ENG-004</strong> — Architecture Standards <span class="guild-muted">(planned)</span></li>
        <li><strong>GCMS-ENG-008</strong> — Security Standards <span class="guild-muted">(planned)</span></li>
    </ul>

    <div id="certification" class="guild-card-soft p-3 mt-5">
        <h3 class="h5 mb-3">Publication Certification</h3>
        <p><strong>Publication:</strong> GCMS-ENG-001</p>
        <p><strong>Title:</strong> The Guild CMS Constitution</p>
        <p><strong>Version:</strong> 1.0</p>
        <p><strong>Status:</strong> Published</p>
        <p><strong>Approved During:</strong> Phase 4.3 — Engineering Foundation &amp; Governance</p>
        <p><strong>Package:</strong> 4.3.0-4</p>
        <p class="mb-0"><strong>Maintained By:</strong> Guild CMS Engineering</p>
    </div>
</div>
HTML;

guildcms_engineering_publication_page($publication, $body_html);
require __DIR__ . '/../includes/footer.php';
