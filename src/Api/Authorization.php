<?php

namespace Krzysztofzylka\MicroFramework\Api;

use Exception;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;

/**
 * Authorization
 * @package Api
 */
class Authorization
{

    /**
     * Basic auth
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function basic(string $username, string $password): bool
    {
        try {
            return (new Account())->login($username, $password);
        } catch (Exception) {
            return false;
        }
    }

}