<?php

namespace Krzysztofzylka\MicroFramework;

use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Enum\BindType;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use krzysztofzylka\DatabaseManager\Table;
use krzysztofzylka\DatabaseManager\Transaction;
use Krzysztofzylka\MicroFramework\Exception\DatabaseException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Memcache\Memcache;
use Krzysztofzylka\MicroFramework\Trait\Log;
use Krzysztofzylka\MicroFramework\Trait\ModelValidation;
use PDOStatement;

/**
 * Model
 * @package Model
 */
class Model
{

    use Log;
    use Trait\Model;
    use ModelValidation;

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
     * @var Controller|ControllerApi
     */
    public Controller|ControllerApi $controller;

    /**
     * Database table instance
     * @var Table
     */
    public Table $tableInstance;

    /**
     * Database transaction instance
     * @var Transaction
     */
    public Transaction $transactionInstance;

    /**
     * POST data
     * @var ?array
     */
    public ?array $data = null;

    /**
     * Select required
     * @param array|null $condition
     * @param array|null $columns
     * @param ?string $orderBy
     * @return array|false
     * @throws DatabaseException
     * @throws NotFoundException
     */
    public function findRequired(?array $condition = null, ?array $columns = null, ?string $orderBy = null): array|false
    {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            $find = $this->find($condition, $columns, $orderBy);

            if (!$find) {
                throw new NotFoundException();
            }

            return $find;
        } catch (DatabaseException $exception) {
            throw new DatabaseException($exception->getMessage());
        }
    }

    /**
     * Select
     * @param ?array $condition
     * @param ?array $columns
     * @param ?string $orderBy
     * @return array|false
     * @throws DatabaseException
     * @throws \Exception
     */
    public function find(?array $condition = null, ?array $columns = null, ?string $orderBy = null): array|false
    {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            Debug::startTime();
            $find = $this->tableInstance->find($condition, $columns, $orderBy);
            Debug::endTime($this->name . '_find');

            return $find;
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Find all
     * @param ?array $condition
     * @param ?array $columns
     * @param ?string $orderBy
     * @param ?string $limit
     * @param ?string $groupBy
     * @return array|false
     * @throws DatabaseException
     */
    public function findAll(?array $condition = null, ?array $columns = null, ?string $orderBy = null, ?string $limit = null, ?string $groupBy = null): array|false
    {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            Debug::startTime();
            $find = $this->tableInstance->findAll($condition, $columns, $orderBy, $limit, $groupBy);
            Debug::endTime($this->name . '_findAll');

            return $find;
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Find count
     * @param ?array $condition
     * @param ?string $groupBy
     * @return int
     * @throws DatabaseException
     */
    public function findCount(?array $condition = null, ?string $groupBy = null): int
    {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            Debug::startTime();
            $findCount = $this->tableInstance->findCount($condition, $groupBy);
            Debug::endTime($this->name . '_findCount');

            return $findCount;
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Find isset
     * @param ?array $condition
     * @return bool
     * @throws DatabaseException
     */
    public function findIsset(?array $condition = null): bool
    {
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
     * Update single column
     * @param string $columnName
     * @param mixed $value
     * @return bool
     * @throws DatabaseException
     */
    public function updateValue(string $columnName, mixed $value): bool
    {
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
     * Before update
     * @param ?string $columnName if updateValue
     * @param ?string $value if updateValue
     * @return bool
     */
    public function beforeUpdate(?string $columnName = null, ?string $value = null): bool
    {
        return true;
    }

    /**
     * After update
     * @param ?string $columnName if updateValue
     * @param ?string $value if updateValue
     * @return bool
     */
    public function afterUpdate(?string $columnName = null, ?string $value = null): bool
    {
        return true;
    }

    /**
     * Delete
     * @param ?int $id
     * @return bool
     * @throws DatabaseException
     */
    public function delete(?int $id = null): bool
    {
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
     * @param BindType|array $bind
     * @param ?string $tableName
     * @param ?string $primaryKey
     * @param ?string $foreignKey
     * @return $this
     */
    public function bind(BindType|array $bind, string $tableName = null, ?string $primaryKey = null, ?string $foreignKey = null): self
    {
        if (!isset($this->tableInstance)) {
            return $this;
        }

        $this->tableInstance->bind($bind, $tableName, $primaryKey, $foreignKey);

        return $this;
    }

    /**
     * Begin transaction
     * @return bool
     * @throws DatabaseException
     */
    public function transactionBegin(): bool
    {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            $this->transactionInstance->begin();

            return true;
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Commit transaction
     * @return bool
     * @throws DatabaseException
     */
    public function transactionCommit(): bool
    {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            $this->transactionInstance->commit();

            return true;
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Rollback transaction
     * @return bool
     * @throws DatabaseException
     */
    public function transactionRollback(): bool
    {
        if (!isset($this->tableInstance)) {
            return false;
        }

        try {
            $this->transactionInstance->rollback();

            return true;
        } catch (DatabaseManagerException $exception) {
            throw new DatabaseException($exception->getHiddenMessage());
        }
    }

    /**
     * Query
     * @param string $sql
     * @return false|PDOStatement
     */
    public function query(string $sql): bool|PDOStatement
    {
        $pdo = DatabaseManager::$connection->getConnection();

        return $pdo->query($sql);
    }

    /**
     * Save (insert or update (if isset id)) with validate
     * @param array $data
     * @param bool $validate
     * @param ?array $protected
     * @return bool
     * @throws DatabaseException
     * @throws NotFoundException
     */
    public function save(array $data, bool $validate = true, ?array $protected = null): bool
    {
        if (empty($data)) {
            return false;
        }

        $isValid = $validate ? $this->validate($data) : true;

        if ($isValid) {
            foreach ($data as $model => $insertData) {
                if (!is_null($protected)) {
                    $insertData = array_intersect_key($insertData, array_flip($protected));
                }

                if (is_int($this->getId())) {
                    $this->loadModel($model)->setId($this->getId())->update($insertData);
                } else {
                    $this->loadModel($model)->insert($insertData);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Get ID
     * @return false|int|null
     */
    public function getId(): false|null|int
    {
        if (!isset($this->tableInstance)) {
            return false;
        }

        return $this->tableInstance->getId();
    }

    /**
     * Update
     * @param array $data
     * @return bool
     * @throws DatabaseException
     */
    public function update(array $data): bool
    {
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
     * Set ID
     * @param ?int $id
     * @return Model|false
     */
    public function setId(?int $id = null): self|false
    {
        if (!isset($this->tableInstance)) {
            return false;
        }

        $this->tableInstance->setId($id);

        return $this;
    }

    /**
     * Insert
     * @param array $data
     * @return bool
     * @throws DatabaseException
     */
    public function insert(array $data): bool
    {
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
     * Before insert
     * @return bool
     */
    public function beforeInsert(): bool
    {
        return true;
    }

    /**
     * After insert
     * @return bool
     */
    public function afterInsert(): bool
    {
        return true;
    }

    /**
     * Save memcache
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return bool
     */
    public function memcacheSet(string $key, mixed $value, int $expiration = 0): bool
    {
        return Memcache::set(Account::$accountId . '_model_' . $this->name . '_' . $key, $value, $expiration);
    }

    /**
     * Get memcache
     * @param string $key
     * @return mixed
     */
    public function memcacheGet(string $key): mixed
    {
        return Memcache::get(Account::$accountId . '_model_' . $this->name . '_' . $key);
    }

    /**
     * Magic __get
     * @param string $name
     * @return mixed|Model
     */
    public function __get(string $name): mixed
    {
        if (in_array($name, array_keys($this->models))) {
            return $this->models[$name];
        }

        return trigger_error(__('micro-framework.model.undefined_property', ['name' => $name]), E_USER_WARNING);
    }

}