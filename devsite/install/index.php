<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use GuildCMS\Installer\Installer;
use GuildCMS\Installer\InstallerState;
use GuildCMS\Installer\Steps\AdministrationStep;
use GuildCMS\Installer\Steps\CompleteStep;
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
