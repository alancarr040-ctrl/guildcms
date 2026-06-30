<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('user-experience.php');
$page_title = 'User Experience & Educational Design Principles';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

$body_html = <<<'HTML'
<div class="engineering-publication-body">
    <p class="lead">User Experience &amp; Educational Design Principles defines how Guild CMS should interact with administrators, site owners, developers, and contributors. It establishes the expectation that Guild CMS explains what it is doing, teaches as it guides, and leaves people more confident than when they began.</p>

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
                        <td>Initial publication of GCMS-ENG-011 during Phase 4.4 to guide installer, administration, documentation, and future user-facing design.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="guild-card-soft p-3 mb-4">
        <h3 class="h5 mb-3">Table of Contents</h3>
        <ol class="mb-0">
            <li><a href="#preamble" class="link-light">Preamble</a></li>
            <li><a href="#section-1" class="link-light">Product Experience Mission</a></li>
            <li><a href="#section-2" class="link-light">Educational Design Principles</a></li>
            <li><a href="#section-3" class="link-light">Installer Experience Standard</a></li>
            <li><a href="#section-4" class="link-light">System Readiness and Requirements</a></li>
            <li><a href="#section-5" class="link-light">Navigation, Save, Cancel, and Resume</a></li>
            <li><a href="#section-6" class="link-light">Progress and Feedback</a></li>
            <li><a href="#section-7" class="link-light">Help and Contextual Guidance</a></li>
            <li><a href="#section-8" class="link-light">Error and Recovery Philosophy</a></li>
            <li><a href="#section-9" class="link-light">Administration Center Experience</a></li>
            <li><a href="#section-10" class="link-light">Modern, Professional, and Accessible</a></li>
            <li><a href="#section-11" class="link-light">Product Separation and First-Run Behavior</a></li>
            <li><a href="#section-12" class="link-light">Package Design Review Questions</a></li>
            <li><a href="#references" class="link-light">Related Publications</a></li>
            <li><a href="#certification" class="link-light">Publication Certification</a></li>
        </ol>
    </div>

    <h3 id="preamble" class="mt-5">Preamble</h3>
    <p>Guild CMS is not intended to be another site project that becomes difficult to understand over time. It is intended to become a product experience that respects the person using it. The installer, Administration Center, documentation, Engineering Library, release process, and error messages should all reflect that purpose.</p>
    <p>The project shall be built around the experience it believes people should have, not merely the experience people have historically endured from web installers and CMS platforms. Guild CMS should be educational, professional, modern, and accessible.</p>

    <h3 id="section-1" class="mt-5">Section 1 &mdash; Product Experience Mission</h3>
    <h4 class="h5">&sect;1.1 Build Experiences, Not Only Features</h4>
    <p>Every feature should be evaluated by the experience it creates. A feature is not successful merely because it works. It is successful when the administrator understands what happened, why it happened, and what to do next.</p>
    <h4 class="h5">&sect;1.2 Build Confidence</h4>
    <p>Guild CMS should leave administrators, site owners, developers, and contributors more confident than they were before they began. Confidence is a product outcome, not a cosmetic detail.</p>
    <h4 class="h5">&sect;1.3 The Installer Is an Introduction</h4>
    <p>The installer is often the first Guild CMS experience a new administrator will see. It must be treated as an introduction to Guild CMS, not merely a technical setup utility.</p>

    <h3 id="section-2" class="mt-5">Section 2 &mdash; Educational Design Principles</h3>
    <h4 class="h5">&sect;2.1 Explain Before Asking</h4>
    <p>Guild CMS should explain what information is needed, why it is needed, where it can usually be found, and what will happen after it is provided before asking the administrator to supply it.</p>
    <h4 class="h5">&sect;2.2 Teach, Do Not Assume</h4>
    <p>Guild CMS shall not assume that a first-time administrator already understands databases, PHP extensions, file permissions, sessions, HTTPS, modules, or configuration files. When knowledge is needed, the interface should teach enough to allow the person to continue.</p>
    <h4 class="h5">&sect;2.3 Documentation Complements the Software</h4>
    <p>Documentation is important, but the software itself must provide enough guidance for common tasks. Guild CMS should not rely on &ldquo;go read the documentation&rdquo; as the primary solution for ordinary installation or administration questions.</p>
    <h4 class="h5">&sect;2.4 Progressive Learning</h4>
    <p>Important information should be available when needed, without overwhelming the administrator before it is relevant. Short explanations, expandable help, and clear next steps are preferred over large blocks of disconnected reference material.</p>

    <h3 id="section-3" class="mt-5">Section 3 &mdash; Installer Experience Standard</h3>
    <p>The Guild CMS installer shall be designed as a guided onboarding experience. The planned installer flow is:</p>
    <ol>
        <li><strong>Welcome</strong> &mdash; Welcomes the administrator and explains what to expect.</li>
        <li><strong>Requirements</strong> &mdash; Checks items required for Guild CMS to run.</li>
        <li><strong>Recommended Features</strong> &mdash; Explains useful features that are not required.</li>
        <li><strong>License</strong> &mdash; Presents the license before installation actions occur.</li>
        <li><strong>Database</strong> &mdash; Collects database information and explains what it is used for.</li>
        <li><strong>Database Issues</strong> &mdash; Explains connection or permission failures when they occur.</li>
        <li><strong>Configuration</strong> &mdash; Collects information needed to generate the configuration file.</li>
        <li><strong>Administration</strong> &mdash; Creates the first administrator identity.</li>
        <li><strong>Site Settings</strong> &mdash; Defines the first site name, URL, and basic behavior.</li>
        <li><strong>Modules</strong> &mdash; Selects the initial module set.</li>
        <li><strong>Summary</strong> &mdash; Reviews what will happen and why.</li>
        <li><strong>Install</strong> &mdash; Performs the installation while showing meaningful progress.</li>
        <li><strong>Complete</strong> &mdash; Welcomes the administrator to Guild CMS and links to the site and Administration Center.</li>
    </ol>
    <p>This flow may evolve as Phase 4.4 implementation reveals new needs, but changes should preserve the same educational and confidence-building intent.</p>

    <h3 id="section-4" class="mt-5">Section 4 &mdash; System Readiness and Requirements</h3>
    <h4 class="h5">&sect;4.1 Check Required Capabilities First</h4>
    <p>If Guild CMS requires something in order to run, the administrator should know immediately. Required environment checks must occur before the administrator spends time entering configuration, database, or account information.</p>
    <h4 class="h5">&sect;4.2 Required Versus Recommended</h4>
    <p>Required checks block installation when they fail. Recommended checks explain improvements that are useful but not mandatory. Optional and recommended features should never be presented as failures.</p>
    <h4 class="h5">&sect;4.3 Use System Readiness Language</h4>
    <p>The phrase &ldquo;System Readiness&rdquo; is preferred over a purely technical &ldquo;Requirements Check&rdquo; label because it frames the installer as helping the administrator prepare their system.</p>
    <h4 class="h5">&sect;4.4 No Changes During Readiness Checks</h4>
    <p>System readiness checks should not modify the site. When a required item fails, the installer should state that nothing has been changed and explain how to correct the issue.</p>

    <h3 id="section-5" class="mt-5">Section 5 &mdash; Navigation, Save, Cancel, and Resume</h3>
    <h4 class="h5">&sect;5.1 Back Must Be Safe</h4>
    <p>If an administrator believes they made a mistake, they must be able to go back and correct earlier information before the installation is committed.</p>
    <h4 class="h5">&sect;5.2 Save Your Place</h4>
    <p>The installer should support saving progress before permanent installation actions occur. Saved state allows the administrator to pause, gather missing information, and continue later.</p>
    <h4 class="h5">&sect;5.3 Cancel Must Be Safe</h4>
    <p>Canceling before the installation phase should leave the site unchanged. The installer should explain what will be discarded and whether any saved installation state remains.</p>
    <h4 class="h5">&sect;5.4 Resume Must Be Supported</h4>
    <p>If saved installation state exists, Guild CMS should offer to resume or start over. Refreshing after correcting a system issue should recheck the condition and continue when possible.</p>

    <h3 id="section-6" class="mt-5">Section 6 &mdash; Progress and Feedback</h3>
    <h4 class="h5">&sect;6.1 Progress Must Be Meaningful</h4>
    <p>A bare &ldquo;Step 3 of 10&rdquo; indicator is not sufficient. Guild CMS should show where the administrator is, what has been completed, what remains, and what is currently happening.</p>
    <h4 class="h5">&sect;6.2 Installation Progress Must Be Visible</h4>
    <p>During the install phase, Guild CMS should show specific progress such as creating configuration, preparing database tables, creating the administrator account, installing modules, and finalizing the installation.</p>
    <h4 class="h5">&sect;6.3 Explain Delays</h4>
    <p>If a step may take time, the interface should say so. Administrators should not be left wondering whether the installer is working, stalled, or broken.</p>

    <h3 id="section-7" class="mt-5">Section 7 &mdash; Help and Contextual Guidance</h3>
    <h4 class="h5">&sect;7.1 Help Belongs on Every Page</h4>
    <p>Every installer page should include contextual help. The administrator should not need to leave the installer to understand the ordinary purpose of the current step.</p>
    <h4 class="h5">&sect;7.2 Standard Help Questions</h4>
    <p>Installer help should answer questions such as: Why am I seeing this? Where do I find this information? Why is this important? What happens next?</p>
    <h4 class="h5">&sect;7.3 Screenshots May Support Learning</h4>
    <p>Future installer pages may include screenshots or visual references where they help explain hosting panels, database fields, or administrative concepts. Screenshots are supportive aids, not replacements for clear installer text.</p>

    <h3 id="section-8" class="mt-5">Section 8 &mdash; Error and Recovery Philosophy</h3>
    <h4 class="h5">&sect;8.1 Do Not Blame the Administrator</h4>
    <p>Guild CMS should avoid language that implies the administrator failed. Messages should describe what happened and how to fix it.</p>
    <h4 class="h5">&sect;8.2 Explain Severity</h4>
    <p>If the installer cannot continue, it should explain why. If the issue is not serious enough to block installation, it should explain the risk and allow the administrator to continue.</p>
    <h4 class="h5">&sect;8.3 Teach Through Failures</h4>
    <p>Failures should become learning opportunities. A database connection failure should explain common causes such as host, username, password, database name, permissions, or server reachability.</p>
    <h4 class="h5">&sect;8.4 Reassure About State</h4>
    <p>Error messages should clearly state whether Guild CMS changed anything. When nothing has been written, say so. When a partial action occurred, explain exactly what happened and what recovery option is available.</p>

    <h3 id="section-9" class="mt-5">Section 9 &mdash; Administration Center Experience</h3>
    <p>The principles in this publication shall extend beyond the installer. The Administration Center should also explain major concepts, introduce features, clarify consequences, and help administrators succeed without forcing them to become experts immediately.</p>
    <p>Plugin installation, theme management, backups, upgrades, user management, permissions, logs, and security warnings should all be designed with the same respect for clarity, context, and recovery.</p>

    <h3 id="section-10" class="mt-5">Section 10 &mdash; Modern, Professional, and Accessible</h3>
    <h4 class="h5">&sect;10.1 Educational</h4>
    <p>Guild CMS should teach as it guides. Educational design is not decoration; it is a product requirement.</p>
    <h4 class="h5">&sect;10.2 Professional</h4>
    <p>The interface should inspire confidence through clear structure, consistent language, predictable behavior, and careful handling of risk.</p>
    <h4 class="h5">&sect;10.3 Modern</h4>
    <p>Modern design means removing unnecessary friction, using sensible defaults, validating early, recovering safely, and respecting the administrator's time. It does not require visual gimmicks.</p>
    <h4 class="h5">&sect;10.4 Accessible</h4>
    <p>Guild CMS should be usable by people with different experience levels, devices, and accessibility needs. The installer and Administration Center should favor readable text, clear contrast, keyboard-friendly navigation, responsive layout, and understandable language.</p>

    <h3 id="section-11" class="mt-5">Section 11 &mdash; Product Separation and First-Run Behavior</h3>
    <h4 class="h5">&sect;11.1 The Public Website Is Not the Installer</h4>
    <p>The Guild CMS public website explains the project, publishes documentation, and provides downloads. Executable installer logic belongs to the installable Guild CMS product, represented during development by devsite.</p>
    <h4 class="h5">&sect;11.2 The Installer Generates Configuration</h4>
    <p>The installable product should not rely on a confusing placeholder configuration file. The installer should generate <code>includes/config.inc.php</code> from information provided during installation.</p>
    <h4 class="h5">&sect;11.3 Uninstalled Sites Should Explain Themselves</h4>
    <p>If a new Guild CMS site is opened before installation is complete, <code>index.php</code> should not fail with raw PHP or database errors. It should clearly explain that Guild CMS is not installed yet and direct the administrator to the installer.</p>

    <h3 id="section-12" class="mt-5">Section 12 &mdash; Package Design Review Questions</h3>
    <p>Before user-facing work is implemented, Guild CMS packages should consider:</p>
    <ul>
        <li>What is the administrator, site owner, developer, or contributor trying to accomplish?</li>
        <li>What might they not know yet?</li>
        <li>What should Guild CMS explain before asking?</li>
        <li>What could go wrong, and how should recovery be explained?</li>
        <li>How does this feature leave the person more confident?</li>
        <li>If this were their first website, would the experience still make sense?</li>
    </ul>

    <h3 id="references" class="mt-5">Related Publications</h3>
    <ul>
        <li><strong>GCMS-ENG-001</strong> &mdash; The Guild CMS Constitution</li>
        <li><strong>GCMS-ENG-002</strong> &mdash; Vision &amp; Mission</li>
        <li><strong>GCMS-ENG-003</strong> &mdash; Engineering Principles</li>
        <li><strong>GCMS-ENG-004</strong> &mdash; Architecture Standards</li>
        <li><strong>GCMS-ENG-005</strong> &mdash; Developer Handbook</li>
        <li><strong>GCMS-ENG-008</strong> &mdash; Security Standards</li>
        <li><strong>GCMS-ENG-010</strong> &mdash; Engineering Roadmap &amp; Publication Framework</li>
    </ul>

    <div id="certification" class="guild-card-soft p-3 mt-5">
        <h3 class="h5 mb-3">Publication Certification</h3>
        <div class="engineering-meta mb-0">
            <div><strong>Publication</strong><span>GCMS-ENG-011</span></div>
            <div><strong>Title</strong><span>User Experience &amp; Educational Design Principles</span></div>
            <div><strong>Version</strong><span>1.0</span></div>
            <div><strong>Status</strong><span>Published</span></div>
            <div><strong>Approved During</strong><span>Phase 4.4</span></div>
            <div><strong>Maintained By</strong><span>Guild CMS Engineering</span></div>
        </div>
    </div>
</div>
HTML;

guildcms_engineering_publication_page($publication, $body_html);
require __DIR__ . '/../includes/footer.php';
