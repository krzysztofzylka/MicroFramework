<?php

namespace Krzysztofzylka\MicroFramework\Extension\Account;

use Exception;
use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\Condition;
use krzysztofzylka\DatabaseManager\CreateTable;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use krzysztofzylka\DatabaseManager\Enum\DatabaseType;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use krzysztofzylka\DatabaseManager\Exception\UpdateException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Exception\AccountException;
use Krzysztofzylka\MicroFramework\Exception\DatabaseException;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;
use krzysztofzylka\SimpleLibraries\Library\Hash;
use krzysztofzylka\SimpleLibraries\Library\Session;

/**
 * Account extension
 * @package Extension\Account
 */
class Account
{

    /**
     * Session name
     * @var string
     */
    public static string $sessionName = 'accountId';

    /**
     * Account id
     * @var ?int
     */
    public static ?int $accountId = null;

    /**
     * Account data
     * @var ?array
     */
    public static ?array $account = null;

    /**
     * Account remember fields
     * @var AccountRememberField
     */
    public static AccountRememberField $accountRememberField;

    /**
     * Table instance
     * @var Table
     */
    public static Table $tableInstance;

    /**
     * Constructor
     * @throws DatabaseException
     */
    public function __construct()
    {
        if (!isset(self::$tableInstance)) {
            self::$tableInstance = (new Table())->setName('account');
        }

        if (Kernel::getConfig()->authControl === true
            && DatabaseManager::getDatabaseType() === DatabaseType::mysql
            && !self::$tableInstance->exists()
        ) {
            self::install();
        }

        if (self::isLogged()) {
            self::$accountId = (int)Session::get(self::$sessionName);
            self::$account = self::getAccountData(self::$accountId);
            self::$accountRememberField = new AccountRememberField();
        }
    }

    /**
     * Install extension
     * @return bool
     * @throws DatabaseException
     */
    public function install(): bool
    {
        if (!isset(DatabaseManager::$connection)) {
            return false;
        }

        try {
            $adminColumn = new Column();
            $adminColumn->setName('admin');
            $adminColumn->setType(ColumnType::tinyint, 1);
            $adminColumn->setDefault(0);

            (new CreateTable())
                ->setName('account')
                ->addIdColumn()
                ->addUsernameColumn()
                ->addPasswordColumn()
                ->addEmailColumn()
                ->addColumn($adminColumn)
                ->addDateCreatedColumn()
                ->addDateModifyColumn()
                ->execute();

            self::$tableInstance = (new Table())->setName('account');

            return true;
        } catch (DatabaseManagerException) {
            throw new DatabaseException();
        }
    }

    /**
     * User is logged
     * @return bool
     */
    public static function isLogged(): bool
    {
        return Session::exists(self::$sessionName) && is_int(Session::get(self::$sessionName));
    }

    /**
     * Get account data
     * @param int $id
     * @return array
     */
    public static function getAccountData(int $id): array
    {
        try {
            return self::$tableInstance->find(['id' => $id]);
        } catch (Exception) {
            return [];
        }
    }

    /**
     * @param string $username
     * @param string $password
     * @param ?string $email
     * @return bool
     * @throws AccountException
     * @throws DatabaseException
     * @throws MicroFrameworkException
     */
    public function registerUser(string $username, string $password, ?string $email = null): bool
    {
        if (!isset(DatabaseManager::$connection)) {
            return false;
        }

        try {
            if (self::$tableInstance->findIsset(['username' => $username])) {
                throw new AccountException('User is already isset');
            }

            return self::$tableInstance->insert([
                'username' => $username,
                'password' => Hash::hash($password),
                'email' => $email
            ]);
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        } catch (SimpleLibraryException $exception) {
            throw new MicroFrameworkException($exception->getMessage());
        }
    }

    /**
     * Change account password
     * @param string $accountId
     * @param string $newPassword
     * @return bool
     * @throws DatabaseException
     * @throws MicroFrameworkException
     */
    public function changePassword(string $accountId, string $newPassword): bool
    {
        if (!isset(DatabaseManager::$connection)) {
            return false;
        }

        try {
            return self::$tableInstance->setId($accountId)->updateValue('password', Hash::hash($newPassword));
        } catch (UpdateException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        } catch (SimpleLibraryException $exception) {
            throw new MicroFrameworkException($exception->getMessage());
        }
    }

    /**
     * Login to account
     * @param string $username
     * @param string $password
     * @return bool
     * @throws AccountException
     * @throws DatabaseException
     * @throws MicroFrameworkException
     */
    public function login(string $username, string $password): bool
    {
        if (!isset(DatabaseManager::$connection)) {
            return false;
        }

        try {
            $find = self::$tableInstance->find(['username' => $username], ['id', 'password']);
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }

        if (!$find) {
            throw new AccountException('User not found', 404);
        }

        try {
            $checkHash = Hash::checkHash($find['account']['password'], $password);
        } catch (SimpleLibraryException $exception) {
            throw new MicroFrameworkException($exception->getMessage());
        }

        if (!$checkHash) {
            throw new AccountException('Authentication failed', 401);
        }

        self::$accountId = (int)$find['account']['id'];
        self::$account = self::getAccountData(self::$accountId);
        Session::set(self::$sessionName, (int)$find['account']['id']);

        return true;
    }

    /**
     * Logout user
     * @return void
     */
    public function logout(): void
    {
        Session::delete(self::$sessionName);

        self::$accountId = null;
        self::$account = null;
    }

}