#!/usr/bin/env php
<?php
require(__DIR__ . '/../autoload.php');

$args = krzysztofzylka\SimpleLibraries\Library\Console\Args::getArgs($_SERVER['argv']);
$path = $_SERVER['PWD'];
$reflection = new \ReflectionClass(\Krzysztofzylka\MicroFramework\Kernel::class);
$microframeworkPath = dirname($reflection->getFileName());

switch ($args['args'][0]) {
    case 'init':
        krzysztofzylka\SimpleLibraries\Library\Console\Prints::print('Start init project', true);
        $path = $path . (isset($args['args'][1]) ? ('/' . $args['args'][1]) : '');

        \krzysztofzylka\SimpleLibraries\Library\File::mkdir($path);
        \krzysztofzylka\SimpleLibraries\Library\File::mkdir($path . '/public');
        \krzysztofzylka\SimpleLibraries\Library\File::copyDirectory($microframeworkPath . '/Console/resources/public', $path . '/public');
        new \Krzysztofzylka\MicroFramework\Kernel($path);

        krzysztofzylka\SimpleLibraries\Library\Console\Prints::print('Success init project', true, false, 'green');
        break;
}
?>