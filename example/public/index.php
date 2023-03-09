<?php
ob_start();
session_start();
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use config\Config;
use krzysztofzylka\DatabaseManager\Debug;
use Krzysztofzylka\MicroFramework\Autoload;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\View;

include('../../vendor/autoload.php');

try {
    Kernel::create(__DIR__ . '/../');
    new Autoload(Kernel::getProjectPath());
    Kernel::setConfig(new Config());
    Kernel::run();
} catch (Exception $exception) {
    $view = new View();

    echo $view->renderError($exception->getCode() ?? 500, $exception);
}

if (Kernel::getConfig()->debug) {
    Debug::showDebugModal();
}