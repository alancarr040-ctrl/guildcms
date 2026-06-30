<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('vision-mission.php');
$page_title = 'Vision & Mission';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

$body_html = <<<'HTML'
<div class="engineering-publication-body">
    <p class="lead">Vision & Mission defines what Guild CMS is trying to become, who it exists to serve, and how the project should measure long-term success.</p>

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
                        <td>Initial publication of GCMS-ENG-002 during Phase 4.3.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="guild-card-soft p-3 mb-4">
        <h3 class="h5 mb-3">Table of Contents</h3>
        <ol class="mb-0">
            <li><a href="#preamble" class="link-light">Preamble</a></li>
            <li><a href="#section-1" class="link-light">Vision Statement</a></li>
            <li><a href="#section-2" class="link-light">Mission Statement</a></li>
            <li><a href="#section-3" class="link-light">Audience and Community Role</a></li>
            <li><a href="#section-4" class="link-light">Product Direction</a></li>
            <li><a href="#section-5" class="link-light">Engineering Direction</a></li>
            <li><a href="#section-6" class="link-light">Documentation Direction</a></li>
            <li><a href="#section-7" class="link-light">Success Criteria</a></li>
            <li><a href="#section-8" class="link-light">Long-Term Stewardship</a></li>
            <li><a href="#references" class="link-light">Related Publications</a></li>
            <li><a href="#certification" class="link-light">Publication Certification</a></li>
        </ol>
    </div>

    <h3 id="preamble" class="mt-5">Preamble</h3>
    <p>Guild CMS exists because long-running gaming communities need software that respects their history, adapts to their future, and remains maintainable by the people who inherit it. Many communities begin with forums, hand-built pages, image galleries, rosters, event calendars, and scattered tools. Over time those tools become important records of identity and continuity.</p>
    <p>The vision of Guild CMS is to turn that reality into a deliberate platform: one that supports community websites, guild archives, public identity, administration, integrations, and future extensibility without losing sight of simplicity and stewardship.</p>

    <h3 id="section-1" class="mt-5">Section 1 — Vision Statement</h3>
    <h4 class="h5">§1.1 Product Vision</h4>
    <p>Guild CMS shall become a secure, modular, extensible content management platform designed for guilds, clans, alliances, gaming communities, and long-running online organizations.</p>
    <h4 class="h5">§1.2 Community Vision</h4>
    <p>Guild CMS shall help communities preserve identity, publish information, organize content, manage history, and present themselves professionally without requiring every community to build a custom platform from scratch.</p>
    <h4 class="h5">§1.3 Engineering Vision</h4>
    <p>Guild CMS shall be developed as an engineering-first project. Architecture, documentation, security, upgradeability, and maintainability are part of the product, not secondary concerns.</p>

    <h3 id="section-2" class="mt-5">Section 2 — Mission Statement</h3>
    <h4 class="h5">§2.1 Platform Mission</h4>
    <p>The mission of Guild CMS is to provide a practical platform for building, managing, and extending community websites while keeping administration approachable and engineering standards strong.</p>
    <h4 class="h5">§2.2 Operational Mission</h4>
    <p>Guild CMS shall make routine site management safer and easier by providing consistent administration tools, structured data management, documented workflows, and release packages that can be reviewed and tested.</p>
    <h4 class="h5">§2.3 Stewardship Mission</h4>
    <p>Guild CMS shall preserve the knowledge required to maintain the platform. Important decisions, standards, releases, and architectural changes shall be documented so future maintainers can understand both what was built and why it was built that way.</p>

    <h3 id="section-3" class="mt-5">Section 3 — Audience and Community Role</h3>
    <h4 class="h5">§3.1 Primary Audience</h4>
    <p>The primary audience is gaming communities that need a public website, administrative tools, content sections, member-facing information, and long-term continuity.</p>
    <h4 class="h5">§3.2 Secondary Audience</h4>
    <p>The secondary audience includes developers, administrators, community officers, archivists, and technical stewards who need to operate, customize, extend, or preserve community infrastructure.</p>
    <h4 class="h5">§3.3 Community Identity</h4>
    <p>Guild CMS should help communities express their identity. The platform should support themes, sections, plugins, media, history, and game-specific modules without forcing every community into the same presentation or structure.</p>

    <h3 id="section-4" class="mt-5">Section 4 — Product Direction</h3>
    <h4 class="h5">§4.1 Modular CMS Core</h4>
    <p>The platform direction is a modular CMS core with replaceable and extensible parts. Content, layout, sections, administration, themes, plugins, providers, and integrations should become increasingly separable over time.</p>
    <h4 class="h5">§4.2 Public and Administrative Separation</h4>
    <p>The public website and Development Center have distinct responsibilities. The public site presents published information and project knowledge. The Development Center tracks engineering state, planning, status, and implementation records.</p>
    <h4 class="h5">§4.3 Installation and Upgrade Path</h4>
    <p>Guild CMS shall mature toward a predictable installation, bootstrap, upgrade, and migration framework. Future administrators should be able to deploy and maintain the platform without relying on undocumented manual steps.</p>
    <h4 class="h5">§4.4 Extension Ecosystem</h4>
    <p>Guild CMS shall support future extension through plugins, themes, provider interfaces, APIs, and developer tools. Extension points should be documented, stable where appropriate, and reviewed through engineering governance.</p>

    <h3 id="section-5" class="mt-5">Section 5 — Engineering Direction</h3>
    <h4 class="h5">§5.1 Security and Maintainability</h4>
    <p>Engineering direction shall continue to prioritize security and maintainability before feature volume. New capabilities should strengthen the platform without weakening its reviewability or upgrade path.</p>
    <h4 class="h5">§5.2 Incremental Release Discipline</h4>
    <p>Guild CMS shall continue using incremental release packages that contain changed files, SQL updates when required, implementation notes, release notes, and security review documentation.</p>
    <h4 class="h5">§5.3 Architecture Governance</h4>
    <p>Significant changes should be aligned with the roadmap, Engineering Library, Development Center, and, when appropriate, Architecture Decision Records.</p>

    <h3 id="section-6" class="mt-5">Section 6 — Documentation Direction</h3>
    <h4 class="h5">§6.1 Engineering Library</h4>
    <p>The Engineering Library is the authoritative home for public engineering knowledge. It shall contain foundational publications, standards, architecture guidance, security guidance, contribution guidance, developer references, and future decision records.</p>
    <h4 class="h5">§6.2 Documentation as Product</h4>
    <p>Documentation should be planned, reviewed, and published as part of the product. Guild CMS should not depend on hidden knowledge, private memory, or undocumented operational behavior.</p>
    <h4 class="h5">§6.3 Public Clarity</h4>
    <p>Public documentation should help administrators, contributors, and interested community members understand the platform direction without requiring access to private development discussions.</p>

    <h3 id="section-7" class="mt-5">Section 7 — Success Criteria</h3>
    <h4 class="h5">§7.1 Usability</h4>
    <p>Guild CMS succeeds when community administrators can manage meaningful portions of their site through understandable tools instead of fragile manual edits.</p>
    <h4 class="h5">§7.2 Extensibility</h4>
    <p>Guild CMS succeeds when new sections, themes, integrations, and community-specific features can be added without destabilizing the core platform.</p>
    <h4 class="h5">§7.3 Maintainability</h4>
    <p>Guild CMS succeeds when future maintainers can understand the code, documentation, release history, and reasoning behind major decisions.</p>
    <h4 class="h5">§7.4 Trust</h4>
    <p>Guild CMS succeeds when users and administrators can trust that security, data handling, upgrades, and releases are treated with discipline.</p>

    <h3 id="section-8" class="mt-5">Section 8 — Long-Term Stewardship</h3>
    <h4 class="h5">§8.1 Continuity</h4>
    <p>Guild CMS should be built for continuity across contributors, servers, communities, technology changes, and operational transitions.</p>
    <h4 class="h5">§8.2 Preservation</h4>
    <p>Community data and history matter. The platform should respect the need to preserve posts, images, links, rosters, stories, timelines, and institutional memory wherever practical.</p>
    <h4 class="h5">§8.3 Future Responsibility</h4>
    <p>Every major change should consider the future administrator, future developer, and future community member who will depend on the system after the original implementer has moved on.</p>

    <h3 id="references" class="mt-5">Related Publications</h3>
    <ul>
        <li><strong>GCMS-ENG-000</strong> — Founder’s Note</li>
        <li><strong>GCMS-ENG-001</strong> — The Guild CMS Constitution</li>
        <li><strong>GCMS-ENG-003</strong> — Engineering Principles <span class="guild-muted">(planned)</span></li>
        <li><strong>GCMS-ENG-004</strong> — Architecture Standards <span class="guild-muted">(planned)</span></li>
    </ul>

    <div id="certification" class="guild-card-soft p-3 mt-5">
        <h3 class="h5 mb-3">Publication Certification</h3>
        <div class="engineering-meta mb-0">
            <div><strong>Publication</strong><span>GCMS-ENG-002</span></div>
            <div><strong>Title</strong><span>Vision &amp; Mission</span></div>
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
