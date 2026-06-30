<?php
/**
 * Guild CMS Installer Architecture
 * Package 4.4.0-4
 */

$devCenterData = __DIR__ . '/../data/development_center_data.php';
if (is_readable($devCenterData)) {
    require $devCenterData;
}

if (!function_exists('guildcms_installer_h')) {
    function guildcms_installer_h($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$installerSteps = [
    'Welcome',
    'Environment Detection',
    'System Readiness',
    'Recommended Features',
    'License',
    'Database',
    'Configuration',
    'Administration',
    'Site Settings',
    'Modules',
    'Summary',
    'Install',
    'Complete',
];
?>

<section class="dev-center-section">
    <h1>Installer Architecture</h1>
    <p class="lead">Phase 4.4 now includes platform intelligence so the installer can identify the server environment before validating requirements or asking configuration questions.</p>

    <div class="card bg-dark text-light border-info mb-4">
        <div class="card-body">
            <div class="small text-info text-uppercase fw-semibold mb-2">Phase 4.4 - Installation &amp; Bootstrap System</div>
            <h2 class="h4">Installer Environment Detection</h2>
            <p class="mb-0">Package 4.4.0-8 adds operating system, web server, PHP, database driver, filesystem, HTTPS, SELinux, and AppArmor detection so later installer steps can give accurate platform-specific guidance. Package 4.4.0-8a refines that detection into an educational report with PHP user, group, ownership, permissions, and progressive technical details.</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card bg-dark text-light border-secondary h-100">
                <div class="card-header">Installer Experience Principles</div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Explain before asking.</li>
                        <li>Detect the platform before validating requirements.</li>
                        <li>Check required environment needs early.</li>
                        <li>Separate required checks from recommended features.</li>
                        <li>Allow back, save, cancel, refresh, and resume.</li>
                        <li>Do not write permanent changes until the Install step.</li>
                        <li>Errors should teach, reassure, and explain recovery.</li>
                        <li>Filesystem and permission details should explain who PHP is running as and where files live.</li>
                        <li>Technical information should remain available through progressive disclosure.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card bg-dark text-light border-secondary h-100">
                <div class="card-header">Installer Step Model</div>
                <div class="card-body">
                    <ol class="mb-0">
                        <?php foreach ($installerSteps as $step): ?>
                            <li><?= guildcms_installer_h($step) ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-dark text-light border-secondary mt-4">
        <div class="card-header">Product Separation Boundary</div>
        <div class="card-body">
            <p class="mb-0">The public Guild CMS site documents the installer. The Development Center records the engineering and testing standards. The devsite tree contains the executable installer and is the installable product baseline.</p>
        </div>
    </div>
</section>
