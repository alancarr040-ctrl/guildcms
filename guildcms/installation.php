<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Installation';
$active_page = 'installation';
require __DIR__ . '/includes/header.php';
?>
<section class="py-5">
    <div class="container">
        <div class="guild-card p-4 mb-4">
            <div class="small text-uppercase text-info fw-semibold mb-2">Phase 4.4</div>
            <h1 class="display-5 fw-bold mb-3">Installation &amp; Bootstrap System</h1>
            <p class="lead guild-muted mb-0">Guild CMS installation work is now implemented against the devsite installable product tree. The public site documents the installer direction; the installer itself belongs to the Guild CMS product package.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="guild-card p-4 h-100">
                    <h2 class="h4">Installer framework purpose</h2>
                    <p class="guild-muted">Package 4.4.0-8 adds platform intelligence to the installer. Package 4.4.0-8a refines that intelligence into educational guidance by explaining the includes directory, PHP execution user, filesystem ownership, permissions, and technical details through progressive disclosure.</p>
                    <p class="guild-muted mb-0">Future Phase 4.4 packages will use this detected environment to provide better guidance for requirements, database setup, configuration generation, administrator creation, installer locking, and final health verification.</p>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="guild-card p-4 h-100">
                    <h2 class="h4">Framework steps</h2>
                    <ol class="guild-muted mb-0">
                        <li>Welcome</li>
                        <li>Environment Detection</li>
                        <li>System Readiness</li>
                        <li>License</li>
                        <li>Database</li>
                        <li>Configuration</li>
                        <li>Administrator</li>
                        <li>Finalize</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="guild-card p-4 mt-4">
            <h2 class="h4">Platform intelligence</h2>
            <p class="guild-muted">The installer identifies the server before it validates requirements. This lets Guild CMS explain fixes in the language of the platform, such as <code>dnf</code> guidance for Rocky and AlmaLinux or <code>apt</code> guidance for Ubuntu and Debian.</p>
            <p class="guild-muted mb-0">Detection currently covers operating system, web server, PHP runtime, loaded PHP extensions, database drivers, filesystem readiness, HTTPS, SELinux, and AppArmor.</p>
        </div>


        <div class="guild-card p-4 mt-4">
            <h2 class="h4">Installer testing and certification</h2>
            <p class="guild-muted">Guild CMS now maintains an installer testing and certification framework. The installer is validated against both a development environment and clean installation environments so it can explain missing requirements, recover from common setup issues, and avoid raw PHP failures whenever possible.</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="guild-card-soft p-3 h-100">
                        <h3 class="h5">Base expectation</h3>
                        <p class="guild-muted mb-0">Guild CMS installs on a clean supported environment and clearly explains any issue that prevents installation from continuing.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="guild-card-soft p-3 h-100">
                        <h3 class="h5">Perfect expectation</h3>
                        <p class="guild-muted mb-0">Guild CMS guides, teaches, diagnoses, recovers, resumes, and completes installation across validated environments.</p>
                    </div>
                </div>
            </div>
            <p class="guild-muted mt-3 mb-0">Current certification planning targets minimal Rocky Linux 9, AlmaLinux 9, Ubuntu Server 24.04, and Debian 12 environments installed through native package managers such as dnf or apt. Hosting panels may be compatible, but Guild CMS certifies operating system environments rather than paid control panels.</p>
        </div>

        <div class="guild-card p-4 mt-4">
            <h2 class="h4">Environment roles</h2>
            <p class="guild-muted mb-0"><strong>Development Center</strong> tracks engineering progress, <strong>guildcms</strong> documents the public project, and <strong>devsite</strong> is the installable Guild CMS product tree used to validate the installer and future reference installations.</p>
        </div>
    </div>

        <div class="guild-card p-4 mt-4">
            <h2 class="h4">Educational environment details</h2>
            <p class="guild-muted mb-0">Environment Detection now explains filesystem results before showing raw paths. The installer reports the effective PHP user and group, the owner and permissions of the includes directory, and why those details matter when Guild CMS later creates <code>includes/config.inc.php</code>.</p>
        </div>

</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
