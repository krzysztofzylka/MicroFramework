<?php

use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;

ob_start();
session_start();

include('../../vendor/autoload.php');

try {
    $kernel = new \Krzysztofzylka\MicroFramework\Kernel(__DIR__ . '/..');
    $kernel->run();
} catch (Throwable $exception) {
    ob_clean();
    die($exception->getMessage());
}

echo DebugBar::renderHeader();
echo DebugBar::render();