<?php

namespace Krzysztofzylka\MicroFramework\Extension\Account;

use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Exception\AccountException;

class AccountRememberField
{

    /**
     * Database table
     * @var Table
     */
    private Table $table;

    /**
     * Set remember fields
     * @param string $name name
     * @param string $value value
     * @return bool
     * @throws AccountException
     */
    public function set(string $name, string $value): bool
    {
        if (!Account::isLogged()) {
            return false;
        }

        try {
            $table = $this->getTable();

            $find = $table->find([
                'account_id' => Account::$accountId,
                'name' => $name
            ], ['id']);

            if ($find) {
                return $table->setId($find['account_remember_field']['id'])->update([
                    'value' => $value,
                    'date' => date('Y-m-d H:i:s')
                ]);
            } else {
                return $table->insert([
                    'account_id' => Account::$accountId,
                    'name' => $name,
                    'value' => $value,
                    'date' => date('Y-m-d H:i:s')
                ]);
            }
        } catch (DatabaseManagerException $exception) {
            throw new AccountException(__('micro-framework.account.fail_set_remember_field') . ': ' . $exception->getHiddenMessage());
        }
    }

    /**
     * Get table instance
     * @return Table
     */
    private function getTable(): Table
    {
        if (isset($this->table)) {
            return $this->table;
        }

        $this->table = new Table('account_remember_field');

        return $this->table;
    }

    /**
     * Get remember fields
     * @param string $name name
     * @return false|string
     * @throws AccountException
     */
    public function get(string $name): false|string
    {
        if (!Account::isLogged()) {
            return false;
        }

        try {
            $find = $this->getTable()->find([
                'account_id' => Account::$accountId,
                'name' => $name
            ], ['value']);

            if (!$find) {
                return false;
            }

            return $find['account_remember_field']['value'];
        } catch (DatabaseManagerException $exception) {
            throw new AccountException(__('micro-framework.account.fail_get_remember_field') . ': ' . $exception->getHiddenMessage());
        }
    }

}