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

        switch ($console->arg[2] ?? false) {
            case 'create':
                $this->create();

                break;
            default:
                $this->dprint('Action not found');

                break;
        }
    }

    /**
     * Create user
     * @return void
     * @throws \Krzysztofzylka\MicroFramework\Exception\NotFoundException
     * @throws \krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException
     */
    private function create() {
        if (!isset($this->console->arg[3]) || !isset($this->console->arg[4])) {
            $this->dprint('Login and password is required');

            exit;
        }

        $this->databaseConnect($this->console->path);

        $account = new Account();

        try {
            $account->registerUser($this->console->arg[3], $this->console->arg[4]);

            if (isset($this->console->arg[5]) && $this->console->arg[5]) {
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