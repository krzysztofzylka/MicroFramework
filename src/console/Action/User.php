<?php

namespace Krzysztofzylka\MicroFramework\console\Action;

use config\Config;
use Exception;
use Krzysztofzylka\MicroFramework\console\Trait\Database;
use krzysztofzylka\SimpleLibraries\Library\Console\Prints;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;

class User
{

    use Database;

    /**
     * Users
     * @param $console
     */
    public function __construct($console)
    {
        $this->console = $console;

        switch ($console->arg[2] ?? false) {
            case 'create':
                $this->create();

                break;
            default:
                Prints::print('Action not found', false, true);

                break;
        }
    }

    /**
     * Create user
     * @return void
     */
    private function create() : void
    {
        if (!isset($this->console->arg[3]) || !isset($this->console->arg[4])) {
            Prints::print('Login and password is required', false, true);

            exit;
        }

        $this->databaseConnect($this->console->path);

        $account = new Account();

        try {
            if ((new Config())->authEmail) {
                $account->registerUser($this->console->arg[3], $this->console->arg[4], $this->console->arg[3]);
            } else {
                $account->registerUser($this->console->arg[3], $this->console->arg[4]);
            }

            if (isset($this->console->arg[5]) && $this->console->arg[5]) {
                Account::$tableInstance->updateValue('admin', 5);
                Prints::print('User has admin permission!', false, true);
            }
        } catch (Exception $exception) {
            Prints::print('Fail: ' . $exception->getMessage(), false, true);

            exit;
        }

        Prints::print('Success', false, true);
    }

}