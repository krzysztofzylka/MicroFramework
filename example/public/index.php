<?php

use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
session_start();

include('../../vendor/autoload.php');

try {
    ob_start();
    $kernel = new \Krzysztofzylka\MicroFramework\Kernel(__DIR__ . '/..');
    $kernel->run();

    if (isset($_GET['dialogbox'])) {
        exit;
    } else {
        \Krzysztofzylka\MicroFramework\View::simpleLoad('../template/template.twig', ['content' => ob_get_clean()]);
    }
} catch (Throwable $exception) {
    \Krzysztofzylka\MicroFramework\View::renderErrorPage($exception);
}

echo DebugBar::renderHeader();
echo DebugBar::render();