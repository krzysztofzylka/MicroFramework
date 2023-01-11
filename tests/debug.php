<?php

use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\View;

include('../vendor/autoload.php');

try {
    Kernel::create(__DIR__);
    Kernel::autoload();
    Kernel::init('test', 'index', ['a']);

    echo (new View())->render('test', ['debug' => true]);
} catch (Exception $e) {
    var_dump($e);
}