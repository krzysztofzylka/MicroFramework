<?php

namespace Krzysztofzylka\MicroFramework;

use krzysztofzylka\DatabaseManager\Condition;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Exception\DatabaseException;

class Model {

    /**
     * Use table
     * @var bool
     */
    public bool $useTable = true;

    /**
     * Custom table name
     * @var ?string
     */
    public ?string $tableName = null;

    /**
     * Model name
     * @var string
     */
    public string $name;

    /**
     * Controller
     * @var Controller
     */
    public Controller $controller;

    /**
     * Database table instance
     * @var Table
     */
    public Table $tableInstance;

    /**
     * Set ID
     * @param ?int $id
     * @return bool
     */
    public function setId(?int $id) : bool {
        if (!isset($this->tableInstance)) {
            return false;
        }

        $this->tableInstance->setId($id);

        return true;
    }

    /**
     * Get ID
     * @return false|int|null
     */
    public function getId() : false|null|int {
        if (!isset($this->tableInstance)) {
            return false;
        }

        return $this->tableInstance->getId();
    }

    /**
     * Select
     * @param ?Condition $condition
     * @param ?string $orderBy
     * @return array|false
     * @throws DatabaseException
     */
    public function find(?Condition $condition = null, ?string $orderBy = null) : array|false {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            return $this->tableInstance->find($condition, $orderBy);
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

}