<?php
/**
 * Guild CMS Development Journal
 * Updated for Package 4.3.0-12 Engineering Library Volume I Completion
 */
?>

<section class="dev-center-section">
    <h1>Development Journal</h1>


    <article class="card bg-dark text-light border-info mb-4">
        <div class="card-header">July 2026 &mdash; Phase 4.4 Roadmap Realigned</div>
        <div class="card-body">
            <p>Package 4.4.0-10 corrects the phase boundary discovered after Installer Certification Milestone 1.</p>
            <p>Configuration generation, requirements validation, database bootstrap, database initialization, administrator account creation, first-run site configuration, plugin manifest format, plugin discovery, hook/event system, and site bootstrap are installer responsibilities and now belong to Phase 4.4.</p>
            <p class="mb-0">Phase 4.5 is restored as Data Normalization &amp; Governance. This package changes roadmap structure only; no new installer runtime functionality is introduced.</p>
        </div>
    </article>




    <article class="card bg-dark text-light border-success mb-4">
        <div class="card-header">July 2026 &mdash; Installer Certification Milestone 1 Published</div>
        <div class="card-body">
            <p>Package 4.4.0-9 publishes <strong>Installer Certification Milestone 1 &mdash; Foundation Platforms</strong> for Guild CMS Installer 4.4.0-8a.</p>
            <p>The milestone certifies Rocky Linux 9.8 with Virtualmin, Rocky Linux 9.8 Minimal, AlmaLinux 9.8 Minimal, Ubuntu 24 Minimal, and Debian 12 Minimal using the standardized Installer Certification Report format.</p>
            <p class="mb-0">This completed Installer Certification Milestone 1, but Package 4.4.0-10 later realigned the roadmap so Phase 4.4 continues through the full installer lifecycle.</p>
        </div>
    </article>


    <article class="card bg-dark text-light border-info mb-4">
        <div class="card-header">June 2026 &mdash; Phase 4.4 Installer Roadmap Refined</div>
        <div class="card-body">
            <p>Phase 4.4 has been refined to reflect the way Guild CMS will actually become installable: first by making devsite safe when unconfigured, then by separating product assumptions from TheRegs.org, and finally by expanding the installer into requirements validation, database bootstrap, completion, and security review.</p>
            <p>The roadmap now treats <strong>devsite</strong> as the installable product tree, while the Admin Development Center and Guild CMS public website remain reference and documentation sites.</p>
            <p class="mb-0">This update keeps the roadmap synchronized with the current architecture before implementation continues with setup detection and product separation preparation.</p>
        </div>
    </article>


    <article class="card bg-dark text-light border-success mb-4">
        <div class="card-header">June 2026 &mdash; Engineering Library Volume I Completed</div>
        <div class="card-body">
            <p>Package 4.3.0-12 completes the Volume I audit for the Guild CMS Engineering Library.</p>
            <p>The foundational Engineering Library set from GCMS-ENG-000 through GCMS-ENG-010 has been published, reviewed for metadata consistency, and synchronized with the Development Center publication registry.</p>
            <p class="mb-0">Volume I now provides the project foundation for governance, constitution, vision, principles, architecture, developer workflow, contributions, coding, security, architecture decision records, and future publication governance.</p>
        </div>
    </article>


    <article class="card bg-dark text-light border-secondary mb-4">
        <div class="card-header">June 2026 &mdash; Engineering Roadmap and Publication Framework Published</div>
        <div class="card-body">
            <p>GCMS-ENG-010, <strong>Engineering Roadmap &amp; Publication Framework</strong>, has been published as the tenth Engineering Library publication and the closing publication of Volume I.</p>
            <p>The Development Center now tracks Volume I as a complete foundational publication set covering governance, vision, principles, architecture, development, contribution, coding, security, decision records, and publication lifecycle.</p>
            <p class="mb-0">This publication defines the Engineering Library lifecycle, numbering model, review process, future volumes, roadmap alignment, and maintenance expectations for long-term engineering documentation.</p>
        </div>
    </article>


    <article class="card bg-dark text-light border-secondary mb-4">
        <div class="card-header">June 2026 &mdash; Architecture Decision Records Published</div>
        <div class="card-body">
            <p>GCMS-ENG-009, <strong>Architecture Decision Records</strong>, has been published as the ninth Engineering Library publication and the formal record system for important Guild CMS architecture decisions.</p>
            <p>The publication defines when ADRs are required, how they are numbered, reviewed, and maintained, and how accepted decisions become part of the project's engineering governance.</p>
            <p class="mb-0">The initial ADR set records the public Engineering Library, Development Center/public site separation, provider-based architecture, incremental release package workflow, and engineering publication system.</p>
        </div>
    </article>


    <article class="card bg-dark text-light border-secondary mb-4">
        <div class="card-header">June 2026 &mdash; Security Standards Published</div>
        <div class="card-body">
            <p>GCMS-ENG-008, <strong>Security Standards</strong>, has been published as the eighth Engineering Library publication and the formal security standard for Guild CMS.</p>
            <p>The Development Center now tracks Security Standards as a published external Engineering Library document and links to the public Guild CMS site as the authoritative source.</p>
            <p class="mb-0">This publication defines standards for trust boundaries, input validation, output escaping, CSRF, authentication, authorization, SQL safety, uploads, sessions, cookies, security headers, logging, privacy, dependencies, release security review, and security defect handling.</p>
        </div>
    </article>


    <article class="card bg-dark text-light border-secondary mb-4">
        <div class="card-header">June 2026 &mdash; Coding Standards Published</div>
        <div class="card-body">
            <p>GCMS-ENG-007, <strong>Coding Standards</strong>, has been published as the seventh Engineering Library publication and the formal implementation standard for Guild CMS.</p>
            <p>The Development Center now tracks Coding Standards as a published external Engineering Library document and links to the public Guild CMS site as the authoritative source.</p>
            <p class="mb-0">This publication defines standards for PHP, naming, file organization, SQL, security coding, output escaping, CSS and JavaScript, diagnostics, documentation, QA, compatibility, and deprecation.</p>
        </div>
    </article>



    <article class="card bg-dark text-light border-secondary mb-4">
        <div class="card-header">June 2026 &mdash; Contribution Guide Published</div>
        <div class="card-body">
            <p>GCMS-ENG-006, <strong>Contribution Guide</strong>, has been published as the sixth Engineering Library publication and the contributor-facing process reference for Guild CMS.</p>
            <p>The Development Center now tracks Contribution Guide as a published external Engineering Library document and links to the public Guild CMS site as the authoritative source.</p>
            <p class="mb-0">This publication defines contribution types, proposal quality, implementation expectations, documentation requirements, review process, security and privacy handling, release package requirements, community conduct, and stewardship practices.</p>
        </div>
    </article>


    <article class="card bg-dark text-light border-secondary mb-4">
        <div class="card-header">June 2026 &mdash; Developer Handbook Published</div>
        <div class="card-body">
            <p>GCMS-ENG-005, <strong>Developer Handbook</strong>, has been published as the fifth Engineering Library publication and the practical onboarding reference for Guild CMS developers.</p>
            <p>The Development Center now tracks Developer Handbook as a published external Engineering Library document and links to the public Guild CMS site as the authoritative source.</p>
            <p class="mb-0">This publication documents developer orientation, project structure, engineering workflow, Development Center responsibilities, public site responsibilities, SQL expectations, security review, release packages, and stewardship practices.</p>
        </div>
    </article>

    <article class="card bg-dark text-light border-secondary mb-4">
        <div class="card-header">June 2026 &mdash; Architecture Standards Published</div>
        <div class="card-body">
            <p>GCMS-ENG-004, <strong>Architecture Standards</strong>, has been published as the fourth Engineering Library publication and the first formal architecture standard.</p>
            <p>The Development Center now tracks Architecture Standards as a published external Engineering Library document and links to the public Guild CMS site as the authoritative source.</p>
            <p class="mb-0">This publication defines structural standards for public/admin separation, module boundaries, provider architecture, data access, user interface architecture, security architecture, plugins, themes, documentation, ADRs, and architecture evolution.</p>
        </div>
    </article>

    <article class="card bg-dark text-light border-secondary mb-4">
        <div class="card-header">June 2026 &mdash; Engineering Principles Published</div>
        <div class="card-body">
            <p>GCMS-ENG-003, <strong>Engineering Principles</strong>, has been published as the third foundational Engineering Library publication.</p>
            <p>The Development Center now tracks Engineering Principles as a published external Engineering Library document and links to the public Guild CMS site as the authoritative source.</p>
            <p class="mb-0">This publication converts the values established by the Constitution and Vision &amp; Mission into practical principles for implementation, review, documentation, security, release discipline, and long-term maintenance.</p>
        </div>
    </article>

    <article class="card bg-dark text-light border-secondary mb-4">
        <div class="card-header">June 2026 &mdash; Vision &amp; Mission Published</div>
        <div class="card-body">
            <p>GCMS-ENG-002, <strong>Vision &amp; Mission</strong>, has been published as the second foundational Engineering Library publication.</p>
            <p>The Development Center now tracks Vision &amp; Mission as a published external Engineering Library document and links to the public Guild CMS site as the authoritative source.</p>
            <p class="mb-0">This publication defines the long-term direction of Guild CMS as a secure, modular, extensible CMS platform for guilds, clans, alliances, gaming communities, and long-running online organizations.</p>
        </div>
    </article>

    <article class="card bg-dark text-light border-secondary mb-4">
        <div class="card-header">June 2026 &mdash; Engineering Foundation Begins</div>
        <div class="card-body">
            <p>Following completion of the Security Foundation and Security Hardening initiatives, Guild CMS has entered a new stage focused on engineering governance and long-term maintainability.</p>
            <p>Phase 4.3 introduces the Engineering Library, a public collection of engineering documentation that will serve as the authoritative source for architecture, standards, engineering principles, coding conventions, and project governance.</p>
            <p class="mb-0">This milestone represents the transition from building software to building a sustainable engineering platform.</p>
        </div>
    </article>
</section>
