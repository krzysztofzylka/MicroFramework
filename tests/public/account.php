<?php
session_start();

use Krzysztofzylka\MicroFramework\Extension\Account;
use Krzysztofzylka\MicroFramework\Kernel;

include('../../vendor/autoload.php');

try {
    Kernel::create(__DIR__ . '/../');
    Kernel::databaseConnect(
        (new \krzysztofzylka\DatabaseManager\DatabaseConnect())
            ->setType(\krzysztofzylka\DatabaseManager\Enum\DatabaseType::mysql)
            ->setUsername('root')
            ->setPassword('123123qwe')
            ->setDatabaseName('microframework')
    );
    Kernel::autoload();

    $account = new Account();

    try {
        $account->install();
    } catch (Exception) {
    }

//    var_dump($account->registerUser('kylu311', '123123qwe'));
    var_dump($account->login('kylu311', '123123qwe'));

    if (Account::isLogged()) {
        var_dump(Account::$account);
    }

    $account->logout();

    if (Account::isLogged()) {
        var_dump(Account::$account);
    }

    Kernel::init('test', 'view');
} catch (Exception $e) {
    var_dump($e);
}