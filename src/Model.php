<?php

namespace Krzysztofzylka\MicroFramework;

use krzysztofzylka\DatabaseManager\Condition;
use krzysztofzylka\DatabaseManager\Enum\BindType;
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
     * POST data
     * @var ?array
     */
    public ?array $data = null;

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

    /**
     * Find all
     * @param ?Condition $condition
     * @param ?string $orderBy
     * @param ?string $limit
     * @param ?string $groupBy
     * @return array|false
     * @throws DatabaseException
     */
    public function findAll(?Condition $condition = null, ?string $orderBy = null, ?string $limit = null, ?string $groupBy = null) : array|false {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            return $this->tableInstance->findAll($condition, $orderBy, $limit, $groupBy);
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Insert
     * @param array $data
     * @return bool
     * @throws DatabaseException
     */
    public function insert(array $data) : bool {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            if (!$this->beforeInsert()) {
                return false;
            }

            if ($this->tableInstance->insert($data)) {
                return $this->afterInsert();
            } else {
                return false;
            }
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Find count
     * @param ?Condition $condition
     * @param ?string $groupBy
     * @return int
     * @throws DatabaseException
     */
    public function findCount(?Condition $condition = null, ?string $groupBy = null) : int {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            return $this->tableInstance->findCount($condition, $groupBy);
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Find isset
     * @param ?Condition $condition
     * @return bool
     * @throws DatabaseException
     */
    public function findIsset(?Condition $condition = null) : bool {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            return $this->tableInstance->findIsset($condition);
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Update
     * @param array $data
     * @return bool
     * @throws DatabaseException
     */
    public function update(array $data) : bool {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            if (!$this->beforeUpdate()) {
                return false;
            }

            if ($this->tableInstance->update($data)) {
                return $this->afterUpdate();
            } else {
                return false;
            }
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Update single column
     * @param string $columnName
     * @param mixed $value
     * @return bool
     * @throws DatabaseException
     */
    public function updateValue(string $columnName, mixed $value) : bool {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            if (!$this->beforeUpdate($columnName, $value)) {
                return false;
            }

            if ($this->tableInstance->updateValue($columnName, $value)) {
                return $this->afterUpdate($columnName, $value);
            } else {
                return false;
            }
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Delete
     * @param ?int $id
     * @return bool
     * @throws DatabaseException
     */
    public function delete(?int $id = null) : bool {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            return $this->tableInstance->delete($id);
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Bind table
     * @param BindType $bindType
     * @param string $tableName
     * @param ?string $primaryKey
     * @param ?string $foreignKey
     * @return $this
     * @throws DatabaseException
     */
    public function bind(BindType $bindType, string $tableName, ?string $primaryKey = null, ?string $foreignKey = null) : self {
        if (!isset($this->tableInstance)) {
            return $this;
        }

        try {
            $this->tableInstance->bind($bindType, $tableName, $primaryKey, $foreignKey);
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }

        return $this;
    }

    /**
     * Before insert
     * @return bool
     */
    public function beforeInsert() : bool {
        return true;
    }

    /**
     * After insert
     * @return bool
     */
    public function afterInsert() : bool {
        return true;
    }

    /**
     * Before update
     * @param ?string $columnName if updateValue
     * @param ?string $value if updateValue
     * @return bool
     */
    public function beforeUpdate(?string $columnName = null, ?string $value = null) : bool {
        return true;
    }

    /**
     * After update
     * @param ?string $columnName if updateValue
     * @param ?string $value if updateValue
     * @return bool
     */
    public function afterUpdate(?string $columnName = null, ?string $value = null) : bool {
        return true;
    }

}