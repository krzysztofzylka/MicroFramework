<?php

use Krzysztofzylka\MicroFramework\bin\Console\Console;

if (file_exists(__DIR__ . '/../../../../autoload.php')) {
    include(__DIR__ . '/../../../../autoload.php');
} elseif (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    include(__DIR__ . '/../../vendor/autoload.php');
}

new Console($argv);