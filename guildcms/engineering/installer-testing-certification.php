<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('installer-testing-certification.php');
$page_title = 'Installer Testing & Certification Framework';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

$body_html = <<<'HTML'
<div class="engineering-publication-body">
    <p class="lead">Installer Testing &amp; Certification Framework defines how Guild CMS validates the installation experience across supported environments, failure cases, accessibility expectations, and release certification checkpoints.</p>

    <div class="guild-card-soft p-3 mb-4">
        <h3 class="h5 mb-3">Revision History</h3>
        <div class="table-responsive">
            <table class="table table-dark table-sm align-middle mb-0">
                <thead>
                    <tr><th>Version</th><th>Date</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td>1.0</td><td>June 2026</td><td>Initial publication defining installer testing, runtime support, environment certification, and validation expectations for Phase 4.4.</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="guild-card-soft p-3 mb-4">
        <h3 class="h5 mb-3">Table of Contents</h3>
        <ol class="mb-0">
            <li><a href="#purpose" class="link-light">Purpose</a></li>
            <li><a href="#philosophy" class="link-light">Testing Philosophy</a></li>
            <li><a href="#runtime" class="link-light">Runtime Support Policy</a></li>
            <li><a href="#environments" class="link-light">Certification Environments</a></li>
            <li><a href="#expectations" class="link-light">Base and Perfect Expectations</a></li>
            <li><a href="#matrix" class="link-light">Installer Test Matrix</a></li>
            <li><a href="#failure" class="link-light">Failure and Recovery Testing</a></li>
            <li><a href="#ux" class="link-light">User Experience Validation</a></li>
            <li><a href="#security" class="link-light">Security Validation</a></li>
            <li><a href="#release" class="link-light">Release Certification Checklist</a></li>
            <li><a href="#records" class="link-light">Certification Records</a></li>
        </ol>
    </div>

    <h3 id="purpose" class="mt-5">Purpose</h3>
    <p>Guild CMS installation testing must validate more than whether the code executes. It must validate whether the installer helps a site owner understand what is happening, diagnose problems, recover safely, and complete installation with confidence.</p>
    <p>This publication establishes the certification framework used during Phase 4.4 and future installer releases. It defines the environments, expectations, tests, and reporting standards required before Guild CMS can be considered installation-ready.</p>

    <h3 id="philosophy" class="mt-5">Testing Philosophy</h3>
    <p>Guild CMS tests the software and the experience. A successful installer should not only pass technical checks; it should explain the checks, guide the administrator through any problems, and avoid raw PHP errors whenever possible.</p>
    <p>Every installer issue discovered during development should be treated as an opportunity to improve the next administrator's experience.</p>

    <h3 id="runtime" class="mt-5">Runtime Support Policy</h3>
    <p>Guild CMS distinguishes between compatible, supported, and certified runtimes.</p>
    <div class="table-responsive">
        <table class="table table-dark table-sm align-middle">
            <thead><tr><th>Runtime</th><th>Status</th><th>Meaning</th></tr></thead>
            <tbody>
                <tr><td>PHP 8.0</td><td><span class="badge bg-danger">Unsupported</span></td><td>The installer must show a friendly minimum-version message rather than loading incompatible code.</td></tr>
                <tr><td>PHP 8.1</td><td><span class="badge bg-warning text-dark">Compatible</span></td><td>The framework may run, but it is not part of the official certification target because it is outside the preferred support window.</td></tr>
                <tr><td>PHP 8.2</td><td><span class="badge bg-success">Supported</span></td><td>Minimum supported PHP runtime for the current Guild CMS development cycle.</td></tr>
                <tr><td>PHP 8.3+</td><td><span class="badge bg-success">Supported</span></td><td>Recommended modern runtime target as certification environments are added.</td></tr>
            </tbody>
        </table>
    </div>
    <p>The installer bootstrap must be conservative enough to detect unsupported PHP versions before loading newer framework code. Guild CMS cannot teach the administrator why PHP is too old if the installer crashes before the version check runs.</p>

    <h3 id="environments" class="mt-5">Certification Environments</h3>
    <p>Guild CMS certifies runtime environments rather than paid hosting control panels. Control panels such as Virtualmin, cPanel, Plesk, DirectAdmin, Hestia, or ISPConfig are provisioning tools. The installer validates the environment they create: web server, PHP runtime, extensions, filesystem access, and database connectivity.</p>
    <div class="table-responsive">
        <table class="table table-dark table-sm align-middle">
            <thead><tr><th>Tier</th><th>Environment</th><th>Purpose</th></tr></thead>
            <tbody>
                <tr><td>Tier 1</td><td>Rocky Linux 9, Virtualmin, Apache, PHP-FPM, MariaDB, PHP 8.2+</td><td>Primary development and clean-install certification environment.</td></tr>
                <tr><td>Tier 1</td><td>Ubuntu Server 24.04 LTS, Apache, PHP-FPM, MariaDB, PHP 8.2+</td><td>Cross-platform certification target for common VPS deployments.</td></tr>
                <tr><td>Tier 2</td><td>AlmaLinux 9</td><td>RHEL-family compatibility validation.</td></tr>
                <tr><td>Tier 2</td><td>Debian 12</td><td>Debian-family compatibility validation.</td></tr>
                <tr><td>Tier 3</td><td>Nginx and containerized deployments</td><td>Later certification targets for alternate web server and repeatable installation testing.</td></tr>
            </tbody>
        </table>
    </div>

    <h3 id="expectations" class="mt-5">Base and Perfect Expectations</h3>
    <h4 class="h5 mt-4">Base Expectation</h4>
    <p>The installer works correctly on a clean supported environment, never fails with raw PHP errors, and clearly explains any required issue that prevents installation from continuing.</p>
    <h4 class="h5 mt-4">Perfect Expectation</h4>
    <p>The installer guides, teaches, diagnoses, recovers, resumes, and completes installation across multiple validated environments while preserving a professional, modern, accessible, and educational experience.</p>

    <h3 id="matrix" class="mt-5">Installer Test Matrix</h3>
    <ul>
        <li>Fresh installation with no configuration file.</li>
        <li>Unsupported PHP version.</li>
        <li>Supported PHP version with required extensions present.</li>
        <li>Missing required PHP extension.</li>
        <li>Missing recommended feature.</li>
        <li>Unwritable configuration target.</li>
        <li>Incorrect database host, name, username, or password.</li>
        <li>Successful database connection.</li>
        <li>Configuration generation.</li>
        <li>Administrator account creation.</li>
        <li>Module selection and installation.</li>
        <li>Installation progress screen.</li>
        <li>Completion screen with links to site and Administration Center.</li>
    </ul>

    <h3 id="failure" class="mt-5">Failure and Recovery Testing</h3>
    <p>Failure tests are first-class installer tests. Guild CMS should explain what happened, why it matters, what has or has not been changed, and how to continue.</p>
    <ul>
        <li>Refresh during each installer step.</li>
        <li>Close browser after saving progress.</li>
        <li>Resume a saved installation.</li>
        <li>Start over from a saved installation.</li>
        <li>Cancel before permanent changes are written.</li>
        <li>Recheck after correcting a server issue.</li>
        <li>Recover from partial write failures during later install phases.</li>
    </ul>

    <h3 id="ux" class="mt-5">User Experience Validation</h3>
    <p>Installer certification must verify that each screen follows the principles from GCMS-ENG-011. Every page should explain before asking, provide contextual help, avoid blaming the administrator, and make clear what happens next.</p>
    <p>Progress indicators should be meaningful. A person should never see only a vague step number when Guild CMS can explain what it is checking or changing.</p>

    <h3 id="security" class="mt-5">Security Validation</h3>
    <p>Installer security testing must include configuration file handling, secret exposure, session state, CSRF protections for later POST steps, filesystem permissions, installer lockout/removal, and safe handling of failure states.</p>
    <p>No installer test is complete until it verifies that sensitive values are not displayed, logged, committed, or left in world-readable artifacts.</p>

    <h3 id="release" class="mt-5">Release Certification Checklist</h3>
    <ul>
        <li>Development environment smoke test completed.</li>
        <li>Clean Rocky Linux / Virtualmin installation test completed.</li>
        <li>Cross-platform Ubuntu installation test completed when available.</li>
        <li>Required checks pass or block with educational guidance.</li>
        <li>Recommended checks warn without blocking.</li>
        <li>Save, resume, cancel, back, refresh, and recheck behavior verified.</li>
        <li>Accessibility review completed for installer pages.</li>
        <li>Security review completed for modified installer behavior.</li>
    </ul>

    <h3 id="records" class="mt-5">Certification Records</h3>
    <p>The Development Center should record installer certification results by package, environment, PHP version, database, result, and known issues. Future Phase 4.5 data normalization work should formalize these records into a structured certification dashboard.</p>
</div>
HTML;

guildcms_engineering_publication_page($publication, $body_html);

require __DIR__ . '/../includes/footer.php';
