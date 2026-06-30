<?php
/**
 * Guild CMS Installer Architecture
 * Package 4.4.0-1
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

$installerArchitecture = $guildcmsInstallerArchitecture ?? [
    'package' => '4.4.0-1',
    'title' => 'Installation Architecture',
    'status' => 'Defined',
    'summary' => 'Installer architecture metadata is unavailable.',
    'principles' => [],
    'stages' => [],
];
?>

<section class="dev-center-section">
    <h1>Installer Architecture</h1>
    <p class="lead">Package <?= guildcms_installer_h($installerArchitecture['package'] ?? '') ?> defines the installation architecture for Guild CMS before executable installer workflows are built.</p>

    <div class="card bg-dark text-light border-info mb-4">
        <div class="card-body">
            <div class="small text-info text-uppercase fw-semibold mb-2">Phase 4.4 - Installation &amp; Bootstrap System</div>
            <h2 class="h4"><?= guildcms_installer_h($installerArchitecture['title'] ?? '') ?></h2>
            <p class="mb-0"><?= guildcms_installer_h($installerArchitecture['summary'] ?? '') ?></p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card bg-dark text-light border-secondary h-100">
                <div class="card-header">Architecture Principles</div>
                <div class="card-body">
                    <ul class="mb-0">
                        <?php foreach (($installerArchitecture['principles'] ?? []) as $principle): ?>
                            <li><?= guildcms_installer_h($principle) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card bg-dark text-light border-secondary h-100">
                <div class="card-header">Installer Stage Model</div>
                <div class="card-body">
                    <ol class="mb-0">
                        <?php foreach (($installerArchitecture['stages'] ?? []) as $stage): ?>
                            <li><?= guildcms_installer_h($stage) ?></li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-dark text-light border-secondary mt-4">
        <div class="card-header">Boundary Decision</div>
        <div class="card-body">
            <p class="mb-0">The Phase 4.4 installer targets the reusable Guild CMS product. The public Guild CMS information site remains a documentation and branding reference and is not the install target.</p>
        </div>
    </div>
</section>
