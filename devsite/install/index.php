<?php
declare(strict_types=1);

if (PHP_VERSION_ID < 80200) {
    http_response_code(500);
    $version = htmlspecialchars(PHP_VERSION, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Guild CMS Installer - PHP Version Required</title><style>body{margin:0;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;background:#0f172a;color:#f9fafb}.wrap{max-width:840px;margin:0 auto;padding:3rem 1.5rem}.card{background:#1f2937;border:1px solid #374151;border-radius:18px;padding:2rem}.kicker{color:#38bdf8;text-transform:uppercase;font-weight:800;letter-spacing:.08em;font-size:.8rem}h1{font-size:clamp(2rem,5vw,3.25rem);margin:.5rem 0 1rem}.muted{color:#cbd5e1;line-height:1.65}.notice{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.4);border-radius:14px;padding:1rem;margin:1rem 0}.code{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;background:#0f172a;border:1px solid #374151;border-radius:10px;padding:.15rem .4rem}</style></head><body><main class="wrap"><section class="card"><div class="kicker">System Readiness</div><h1>Guild CMS needs a newer PHP version.</h1><p class="muted">The installer detected PHP <span class="code">' . $version . '</span>. Guild CMS requires PHP <strong>8.2 or newer</strong> for the current supported installation path.</p><div class="notice"><strong>Nothing has been changed.</strong><br><span class="muted">Update the PHP version assigned to this website, then refresh this page. The installer will continue once the server is ready.</span></div><p class="muted">On Rocky Linux or AlmaLinux, PHP is usually managed with <span class="code">dnf</span>. On Ubuntu or Debian, PHP is usually managed with <span class="code">apt</span>. If you use a hosting provider, ask them to switch this site to PHP 8.2 or newer.</p></section></main></body></html>';
    exit;
}

require_once __DIR__ . '/bootstrap.php';

use GuildCMS\Installer\Installer;
use GuildCMS\Installer\InstallerState;
use GuildCMS\Installer\Steps\AdministrationStep;
use GuildCMS\Installer\Steps\CompleteStep;
use GuildCMS\Installer\Steps\EnvironmentStep;
use GuildCMS\Installer\Steps\ConfigurationStep;
use GuildCMS\Installer\Steps\DatabaseStep;
use GuildCMS\Installer\Steps\InstallStep;
use GuildCMS\Installer\Steps\LicenseStep;
use GuildCMS\Installer\Steps\ModulesStep;
use GuildCMS\Installer\Steps\RecommendedStep;
use GuildCMS\Installer\Steps\RequirementsStep;
use GuildCMS\Installer\Steps\SiteSettingsStep;
use GuildCMS\Installer\Steps\SummaryStep;
use GuildCMS\Installer\Steps\WelcomeStep;

session_name('GUILDCMS_INSTALLER');
session_start();

$state = new InstallerState($_SESSION);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if ($action === 'save') {
    $state->save();
}

if ($action === 'cancel') {
    $state->cancel();
}

$installer = new Installer($state);
$installer->registerStep(new WelcomeStep());
$installer->registerStep(new EnvironmentStep());
$installer->registerStep(new RequirementsStep());
$installer->registerStep(new RecommendedStep());
$installer->registerStep(new LicenseStep());
$installer->registerStep(new DatabaseStep());
$installer->registerStep(new ConfigurationStep());
$installer->registerStep(new AdministrationStep());
$installer->registerStep(new SiteSettingsStep());
$installer->registerStep(new ModulesStep());
$installer->registerStep(new SummaryStep());
$installer->registerStep(new InstallStep());
$installer->registerStep(new CompleteStep());

$stepKey = filter_input(INPUT_GET, 'step', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$stepKey = is_string($stepKey) && $stepKey !== '' ? $stepKey : null;

echo $installer->render($stepKey);
