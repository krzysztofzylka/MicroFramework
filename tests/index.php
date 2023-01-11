<?php

include('../vendor/autoload.php');

try {
    \Krzysztofzylka\MicroFramework\Kernel::create(__DIR__);
    \Krzysztofzylka\MicroFramework\Kernel::autoload();
    \Krzysztofzylka\MicroFramework\Kernel::init('test', 'index', ['a']);
} catch (Exception $e) {
    var_dump($e);
}