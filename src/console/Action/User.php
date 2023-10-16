<?php

namespace Krzysztofzylka\MicroFramework\console\Action;

use config\Config;
use Exception;
use Krzysztofzylka\MicroFramework\console\Trait\Database;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use krzysztofzylka\SimpleLibraries\Library\Console\Prints;

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

        switch ($console->arg['args'][1] ?? false) {
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
    private function create(): void
    {
        if (!isset($this->console->arg['args'][2]) || !isset($this->console->arg['args'][3])) {
            Prints::print('Login and password is required', false, true);

            exit;
        }

        $this->databaseConnect($this->console->path);

        $account = new Account();

        try {
            if ($_ENV['auth_with_email']) {
                $account->registerUser($this->console->arg['args'][2], $this->console->arg['args'][3], $this->console->arg['args'][2]);
            } else {
                $account->registerUser($this->console->arg['args'][2], $this->console->arg['args'][3]);
            }

            if (isset($this->console->arg['args'][4]) && $this->console->arg['args'][4]) {
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