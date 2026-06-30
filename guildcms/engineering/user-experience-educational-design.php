<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('user-experience-educational-design.php');
$page_title = 'User Experience & Educational Design Principles';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

$body_html = <<<'HTML'
<div class="engineering-publication-body">
    <p class="lead">User Experience &amp; Educational Design Principles defines how Guild CMS should help people understand, install, configure, administer, and extend the platform with confidence.</p>

    <div class="guild-card-soft p-3 mb-4">
        <h3 class="h5 mb-3">Revision History</h3>
        <div class="table-responsive">
            <table class="table table-dark table-sm align-middle mb-0">
                <thead><tr><th>Version</th><th>Date</th><th>Description</th></tr></thead>
                <tbody><tr><td>1.0</td><td>June 2026</td><td>Initial publication of GCMS-ENG-011 during Phase 4.4.</td></tr></tbody>
            </table>
        </div>
    </div>

    <div class="guild-card-soft p-3 mb-4">
        <h3 class="h5 mb-3">Table of Contents</h3>
        <ol class="mb-0">
            <li><a href="#preamble" class="link-light">Preamble</a></li>
            <li><a href="#principle-1" class="link-light">Explain Before Asking</a></li>
            <li><a href="#principle-2" class="link-light">Teach, Do Not Assume</a></li>
            <li><a href="#principle-3" class="link-light">The Installer Is the First Teacher</a></li>
            <li><a href="#principle-4" class="link-light">Confidence Is a Product Goal</a></li>
            <li><a href="#principle-5" class="link-light">Errors Should Educate</a></li>
            <li><a href="#principle-6" class="link-light">Documentation Complements the Interface</a></li>
            <li><a href="#principle-7" class="link-light">Use Role-Based Language</a></li>
            <li><a href="#principle-8" class="link-light">Progressive Disclosure</a></li>
            <li><a href="#principle-9" class="link-light">Accessibility and Respect</a></li>
            <li><a href="#principle-10" class="link-light">Review Questions</a></li>
            <li><a href="#references" class="link-light">Related Publications</a></li>
            <li><a href="#certification" class="link-light">Publication Certification</a></li>
        </ol>
    </div>

    <h3 id="preamble" class="mt-5">Preamble</h3>
    <p>Guild CMS exists to help people succeed, not merely to complete tasks. A site owner, administrator, contributor, or developer should leave each Guild CMS interaction with more confidence than they had when they arrived.</p>
    <p>This publication establishes the product experience principles that guide the installer, Administration Center, public documentation, plugin workflows, upgrade tools, and future user-facing interfaces. It is intentionally written as an engineering standard because user experience is not decoration added after implementation. It is part of the system design.</p>

    <h3 id="principle-1" class="mt-5">Principle 1 &mdash; Explain Before Asking</h3>
    <p>Guild CMS should explain what a screen is doing before it asks someone to provide information. A form label alone is rarely enough. When Guild CMS asks for a database host, administrator password, site name, theme choice, provider setting, or plugin permission, the interface should briefly explain why the information is needed and what will happen next.</p>
    <p>The preferred pattern is: <strong>what this is</strong>, <strong>why it matters</strong>, <strong>where to find the information</strong>, and <strong>what happens after the action succeeds</strong>.</p>

    <h3 id="principle-2" class="mt-5">Principle 2 &mdash; Teach, Do Not Assume</h3>
    <p>Guild CMS should not assume that the person installing or administering the system has installed a CMS before. The software should teach the basics at the moment the information is needed. Documentation remains important, but it must not become a substitute for clear in-product guidance.</p>
    <p>The installer should be sufficient for a first-time site owner to reach a working installation without being told to leave the process and search through documentation to understand ordinary steps.</p>

    <h3 id="principle-3" class="mt-5">Principle 3 &mdash; The Installer Is the First Teacher</h3>
    <p>The installer is often the first direct experience someone has with Guild CMS. It should introduce the architecture gently: files, database, configuration, administrator account, site identity, modules, themes, security, and completion. Every installer screen should teach enough context to make the person feel oriented rather than interrogated.</p>
    <p>The installation process should be educational, reassuring, and recoverable. It should never feel like a blind sequence of technical prompts.</p>

    <h3 id="principle-4" class="mt-5">Principle 4 &mdash; Confidence Is a Product Goal</h3>
    <p>Guild CMS should make people more confident. Successful steps should confirm what happened. Risky steps should explain risk before action. Destructive actions should clearly describe what will change and whether the action can be reversed.</p>
    <p>A useful review question for every interface is: <em>Will someone feel more capable after using this?</em></p>

    <h3 id="principle-5" class="mt-5">Principle 5 &mdash; Errors Should Educate</h3>
    <p>Error messages must explain what happened, why it likely happened, what Guild CMS did to protect the installation, and what the person can do next. Raw error codes may be useful for developers, but they should not be the only message presented to site owners.</p>
    <p>Whenever practical, Guild CMS should distinguish between no change made, partial change made, and completed change. Recovery guidance should be part of the error experience.</p>

    <h3 id="principle-6" class="mt-5">Principle 6 &mdash; Documentation Complements the Interface</h3>
    <p>Documentation teaches mastery. The interface teaches the immediate task. Guild CMS should not use documentation as an excuse to leave ordinary workflows unexplained.</p>
    <p>Pages may link to deeper documentation, but the person should still understand the basic purpose, risk, and next step from the page itself.</p>

    <h3 id="principle-7" class="mt-5">Principle 7 &mdash; Use Role-Based Language</h3>
    <p>Guild CMS should prefer meaningful role language over generic labels where it improves clarity. A person may be a site owner, administrator, developer, contributor, maintainer, or visitor. The interface should speak to the role involved in the task.</p>
    <p>This principle helps Guild CMS avoid vague messages and design for real responsibilities.</p>

    <h3 id="principle-8" class="mt-5">Principle 8 &mdash; Progressive Disclosure</h3>
    <p>Guild CMS should provide essential guidance directly and deeper explanation nearby. A screen should not overwhelm first-time administrators with unnecessary detail, but it should make additional context available without forcing them to leave the workflow.</p>
    <p>Expandable panels such as <strong>Why am I seeing this?</strong>, <strong>Where do I find this?</strong>, and <strong>What happens next?</strong> are encouraged for installer and administration workflows.</p>

    <h3 id="principle-9" class="mt-5">Principle 9 &mdash; Accessibility and Respect</h3>
    <p>Educational design must remain accessible. Text should be readable, controls should be keyboard-friendly, color should not be the only indicator, and important actions should use clear labels. Friendly language must not become patronizing language.</p>
    <p>Guild CMS should respect the administrator's time, attention, and level of experience.</p>

    <h3 id="principle-10" class="mt-5">Principle 10 &mdash; UX Review Questions</h3>
    <p>User-facing work should be reviewed with questions such as:</p>
    <ul>
        <li>Does this explain itself before asking for input?</li>
        <li>Would a first-time site owner understand what to do?</li>
        <li>Does the page explain where to find required information?</li>
        <li>Does the error message teach and provide recovery guidance?</li>
        <li>Does the workflow avoid trapping the person?</li>
        <li>Does documentation deepen understanding rather than replace missing interface guidance?</li>
        <li>Does this make the person more confident?</li>
    </ul>

    <h3 id="references" class="mt-5">Related Publications</h3>
    <ul>
        <li><a href="/engineering/principles.php" class="link-light">GCMS-ENG-003 &mdash; Engineering Principles</a></li>
        <li><a href="/engineering/architecture-standards.php" class="link-light">GCMS-ENG-004 &mdash; Architecture Standards</a></li>
        <li><a href="/engineering/developer-handbook.php" class="link-light">GCMS-ENG-005 &mdash; Developer Handbook</a></li>
        <li><a href="/engineering/security-standards.php" class="link-light">GCMS-ENG-008 &mdash; Security Standards</a></li>
        <li><a href="/engineering/future.php" class="link-light">GCMS-ENG-010 &mdash; Engineering Roadmap &amp; Publication Framework</a></li>
    </ul>

    <h3 id="certification" class="mt-5">Publication Certification</h3>
    <p class="mb-0">GCMS-ENG-011 is published as the first Engineering Library publication focused on product experience and educational design. It applies to installer work beginning in Phase 4.4 and to future administration, plugin, theme, upgrade, and documentation workflows.</p>
</div>
HTML;

guildcms_engineering_publication_page($publication, $body_html);

require __DIR__ . '/../includes/footer.php';
