<?php

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