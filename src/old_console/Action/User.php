<?php

namespace Krzysztofzylka\MicroFramework\bin\Action;

use Exception;
use Krzysztofzylka\MicroFramework\bin\Console\Console;
use Krzysztofzylka\MicroFramework\bin\Trait\Database;
use Krzysztofzylka\MicroFramework\bin\Trait\Prints;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;

class User
{

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
     * @throws NotFoundException
     * @throws SimpleLibraryException
     */
    private function create() : void
    {
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
        } catch (Exception $exception) {
            $this->dprint('Fail: ' . $exception->getMessage());

            exit;
        }

        $this->dprint('Success');
    }

}