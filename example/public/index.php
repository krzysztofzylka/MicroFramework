<?php

use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;

ob_start();
session_start();

include('../../vendor/autoload.php');

try {
    $kernel = new \Krzysztofzylka\MicroFramework\Kernel(__DIR__ . '/..');
    $kernel->run();
} catch (Throwable $exception) {
    echo $exception->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <?= DebugBar::renderHeader() ?>
</head>
<body>
    ...
    <?= DebugBar::render() ?>
</body>
</html>
