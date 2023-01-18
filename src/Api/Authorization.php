<?php

namespace Krzysztofzylka\MicroFramework\Api;

use Krzysztofzylka\MicroFramework\Extension\Account;

class Authorization {

    /**
     * Basic auth
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function basic(string $username, string $password) : bool {
        try {
            return (new Account())->login($username, $password);
        } catch (\Exception) {
            return false;
        }
    }

}