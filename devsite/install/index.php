<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use GuildCMS\Installer\Installer;
use GuildCMS\Installer\InstallerState;
use GuildCMS\Installer\Steps\AdministratorStep;
use GuildCMS\Installer\Steps\ConfigurationStep;
use GuildCMS\Installer\Steps\DatabaseStep;
use GuildCMS\Installer\Steps\FinalizeStep;
use GuildCMS\Installer\Steps\LicenseStep;
use GuildCMS\Installer\Steps\RequirementsStep;
use GuildCMS\Installer\Steps\WelcomeStep;

session_name('GUILDCMS_INSTALLER');
session_start();

$installer = new Installer(new InstallerState($_SESSION));
$installer->registerStep(new WelcomeStep());
$installer->registerStep(new RequirementsStep());
$installer->registerStep(new LicenseStep());
$installer->registerStep(new DatabaseStep());
$installer->registerStep(new ConfigurationStep());
$installer->registerStep(new AdministratorStep());
$installer->registerStep(new FinalizeStep());

$stepKey = filter_input(INPUT_GET, 'step', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$stepKey = is_string($stepKey) && $stepKey !== '' ? $stepKey : null;

echo $installer->render($stepKey);
