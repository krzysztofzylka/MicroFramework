<?php
ob_start();
session_start();

use config\Config;
use Krzysztofzylka\MicroFramework\Autoload;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\View;

include('{{vendorPath}}');

try {
    Kernel::initPaths(__DIR__ . '/../');
    new Autoload(Kernel::getProjectPath());
    Kernel::loadEnv();
    Kernel::run();
} catch (Exception $exception) {
    $view = new View();

    echo $view->renderError($exception->getCode() ?? 500, $exception);
}

new \Krzysztofzylka\MicroFramework\Debug();