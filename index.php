<?php
namespace Yauc;

define('APP_ROOT', __DIR__);
define('VIEW_DIR', APP_ROOT.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR);

require APP_ROOT.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

$locator = ServiceLocator::instance();
$locator->registerService('config', "Yauc\\ConfigManager");
$locator->registerService('route', "Yauc\\Router");
$locator->registerService('clients', "Yauc\\ClientRepository");
$locator->registerService('users', "Yauc\\UserRepository");
$locator->registerService('discuz', "Yauc\\DiscuzConnector");
$locator->registerService('token', "Yauc\\TokenManager", array('sso', 3600, '/'));
$locator->registerService('smarty', "\\Smarty");

$route = $locator->getService('route');

$controller = Controller\Base::getController($route);
if (is_object($controller))
{
	$controller->run();
}