<?php

namespace Krzysztofzylka\MicroFramework\Api;

use Exception;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Trait\Log;

/**
 * Authorization
 * @package Api
 */
class Authorization
{

    use Log;

    /**
     * Basic auth
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function basic(string $username, string $password): bool
    {
        try {
            return (new Account())->login(
                htmlspecialchars($username),
                htmlspecialchars($password)
            );
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Apikey auth
     * @param string $apikey
     * @return bool
     */
    public function apikey(string $apikey): bool
    {
        try {
            return (new Account())->loginApikey($apikey);
        } catch (Exception) {
            return false;
        }
    }

}