<?php

namespace Krzysztofzylka\MicroFramework\Extension\Authorization;

use Exception;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\Hash\Hash;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;

class Account
{

    /**
     * Table instance
     * @var Table
     */
    private static Table $tableInstance;

    /**
     * Is auth user
     * @var bool
     */
    private static bool $isAuth = false;

    /**
     * Account id
     * @var ?int
     */
    private static ?int $accountId = null;

    /**
     * Account data
     * @var array
     */
    private static array $account = [];

    /**
     * Init account
     * @return void
     * @throws DatabaseManagerException
     */
    public static function init(): void
    {
        self::$tableInstance = new Table('account');

        if (isset($_SESSION[$_ENV['AUTHORIZATION_SESSION_NAME']])) {
            self::setAccountId($_SESSION[$_ENV['AUTHORIZATION_SESSION_NAME']]);
            self::setAuth(true);
            self::setAccount(self::getAccountId());
            DebugBar::addFrameworkMessage('Account logged by session as ' . self::getAccountId(), 'Account');
        }
    }

    /**
     * Get account id
     * @return int|null
     */
    public static function getAccountId(): ?int
    {
        return self::$accountId;
    }

    /**
     * Set account id
     * @param int|null $accountId
     * @return void
     */
    public static function setAccountId(?int $accountId): void
    {
        self::$accountId = $accountId;
    }

    /**
     * Is auth user
     * @return bool
     */
    public static function isAuth(): bool
    {
        return self::$isAuth;
    }

    /**
     * Set auth user
     * @param bool $isAuth
     * @return void
     */
    public static function setAuth(bool $isAuth): void
    {
        self::$isAuth = $isAuth;
    }

    /**
     * Get account data
     * @return array
     */
    public static function getAccount(): array
    {
        return self::$account;
    }

    /**
     * Set account data
     * @param int|array $account
     * @return void
     * @throws DatabaseManagerException
     */
    public static function setAccount(int|array $account): void
    {
        if (is_int($account)) {
            $find = self::$tableInstance->find(['account.id' => $account]);

            if ($find) {
                $account = $find;
            }
        }

        self::$account = $account;
    }

    /**
     * Register user
     * @param string $login
     * @param string $password
     * @return bool
     * @throws DatabaseManagerException
     */
    public static function register(string $login, string $password): bool
    {
        try {
            $findAccount = self::$tableInstance->findIsset(['account.email' => $login]);

            if ($findAccount) {
                return false;
            }

            $password = Hash::hash($password, $_ENV['AUTHORIZATION_HASH_ALGORITHM']);

            self::$tableInstance->insert(['email' => $login, 'password' => $password]);

            return true;
        } catch (Exception $exception) {
            Log::throwableLog($exception, 'Register error');

            throw new $exception;
        }
    }

    /**
     * Login user
     * @param string $login
     * @param string $password
     * @return bool
     * @throws DatabaseManagerException
     */
    public static function login(string $login, string $password): bool
    {
        try {
            $findAccount = self::$tableInstance->find(['account.email' => $login], ['account.id', 'account.password']);

            if (!$findAccount || ($findAccount && !Hash::checkHash($findAccount['account']['password'], $password))) {
                return false;
            }

            $_SESSION[$_ENV['AUTHORIZATION_SESSION_NAME']] = $findAccount['account']['id'];
            Account::setAccountId($_SESSION[$_ENV['AUTHORIZATION_SESSION_NAME']] );
            Account::setAccount($_SESSION[$_ENV['AUTHORIZATION_SESSION_NAME']] );
            Account::setAuth(true);

            return true;
        } catch (Exception $exception) {
            Log::throwableLog($exception, 'Login error');

            throw new $exception;
        }
    }

    /**
     * Logout user
     * @return void
     */
    public static function logout(): void
    {
        unset($_SESSION[$_ENV['AUTHORIZATION_SESSION_NAME']]);
        Account::setAccountId(null);
        Account::setAccount([]);
        Account::setAuth(false);
    }

}