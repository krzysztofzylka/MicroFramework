<?php
ob_start();
session_start();

use Krzysztofzylka\MicroFramework\Autoload;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\View;

include('/home/krzysztof/PhpstormProjects/MicroFramework/vendor/autoload.php');

try {
    Kernel::initPaths(__DIR__ . '/../');
    new Autoload(Kernel::getProjectPath());
    Kernel::loadEnv();
    Kernel::run();
} catch (Exception $exception) {
    $view = new View();

    echo $view->renderError($exception->getCode() ?? 500, $exception);
}

if ($_ENV['config_debug']) {
    echo (new \Krzysztofzylka\MicroFramework\Extension\Debug\Debug())->render();
}