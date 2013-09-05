<?php

define('APP_ROOT', __DIR__);
define('VIEW_DIR', APP_ROOT . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR);

require APP_ROOT . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

$locator = new \Yauc\ServiceLocator();
$locator->registerService('route', "\\Yauc\\Router");
$locator->registerService('clients', "\\Yauc\\ClientRepository");

$route = $locator->getService('route');

$controller = \Yauc\Controller\Base::getController($route, $locator);
$controller->run();
