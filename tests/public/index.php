<?php

use Krzysztofzylka\MicroFramework\Kernel;

include('../../vendor/autoload.php');

try {
    Kernel::create(__DIR__ . '/../');
    Kernel::autoload();
    Kernel::init('index', 'index');
} catch (Exception $e) {
    var_dump($e);
}