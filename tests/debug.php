<?php

use krzysztofzylka\DatabaseManager\DatabaseConnect;
use krzysztofzylka\DatabaseManager\Enum\DatabaseType;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\View;

include('../vendor/autoload.php');

try {
    Kernel::create(__DIR__);
    Kernel::databaseConnect(
        (new DatabaseConnect())
            ->setType(DatabaseType::mysql)
            ->setUsername('root')
            ->setPassword('123123qwe')
            ->setDatabaseName('microframework')
    );
    Kernel::autoload();
    Kernel::init('test', 'index', ['a']);

    echo (new View())->render('test', ['debug' => true]);
} catch (Exception $e) {
    var_dump($e);
}