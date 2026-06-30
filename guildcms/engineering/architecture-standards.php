<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('architecture-standards.php');
$page_title = 'Architecture Standards';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

$body_html = <<<'HTML'
<div class="engineering-publication-body">
    <p class="lead">Architecture Standards defines the required and recommended structural rules used to design, extend, review, document, and evolve Guild CMS.</p>

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
                        <td>Initial publication of GCMS-ENG-004 during Phase 4.3.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="guild-card-soft p-3 mb-4">
        <h3 class="h5 mb-3">Table of Contents</h3>
        <ol class="mb-0">
            <li><a href="#preamble" class="link-light">Preamble</a></li>
            <li><a href="#section-1" class="link-light">Architectural Philosophy</a></li>
            <li><a href="#section-2" class="link-light">System Architecture</a></li>
            <li><a href="#section-3" class="link-light">Module Architecture</a></li>
            <li><a href="#section-4" class="link-light">Provider Architecture</a></li>
            <li><a href="#section-5" class="link-light">Data Layer Standards</a></li>
            <li><a href="#section-6" class="link-light">User Interface Architecture</a></li>
            <li><a href="#section-7" class="link-light">Security Architecture</a></li>
            <li><a href="#section-8" class="link-light">Plugin Architecture</a></li>
            <li><a href="#section-9" class="link-light">Theme Architecture</a></li>
            <li><a href="#section-10" class="link-light">Documentation Architecture</a></li>
            <li><a href="#section-11" class="link-light">Architecture Decision Records</a></li>
            <li><a href="#section-12" class="link-light">Architecture Evolution</a></li>
            <li><a href="#compliance" class="link-light">Architecture Compliance Matrix</a></li>
            <li><a href="#references" class="link-light">Related Publications</a></li>
            <li><a href="#certification" class="link-light">Publication Certification</a></li>
        </ol>
    </div>

    <h3 id="preamble" class="mt-5">Preamble</h3>
    <p>Guild CMS is intended to become a secure, modular, extensible platform for communities that may operate for many years. Architecture Standards exists so that new features, modules, providers, themes, plugins, and administrative tools are built with consistent structure instead of isolated one-off solutions.</p>
    <p>This publication does not freeze Guild CMS into a single implementation style. Instead, it defines the architectural expectations that future implementations must respect: clear boundaries, reviewable changes, documented decisions, secure defaults, and predictable extension points.</p>

    <h3 id="section-1" class="mt-5">Section 1 &mdash; Architectural Philosophy</h3>
    <h4 class="h5">&sect;1.1 Architecture Must Serve Maintainability</h4>
    <p>Guild CMS architecture shall favor structures that future maintainers can understand, test, document, and safely extend. Cleverness is less valuable than clarity when long-term stewardship is the goal.</p>
    <h4 class="h5">&sect;1.2 Prefer Small, Composable Parts</h4>
    <p>Features should be composed from focused responsibilities rather than large multi-purpose files. Controllers, views, data access, assets, configuration, and documentation should remain distinguishable wherever practical.</p>
    <h4 class="h5">&sect;1.3 Architecture Is a Public Commitment</h4>
    <p>Important architecture decisions should be visible through the Engineering Library, Development Center, release notes, or Architecture Decision Records. Architecture should not exist only in private memory.</p>
    <div class="guild-card-soft p-3 my-3"><strong>Compliance Note:</strong> This section applies GCMS-ENG-001 stewardship commitments and GCMS-ENG-003 maintainability principles to all structural design decisions.</div>

    <h3 id="section-2" class="mt-5">Section 2 &mdash; System Architecture</h3>
    <h4 class="h5">&sect;2.1 Public Site and Development Center Separation</h4>
    <p>The public Guild CMS site is the authoritative publication surface for public project knowledge. The Development Center is the engineering management system for planning, status, metadata, release history, and internal project visibility.</p>
    <h4 class="h5">&sect;2.2 Public, Admin, and Core Boundaries</h4>
    <p>Public pages, administrative pages, shared core functions, data access, and future extension systems should remain logically separated. A public-facing page should not become an administrative controller, and administrative tools should not duplicate public publication content.</p>
    <h4 class="h5">&sect;2.3 Shared Services Should Be Explicit</h4>
    <p>When functionality is shared between sections, it should be moved into a predictable include, service, helper, provider, or future core component rather than copied across unrelated pages.</p>
    <div class="guild-card-soft p-3 my-3"><strong>Compliance Note:</strong> The public Engineering Library content lives on the public site, while Development Center publication tracking links to it as metadata.</div>

    <h3 id="section-3" class="mt-5">Section 3 &mdash; Module Architecture</h3>
    <h4 class="h5">&sect;3.1 Modules Must Have Clear Boundaries</h4>
    <p>A module should represent a recognizable feature area with clear ownership of its controllers, display logic, assets, configuration, database migrations, and documentation.</p>
    <h4 class="h5">&sect;3.2 Module Layout Should Be Predictable</h4>
    <p>Where possible, modules should follow a repeatable structure for page entry points, includes, templates, assets, SQL, tests or verification steps, and documentation. Predictability reduces onboarding cost and review risk.</p>
    <h4 class="h5">&sect;3.3 Avoid Cross-Module Entanglement</h4>
    <p>Modules should communicate through documented interfaces, shared services, events, hooks, or provider contracts rather than direct assumptions about another module&rsquo;s internal files or database queries.</p>
    <div class="guild-card-soft p-3 my-3"><strong>Compliance Note:</strong> Future Developer Handbook and Coding Standards publications will turn this architecture guidance into practical implementation rules.</div>

    <h3 id="section-4" class="mt-5">Section 4 &mdash; Provider Architecture</h3>
    <h4 class="h5">&sect;4.1 Providers Abstract Replaceable Capabilities</h4>
    <p>A provider represents a replaceable implementation of a platform capability. Authentication, storage, search, cache, logging, notifications, uploads, and future AI-assisted services are examples of capabilities that may benefit from provider boundaries.</p>
    <h4 class="h5">&sect;4.2 Provider Contracts Must Be Documented</h4>
    <p>Provider-based systems require clear contracts. A provider interface should document inputs, outputs, failure behavior, security expectations, configuration requirements, and compatibility concerns.</p>
    <h4 class="h5">&sect;4.3 Providers Must Not Hide Security Boundaries</h4>
    <p>Provider abstraction should not obscure authentication, authorization, validation, escaping, or data ownership responsibilities. Replaceable components remain subject to Guild CMS security standards.</p>
    <div class="guild-card-soft p-3 my-3"><strong>Compliance Note:</strong> Phase 5.4 Provider Framework Expansion should cite this section when defining concrete provider interfaces.</div>

    <h3 id="section-5" class="mt-5">Section 5 &mdash; Data Layer Standards</h3>
    <h4 class="h5">&sect;5.1 Database Access Must Be Reviewable</h4>
    <p>Queries should be explicit, parameterized where user input is involved, and understandable during review. Complex SQL should be documented enough that future maintainers understand its purpose and risk.</p>
    <h4 class="h5">&sect;5.2 Schema Changes Require Migration Discipline</h4>
    <p>Database changes should be provided as SQL scripts with release documentation and verification steps when practical. Content-only updates should not be mixed with schema changes unless the package clearly explains why.</p>
    <h4 class="h5">&sect;5.3 Data Ownership Should Be Clear</h4>
    <p>Tables, records, and configuration values should have an identifiable owner or feature area. Shared tables should document their consumers and intended use.</p>
    <div class="guild-card-soft p-3 my-3"><strong>Compliance Note:</strong> Data access must align with future Coding Standards and Security Standards, especially for request handling, escaping, and permission checks.</div>

    <h3 id="section-6" class="mt-5">Section 6 &mdash; User Interface Architecture</h3>
    <h4 class="h5">&sect;6.1 Interface Patterns Should Be Consistent</h4>
    <p>Guild CMS should use consistent page layouts, navigation patterns, cards, tables, forms, badges, and status indicators. Consistency reduces user confusion and simplifies future theming.</p>
    <h4 class="h5">&sect;6.2 Responsive Design Is Required</h4>
    <p>Public and administrative interfaces should be usable across desktop and mobile layouts. Bootstrap-based layout conventions should be preserved unless replaced by an approved theme architecture decision.</p>
    <h4 class="h5">&sect;6.3 Accessibility Should Be Considered During Design</h4>
    <p>Headings, navigation, links, form labels, color contrast, and status indicators should be structured so the interface can become progressively more accessible as the platform matures.</p>
    <div class="guild-card-soft p-3 my-3"><strong>Compliance Note:</strong> Phase 5.1 Theme Engine work should preserve the consistency and accessibility expectations established here.</div>

    <h3 id="section-7" class="mt-5">Section 7 &mdash; Security Architecture</h3>
    <h4 class="h5">&sect;7.1 Trust Boundaries Must Be Explicit</h4>
    <p>Requests, forms, sessions, authentication state, administrative access, uploaded files, database content, and external integrations must be treated as separate trust boundaries.</p>
    <h4 class="h5">&sect;7.2 Authorization Belongs Near Sensitive Actions</h4>
    <p>Administrative and destructive actions should verify authorization near the action itself rather than relying only on navigation visibility or earlier page routing.</p>
    <h4 class="h5">&sect;7.3 Security Review Is Architectural Work</h4>
    <p>Security review is not only code inspection. It also evaluates trust boundaries, data flow, dependencies, permissions, configuration, and operational exposure.</p>
    <div class="guild-card-soft p-3 my-3"><strong>Compliance Note:</strong> GCMS-ENG-008 Security Standards will provide detailed implementation rules for the security architecture principles introduced here.</div>

    <h3 id="section-8" class="mt-5">Section 8 &mdash; Plugin Architecture</h3>
    <h4 class="h5">&sect;8.1 Plugins Extend, They Do Not Rewrite Core</h4>
    <p>Plugins should extend Guild CMS through documented extension points, events, hooks, providers, or APIs. Plugins should not require modification of unrelated core files to function.</p>
    <h4 class="h5">&sect;8.2 Plugin Lifecycle Must Be Defined</h4>
    <p>Discovery, installation, activation, configuration, dependency handling, upgrade, disablement, and removal should be documented before plugins become a supported extension mechanism.</p>
    <h4 class="h5">&sect;8.3 Plugin Risk Must Be Visible</h4>
    <p>Plugins may introduce security and stability risk. Future plugin tooling should expose compatibility, permissions, dependencies, and trust information where practical.</p>
    <div class="guild-card-soft p-3 my-3"><strong>Compliance Note:</strong> Phase 5.0 Plugin SDK should use this section as the architectural foundation for plugin lifecycle and extension rules.</div>

    <h3 id="section-9" class="mt-5">Section 9 &mdash; Theme Architecture</h3>
    <h4 class="h5">&sect;9.1 Themes Control Presentation, Not Business Logic</h4>
    <p>Themes should be responsible for presentation, layout, visual identity, assets, and template overrides. They should not become hidden controllers or duplicate platform logic.</p>
    <h4 class="h5">&sect;9.2 Theme Overrides Require Boundaries</h4>
    <p>Template overrides, child themes, and asset replacement should have documented rules so visual customization does not break upgrades or security expectations.</p>
    <h4 class="h5">&sect;9.3 Public and Admin Themes May Differ</h4>
    <p>The public site and administrative tools may have different theming requirements. Both should still follow consistent accessibility, maintainability, and asset-management expectations.</p>
    <div class="guild-card-soft p-3 my-3"><strong>Compliance Note:</strong> Phase 5.1 Theme Engine &amp; Template System should expand this section into concrete theme contracts and override behavior.</div>

    <h3 id="section-10" class="mt-5">Section 10 &mdash; Documentation Architecture</h3>
    <h4 class="h5">&sect;10.1 Documentation Has Ownership</h4>
    <p>Major features, architecture decisions, standards, migrations, and release packages should include documentation that explains intent, installation, verification, and long-term maintenance impact.</p>
    <h4 class="h5">&sect;10.2 Public Publications Are Authoritative</h4>
    <p>Engineering Library publications reside on the public Guild CMS site. The Development Center tracks metadata, status, and links but should not duplicate publication content.</p>
    <h4 class="h5">&sect;10.3 Release Documentation Is Required</h4>
    <p>Every Guild CMS release package should include README, Release Notes, Implementation Guide, Security Review, required SQL, verification scripts, and a package manifest.</p>
    <div class="guild-card-soft p-3 my-3"><strong>Compliance Note:</strong> This section formalizes the release workflow already used throughout Phase 4.3.</div>

    <h3 id="section-11" class="mt-5">Section 11 &mdash; Architecture Decision Records</h3>
    <h4 class="h5">&sect;11.1 Significant Decisions Need Durable Records</h4>
    <p>When an architecture decision has long-term impact, meaningful tradeoffs, or future compatibility implications, it should be recorded as an Architecture Decision Record.</p>
    <h4 class="h5">&sect;11.2 ADRs Should Capture Context and Consequences</h4>
    <p>An ADR should explain the problem, context, decision, alternatives considered, consequences, status, and related publications or packages.</p>
    <h4 class="h5">&sect;11.3 ADRs Are Part of Governance</h4>
    <p>ADRs create a bridge between design discussion and implemented architecture. They should be discoverable through the Engineering Library when they become public records.</p>
    <div class="guild-card-soft p-3 my-3"><strong>Compliance Note:</strong> GCMS-ADR-000 will define the ADR index and format in a future Engineering Library publication.</div>

    <h3 id="section-12" class="mt-5">Section 12 &mdash; Architecture Evolution</h3>
    <h4 class="h5">&sect;12.1 Architecture May Evolve</h4>
    <p>Guild CMS architecture is allowed to change as the platform matures. Evolution should be intentional, documented, and compatible with the project&rsquo;s Constitution and Engineering Principles.</p>
    <h4 class="h5">&sect;12.2 Backward Compatibility Has Value</h4>
    <p>Existing installations, legacy URLs, data structures, and administrator workflows should not be broken casually. Breaking changes require justification, release notes, and migration guidance.</p>
    <h4 class="h5">&sect;12.3 Refactoring Requires Purpose</h4>
    <p>Refactoring should reduce risk, improve maintainability, clarify boundaries, or prepare for documented future work. Cosmetic churn without clear value should be avoided.</p>
    <div class="guild-card-soft p-3 my-3"><strong>Compliance Note:</strong> Future upgrade and migration framework work should cite this section when defining compatibility guarantees and migration expectations.</div>

    <h3 id="compliance" class="mt-5">Architecture Compliance Matrix</h3>
    <div class="table-responsive">
        <table class="table table-dark table-sm align-middle">
            <thead>
                <tr>
                    <th>Standard</th>
                    <th>Level</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Public Site / Development Center separation</td><td><span class="badge bg-success">Required</span></td><td>Public documents live on the public site; the Development Center tracks metadata.</td></tr>
                <tr><td>Release documentation with each package</td><td><span class="badge bg-success">Required</span></td><td>README, Release Notes, Implementation Guide, Security Review, SQL, and manifest where applicable.</td></tr>
                <tr><td>Parameterized database access for request-driven queries</td><td><span class="badge bg-success">Required</span></td><td>Detailed implementation rules belong in Coding and Security Standards.</td></tr>
                <tr><td>Module boundary documentation</td><td><span class="badge bg-warning text-dark">Recommended</span></td><td>Required for large modules and future plugin-facing systems.</td></tr>
                <tr><td>Provider abstraction for replaceable capabilities</td><td><span class="badge bg-warning text-dark">Recommended</span></td><td>Expected to become required for Phase 5.4 provider systems.</td></tr>
                <tr><td>Architecture Decision Records for major decisions</td><td><span class="badge bg-warning text-dark">Recommended</span></td><td>Expected to become required for major platform decisions.</td></tr>
            </tbody>
        </table>
    </div>

    <h3 id="references" class="mt-5">Related Publications</h3>
    <ul>
        <li><strong>GCMS-ENG-001</strong> &mdash; The Guild CMS Constitution</li>
        <li><strong>GCMS-ENG-002</strong> &mdash; Vision &amp; Mission</li>
        <li><strong>GCMS-ENG-003</strong> &mdash; Engineering Principles</li>
        <li><strong>GCMS-ENG-005</strong> &mdash; Developer Handbook <span class="guild-muted">(planned)</span></li>
        <li><strong>GCMS-ENG-007</strong> &mdash; Coding Standards <span class="guild-muted">(planned)</span></li>
        <li><strong>GCMS-ENG-008</strong> &mdash; Security Standards <span class="guild-muted">(planned)</span></li>
        <li><strong>GCMS-ADR-000</strong> &mdash; Architecture Decision Records <span class="guild-muted">(planned)</span></li>
    </ul>

    <div id="certification" class="guild-card-soft p-3 mt-5">
        <h3 class="h5 mb-3">Publication Certification</h3>
        <div class="engineering-meta mb-0">
            <div><strong>Publication</strong><span>GCMS-ENG-004</span></div>
            <div><strong>Title</strong><span>Architecture Standards</span></div>
            <div><strong>Version</strong><span>1.0</span></div>
            <div><strong>Status</strong><span>Published</span></div>
            <div><strong>Approved During</strong><span>Phase 4.3</span></div>
            <div><strong>Maintained By</strong><span>Guild CMS Engineering</span></div>
        </div>
    </div>
</div>
HTML;

guildcms_engineering_publication_page($publication, $body_html);
require __DIR__ . '/../includes/footer.php';
