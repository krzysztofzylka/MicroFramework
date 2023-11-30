<?php

use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
session_start();

include('../../vendor/autoload.php');

try {
    $kernel = new \Krzysztofzylka\MicroFramework\Kernel(__DIR__ . '/..');
    $kernel->run();
} catch (Throwable $exception) {
    die($exception);
}

echo DebugBar::renderHeader();
echo DebugBar::render();