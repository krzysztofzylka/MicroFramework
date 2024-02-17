<?php

use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\View;

ob_start();
session_start();

for ($i=1; $i<=10; $i++) {
    $path = str_repeat('../', $i) . 'vendor/autoload.php';

    if (file_exists($path)) {
        require($path);

        break;
    }
}

try {
    $kernel = new \Krzysztofzylka\MicroFramework\Kernel(__DIR__ . '/..');
    $kernel->run();
} catch (Throwable $exception) {
    DebugBar::addThrowable($exception);
    View::renderErrorPage($exception);
}