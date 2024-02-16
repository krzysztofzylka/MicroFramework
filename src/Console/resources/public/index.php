<?php

use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\View;

ob_start();
session_start();

include('../vendor/autoload.php');

try {
    $kernel = new \Krzysztofzylka\MicroFramework\Kernel(__DIR__ . '/..');
    $kernel->run();
} catch (Throwable $exception) {
    DebugBar::addThrowable($exception);

    View::renderErrorPage($exception);
}

echo DebugBar::renderHeader();
echo DebugBar::render();