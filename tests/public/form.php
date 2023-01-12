<?php

use krzysztofzylka\DatabaseManager\DatabaseConnect;
use krzysztofzylka\DatabaseManager\Enum\DatabaseType;
use Krzysztofzylka\MicroFramework\Kernel;

include('../../vendor/autoload.php');

try {
    Kernel::create(__DIR__ . '/../');
    Kernel::databaseConnect(
        (new DatabaseConnect())
            ->setType(DatabaseType::mysql)
            ->setUsername('root')
            ->setPassword('123123qwe')
            ->setDatabaseName('microframework')
    );
    Kernel::autoload();
    Kernel::init('form', 'test');
} catch (Exception $e) {
    var_dump($e);
}