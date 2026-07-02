<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/library.php';

$publication = guildcms_engineering_find('installer-certification-milestone-1.php');
$page_title = 'Installer Certification Milestone 1';
$active_page = 'engineering';
require __DIR__ . '/../includes/header.php';

ob_start();
?>
<p class="lead">Installer Certification Milestone 1 publishes the first cross-platform certification record for Guild CMS Installer 4.4.0-8a. The milestone proves that the installer can identify, explain, and complete installation across representative foundation platforms.</p>

<div class="guild-card-soft p-3 mb-4">
    <h3 class="h5">Milestone Summary</h3>
    <p class="mb-0">Guild CMS Installer 4.4.0-8a has completed certification across the RHEL and Debian Linux families, including a Virtualmin hosting-panel deployment and a diagnostic validation scenario.</p>
</div>

<h3 class="h4 mt-4">Certified Platforms</h3>
<div class="table-responsive mb-4">
    <table class="table table-dark table-sm align-middle">
        <thead><tr><th>Platform</th><th>Certification Scenario</th><th>Result</th><th>Key Observation</th></tr></thead>
        <tbody>
            <tr><td>Rocky Linux 9.8 + Virtualmin</td><td>Fresh hosting-panel deployment</td><td>PASS</td><td>Virtualmin document root, PHP-FPM user, HTTPS, and configuration paths detected correctly.</td></tr>
            <tr><td>Rocky Linux 9.8 Minimal</td><td>Fresh RHEL-family minimal installation</td><td>PASS</td><td>Requires PHP stream upgrade, mod_ssl, and SELinux policy adjustment.</td></tr>
            <tr><td>AlmaLinux 9.8 Minimal</td><td>Fresh RHEL-family minimal installation</td><td>PASS</td><td>Substantially equivalent to Rocky Minimal with the same SELinux and PHP stream considerations.</td></tr>
            <tr><td>Ubuntu 24 Minimal</td><td>Fresh Ubuntu LTS installation</td><td>PASS</td><td>Straightforward installation with PHP 8.3 available by default.</td></tr>
            <tr><td>Debian 12 Minimal</td><td>Diagnostic validation</td><td>PASS</td><td>Installer correctly detected intentionally omitted recommended PHP extensions.</td></tr>
        </tbody>
    </table>
</div>

<h3 class="h4 mt-4">Milestone Evidence</h3>
<p>The following plain-text certification reports are included with the Phase 4.4.0-9 documentation package:</p>
<ul>
    <li><code>installer-cert-rocky9-virtualmin-4.4.0-8a.txt</code></li>
    <li><code>installer-cert-rocky9-minimal-4.4.0-8a.txt</code></li>
    <li><code>installer-cert-almalinux9-4.4.0-8a.txt</code></li>
    <li><code>installer-cert-ubuntu24-4.4.0-8a.txt</code></li>
    <li><code>installer-cert-debian12-4.4.0-8a.txt</code></li>
    <li><code>installer-cert-template-4.4.0-8a.txt</code></li>
</ul>

<h3 class="h4 mt-4">Certification Conclusions</h3>
<ul>
    <li>The installer successfully detects distribution, package manager, document root, PHP runtime, PHP configuration, database support, HTTPS state, and available security framework information.</li>
    <li>RHEL-family minimal installations require additional platform preparation for PHP 8.3, Apache SSL support, administrative utilities, and SELinux write contexts.</li>
    <li>Debian-family installations provide a comparatively straightforward Apache/PHP setup, with AppArmor detected where applicable.</li>
    <li>The installer correctly distinguishes required readiness checks from recommended feature checks.</li>
    <li>The standardized certification report format is suitable for future certification milestones.</li>
</ul>

<h3 class="h4 mt-4">Next Certification Milestones</h3>
<ul>
    <li><strong>Milestone 2:</strong> Nginx + PHP-FPM certification on Ubuntu/Debian and Rocky Linux.</li>
    <li><strong>Milestone 3:</strong> Docker-based regression and certification environments.</li>
    <li><strong>Future:</strong> Podman, reverse proxy deployments, and additional deployment models when they introduce meaningful new risk.</li>
</ul>
<?php
$body = ob_get_clean();
guildcms_engineering_publication_page($publication, $body);
require __DIR__ . '/../includes/footer.php';
