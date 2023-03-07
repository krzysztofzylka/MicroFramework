<?php
ob_start();
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use config\Config;
use Krzysztofzylka\MicroFramework\Kernel;

include('../../vendor/autoload.php');

try {
    Kernel::create(__DIR__ . '/../');
    Kernel::autoload();
    Kernel::setConfig(new Config());
    Kernel::run();
} catch (Exception $e) {
    var_dump($e);
}