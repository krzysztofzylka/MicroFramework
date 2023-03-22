<?php

namespace Krzysztofzylka\MicroFramework\bin\Action;

use Krzysztofzylka\MicroFramework\bin\Console\Console;
use Krzysztofzylka\MicroFramework\bin\Trait\Database;
use Krzysztofzylka\MicroFramework\bin\Trait\Prints;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;

class User {

    use Prints;
    use Database;

    /**
     * Users
     * @param Console $console
     */
    public function __construct(Console $console)
    {
        $this->console = $console;
        $userAction = $console->arg[2];

        if (!in_array($userAction, ['create'])) {
            $this->dprint('Action not found');

            exit;
        } elseif (!isset($console->arg[3]) || !isset($console->arg[4])) {
            $this->dprint('Login and password is required');

            exit;
        }

        $this->databaseConnect($console->path);

        $account = new Account();

        try {
            $account->registerUser($console->arg[3], $console->arg[4]);

            if (isset($console->arg[5]) && $console->arg[5]) {
                Account::$tableInstance->updateValue('admin', 5);
                $this->print('User has admin permission!');
            }
        } catch (\Exception $exception) {
            $this->dprint('Fail: ' . $exception->getMessage());

            exit;
        }

        $this->dprint('Success');
    }

}