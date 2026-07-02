<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('installer-testing-certification.php');
$page_title = 'Installer Testing & Certification Framework';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<p class="lead">GCMS-ENG-012 defines how Guild CMS validates the installation experience. The installer is not considered successful merely because code executes. It must also explain, guide, diagnose, recover, and help the administrator complete installation with confidence.</p>

<div class="guild-card-soft p-3 mb-4">
    <h3 class="h5">Revision History</h3>
    <div class="table-responsive">
        <table class="table table-dark table-sm align-middle mb-0">
            <thead><tr><th>Version</th><th>Date</th><th>Description</th></tr></thead>
            <tbody>
                <tr><td>1.0</td><td>June 2026</td><td>Initial installer testing and certification framework.</td></tr>
                <tr><td>1.1</td><td>July 2026</td><td>Updated to reference Installer Certification Milestone 1 and standardized certification reports.</td></tr>
            </tbody>
        </table>
    </div>
</div>

<h3 class="h4 mt-4">Certification Philosophy</h3>
<p>Guild CMS tests both the software and the experience. Every failure discovered during installer testing should become guidance for the next administrator. The installer must avoid raw PHP failures wherever possible and should explain what happened, why it matters, what has or has not changed, and what to do next.</p>

<h3 class="h4 mt-4">Runtime Support Policy</h3>
<p>Guild CMS distinguishes between compatibility, support, and certification. The installer should evaluate capabilities first, while still warning when a runtime is older than the supported baseline or newer than the latest certified target.</p>
<ul>
    <li><strong>PHP 8.0:</strong> Unsupported. The installer must show a friendly minimum-version message before incompatible framework code loads.</li>
    <li><strong>PHP 8.1:</strong> Compatible but not part of the current official certification target.</li>
    <li><strong>PHP 8.2:</strong> Supported minimum runtime for the current installer development cycle.</li>
    <li><strong>PHP 8.3+:</strong> Recommended modern runtime target for certification environments.</li>
    <li><strong>Future PHP versions:</strong> Expected compatible when required capabilities remain available, but not certified until tested.</li>
</ul>

<h3 class="h4 mt-4">Required Installer Test Areas</h3>
<ul>
    <li>Fresh installation with no configuration file.</li>
    <li>Unsupported PHP version handling.</li>
    <li>Required and recommended PHP extension detection.</li>
    <li>Writable and unwritable configuration target behavior.</li>
    <li>Database availability and connection failure handling.</li>
    <li>Configuration generation and safe file handling.</li>
    <li>Administrator account creation and initial site setup.</li>
    <li>Completion, lockout, recovery, and recheck behavior.</li>
</ul>

<h3 class="h4 mt-4">Installer Certification Milestone 1</h3>
<p>Package 4.4.0-9 publishes the first installer certification milestone for Guild CMS Installer 4.4.0-8a. The milestone validates the foundation platforms across RHEL-family and Debian-family systems, plus a Virtualmin hosting-panel deployment scenario.</p>
<div class="table-responsive mb-4">
    <table class="table table-dark table-sm align-middle">
        <thead><tr><th>Platform</th><th>Scenario</th><th>Result</th></tr></thead>
        <tbody>
            <tr><td>Rocky Linux 9.8 + Virtualmin</td><td>Fresh hosting-panel deployment</td><td>PASS</td></tr>
            <tr><td>Rocky Linux 9.8 Minimal</td><td>Fresh RHEL-family minimal installation</td><td>PASS</td></tr>
            <tr><td>AlmaLinux 9.8 Minimal</td><td>Fresh RHEL-family minimal installation</td><td>PASS</td></tr>
            <tr><td>Ubuntu 24 Minimal</td><td>Fresh Debian-family LTS installation</td><td>PASS</td></tr>
            <tr><td>Debian 12 Minimal</td><td>Fresh installation with diagnostic validation</td><td>PASS</td></tr>
        </tbody>
    </table>
</div>

<h3 class="h4 mt-4">Certification Report Format</h3>
<p>Certification reports are plain-text engineering records. Each report records the test environment, platform preparation, installer observations, detected runtime, filesystem and security context, installer accuracy, certification result, lessons learned, and scorecard.</p>
<p>The reports are evidence, not marketing copy. They document what Guild CMS detected, why the result matters, and what was required to complete installation.</p>

<h3 class="h4 mt-4">Related Certification Evidence</h3>
<ul>
    <li>Installer Certification Milestone 1 – Foundation Platforms.</li>
    <li>Rocky Linux 9.8 + Virtualmin certification report.</li>
    <li>Rocky Linux 9.8 Minimal certification report.</li>
    <li>AlmaLinux 9.8 Minimal certification report.</li>
    <li>Ubuntu 24 Minimal certification report.</li>
    <li>Debian 12 Minimal diagnostic certification report.</li>
</ul>

<h3 class="h4 mt-4">Future Certification Targets</h3>
<ul>
    <li>Nginx + PHP-FPM on Ubuntu/Debian.</li>
    <li>Nginx + PHP-FPM on Rocky Linux.</li>
    <li>Docker-based regression environments.</li>
    <li>Container and reverse-proxy deployment scenarios.</li>
</ul>
<?php
$body = ob_get_clean();
guildcms_engineering_publication_page($publication, $body);
require __DIR__ . '/../includes/footer.php';
