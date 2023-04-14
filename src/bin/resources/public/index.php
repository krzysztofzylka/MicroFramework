<?php
ob_start();
session_start();

use config\Config;
use krzysztofzylka\DatabaseManager\Debug;
use Krzysztofzylka\MicroFramework\Autoload;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\View;
use krzysztofzylka\SimpleLibraries\Library\Request;

include('{{vendorPath}}');

try {
    Kernel::initPaths(__DIR__ . '/../');
    new Autoload(Kernel::getProjectPath());
    Kernel::setConfig(new Config());
    Kernel::run();
} catch (Exception $exception) {
    $view = new View();

    echo $view->renderError($exception->getCode() ?? 500, $exception);
}

if (Kernel::getConfig()->debug) {
    echo (new \Krzysztofzylka\MicroFramework\Extension\Debug\Debug())->render();
}