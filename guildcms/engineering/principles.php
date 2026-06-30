<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('principles.php');
$page_title = 'Engineering Principles';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

$body_html = <<<'HTML'
<div class="engineering-publication-body">
    <p class="lead">Engineering Principles defines the practical rules of judgment used when Guild CMS code, documentation, architecture, security, and releases are planned, reviewed, and maintained.</p>

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
                        <td>Initial publication of GCMS-ENG-003 during Phase 4.3.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="guild-card-soft p-3 mb-4">
        <h3 class="h5 mb-3">Table of Contents</h3>
        <ol class="mb-0">
            <li><a href="#preamble" class="link-light">Preamble</a></li>
            <li><a href="#section-1" class="link-light">Principle of Security First</a></li>
            <li><a href="#section-2" class="link-light">Principle of Maintainability</a></li>
            <li><a href="#section-3" class="link-light">Principle of Documentation as Deliverable</a></li>
            <li><a href="#section-4" class="link-light">Principle of Incremental Change</a></li>
            <li><a href="#section-5" class="link-light">Principle of Reviewability</a></li>
            <li><a href="#section-6" class="link-light">Principle of Separation of Responsibilities</a></li>
            <li><a href="#section-7" class="link-light">Principle of Compatibility and Migration</a></li>
            <li><a href="#section-8" class="link-light">Principle of Operational Clarity</a></li>
            <li><a href="#section-9" class="link-light">Principle of Public Knowledge</a></li>
            <li><a href="#section-10" class="link-light">Principle of Stewardship</a></li>
            <li><a href="#references" class="link-light">Related Publications</a></li>
            <li><a href="#certification" class="link-light">Publication Certification</a></li>
        </ol>
    </div>

    <h3 id="preamble" class="mt-5">Preamble</h3>
    <p>The Guild CMS Constitution defines the project&rsquo;s enduring commitments. Vision &amp; Mission defines where the project is going and who it exists to serve. Engineering Principles converts those commitments into practical guidance for daily engineering work.</p>
    <p>These principles are not tied to a single file layout, framework, database table, or implementation phase. They are the standards of judgment used when choosing between convenience and maintainability, speed and safety, duplication and clarity, private knowledge and public documentation.</p>

    <h3 id="section-1" class="mt-5">Section 1 &mdash; Principle of Security First</h3>
    <h4 class="h5">&sect;1.1 Security Is a Design Requirement</h4>
    <p>Security shall be considered during design, implementation, review, installation, upgrade, and operation. It is not a final checklist item added after functionality is complete.</p>
    <h4 class="h5">&sect;1.2 Secure Defaults</h4>
    <p>When Guild CMS provides configuration, generated files, examples, or installer behavior, the default path should favor safer behavior over convenience unless a clear operational reason exists.</p>
    <h4 class="h5">&sect;1.3 Defense in Depth</h4>
    <p>Security should not depend on a single control. Validation, authorization, escaping, headers, permissions, logging, and review should reinforce one another where appropriate.</p>

    <h3 id="section-2" class="mt-5">Section 2 &mdash; Principle of Maintainability</h3>
    <h4 class="h5">&sect;2.1 Future Maintainers Matter</h4>
    <p>Code should be written for the person who must understand it later. Clear names, predictable structure, documented assumptions, and small focused changes are preferred over cleverness.</p>
    <h4 class="h5">&sect;2.2 Reduce Hidden Knowledge</h4>
    <p>Important behavior should not exist only in memory, chat history, or scattered comments. If a rule is important enough to affect future work, it should be documented where future maintainers can find it.</p>
    <h4 class="h5">&sect;2.3 Prefer Simpler Failure Modes</h4>
    <p>Systems should fail in understandable ways. When possible, Guild CMS should provide clear errors, verification scripts, release notes, and recovery guidance.</p>

    <h3 id="section-3" class="mt-5">Section 3 &mdash; Principle of Documentation as Deliverable</h3>
    <h4 class="h5">&sect;3.1 Documentation Is Part of the Work</h4>
    <p>A feature, release, standard, or migration is not complete until the documentation needed to understand, install, test, or maintain it has been provided.</p>
    <h4 class="h5">&sect;3.2 Public Engineering Knowledge</h4>
    <p>Public engineering documents belong in the Engineering Library. The Development Center tracks publication metadata and status, while the public site remains the authoritative source for published documents.</p>
    <h4 class="h5">&sect;3.3 Release Documentation</h4>
    <p>Every release package should include a README, Release Notes, Implementation Guide, Security Review, and SQL documentation when database changes are required.</p>

    <h3 id="section-4" class="mt-5">Section 4 &mdash; Principle of Incremental Change</h3>
    <h4 class="h5">&sect;4.1 Small Packages Are Safer</h4>
    <p>Guild CMS releases should prefer focused incremental packages over large mixed changes. Smaller packages are easier to test, understand, review, and roll back.</p>
    <h4 class="h5">&sect;4.2 Changed Files Only</h4>
    <p>Release packages should contain only changed files, new files, required SQL, verification scripts, and release documentation. They should not repackage the whole project unless explicitly required.</p>
    <h4 class="h5">&sect;4.3 Verify After Install</h4>
    <p>Where practical, packages should include verification guidance or SQL checks so administrators can confirm that the intended update was applied.</p>

    <h3 id="section-5" class="mt-5">Section 5 &mdash; Principle of Reviewability</h3>
    <h4 class="h5">&sect;5.1 Changes Should Be Understandable</h4>
    <p>A reviewer should be able to understand what changed, why it changed, and what risk it introduces. Release notes and implementation guides exist to make that review possible.</p>
    <h4 class="h5">&sect;5.2 Preserve Existing Architecture</h4>
    <p>Updates should respect the existing codebase unless the package explicitly includes a refactor. New code should fit the project instead of imposing unrelated patterns.</p>
    <h4 class="h5">&sect;5.3 Explain Tradeoffs</h4>
    <p>When a change introduces a meaningful tradeoff, the reasoning should be documented in release notes, engineering publications, or an Architecture Decision Record as appropriate.</p>

    <h3 id="section-6" class="mt-5">Section 6 &mdash; Principle of Separation of Responsibilities</h3>
    <h4 class="h5">&sect;6.1 Public Site and Development Center</h4>
    <p>The public Guild CMS site publishes official project knowledge. The Development Center manages engineering state, planning, package tracking, and publication metadata.</p>
    <h4 class="h5">&sect;6.2 Content and Metadata</h4>
    <p>Published documents should not be duplicated between systems. The Development Center should link to authoritative public documents rather than maintaining separate copies.</p>
    <h4 class="h5">&sect;6.3 Modules and Boundaries</h4>
    <p>As Guild CMS grows, modules, themes, providers, plugins, administrative pages, public pages, and data access should maintain clear boundaries.</p>

    <h3 id="section-7" class="mt-5">Section 7 &mdash; Principle of Compatibility and Migration</h3>
    <h4 class="h5">&sect;7.1 Existing Installations Matter</h4>
    <p>Guild CMS should avoid breaking existing installations without a documented reason, migration path, and release warning.</p>
    <h4 class="h5">&sect;7.2 Database Changes Require Care</h4>
    <p>SQL changes should be explicit, reviewable, and accompanied by verification steps when practical. Schema changes should be separated from content-only updates whenever possible.</p>
    <h4 class="h5">&sect;7.3 Backward Compatibility Has Value</h4>
    <p>Legacy paths, compatibility pointers, and migration helpers may be appropriate when they reduce disruption and preserve project history.</p>

    <h3 id="section-8" class="mt-5">Section 8 &mdash; Principle of Operational Clarity</h3>
    <h4 class="h5">&sect;8.1 Administrators Need Predictability</h4>
    <p>Installation, upgrade, configuration, verification, and troubleshooting should become increasingly predictable as the platform matures.</p>
    <h4 class="h5">&sect;8.2 Status Should Be Visible</h4>
    <p>Roadmap state, package status, security posture, publication status, and release progress should be visible through the Development Center or public project pages as appropriate.</p>
    <h4 class="h5">&sect;8.3 Automation Should Reduce Risk</h4>
    <p>Future CLI, installer, migration, and release-builder tools should reduce manual error while preserving transparency and administrator control.</p>

    <h3 id="section-9" class="mt-5">Section 9 &mdash; Principle of Public Knowledge</h3>
    <h4 class="h5">&sect;9.1 The Engineering Library Is Canonical</h4>
    <p>The Engineering Library shall be the canonical public home for Guild CMS engineering publications, including the Constitution, Vision &amp; Mission, Engineering Principles, standards, and decision records.</p>
    <h4 class="h5">&sect;9.2 Stable References</h4>
    <p>Engineering publications should use stable identifiers and section numbering so future documents, release notes, issues, and decisions can cite them reliably.</p>
    <h4 class="h5">&sect;9.3 Knowledge Should Outlive Conversations</h4>
    <p>Important engineering reasoning should not depend on private conversations. It should be promoted into durable project documentation when it affects long-term direction.</p>

    <h3 id="section-10" class="mt-5">Section 10 &mdash; Principle of Stewardship</h3>
    <h4 class="h5">&sect;10.1 Build for Inheritance</h4>
    <p>Guild CMS should be built as a system that can be inherited by future administrators, contributors, and communities.</p>
    <h4 class="h5">&sect;10.2 Preserve the Project&rsquo;s Character</h4>
    <p>Future changes should preserve the qualities that define Guild CMS: security, maintainability, modularity, documentation, transparency, and respect for long-running communities.</p>
    <h4 class="h5">&sect;10.3 Improve the Next Release</h4>
    <p>Every package should leave the project easier to understand, safer to operate, or better prepared for future development.</p>

    <h3 id="references" class="mt-5">Related Publications</h3>
    <ul>
        <li><strong>GCMS-ENG-000</strong> &mdash; Founder&rsquo;s Note</li>
        <li><strong>GCMS-ENG-001</strong> &mdash; The Guild CMS Constitution</li>
        <li><strong>GCMS-ENG-002</strong> &mdash; Vision &amp; Mission</li>
        <li><strong>GCMS-ENG-004</strong> &mdash; Architecture Standards <span class="guild-muted">(planned)</span></li>
        <li><strong>GCMS-ENG-007</strong> &mdash; Coding Standards <span class="guild-muted">(planned)</span></li>
        <li><strong>GCMS-ENG-008</strong> &mdash; Security Standards <span class="guild-muted">(planned)</span></li>
    </ul>

    <div id="certification" class="guild-card-soft p-3 mt-5">
        <h3 class="h5 mb-3">Publication Certification</h3>
        <div class="engineering-meta mb-0">
            <div><strong>Publication</strong><span>GCMS-ENG-003</span></div>
            <div><strong>Title</strong><span>Engineering Principles</span></div>
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
