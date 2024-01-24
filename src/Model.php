<?php

namespace Krzysztofzylka\MicroFramework;

use Exception;
use krzysztofzylka\DatabaseManager\Condition;
use krzysztofzylka\DatabaseManager\Enum\BindType;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use krzysztofzylka\DatabaseManager\Table;
use krzysztofzylka\DatabaseManager\Transaction;
use Krzysztofzylka\MicroFramework\Exception\HiddenException;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\ModelHelper;
use Krzysztofzylka\MicroFramework\Libs\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Libs\Log\Log;
use Throwable;

/**
 * Class Model
 */
class Model
{

    use ModelHelper;

    /**
     * Model name
     * @var string
     */
    public string $name;

    /**
     * Use table
     * @var mixed
     */
    public mixed $useTable = null;

    /**
     * Database table instance
     * @var Table
     */
    public Table $tableInstance;

    /**
     * Controller
     * @var Controller
     */
    public Controller $controller;

    /**
     * Isolator name
     * @var null|string
     */
    public ?string $isolatorName = null;

    /**
     * Isolator
     * @var string|int
     */
    public string|int $isolator;

    /**
     * Element ID
     * @var int|null
     */
    public ?int $id = null;

    /**
     * Transaction instance
     * @var Transaction
     */
    public Transaction $transactionInstance;

    /**
     * Bind left join
     * @var array
     */
    public array $bindLeftJoin = [];

    /**
     * Auto bind
     * @var bool
     */
    private bool $autoBind = false;

    /**
     * Find records based on the specified condition, columns, and order.
     * @param array|null $condition Optional. The condition to filter the records. Default is null.
     * @param array|null $columns Optional. The columns to select from the records. Default is null.
     * @param string|null $orderBy Optional. The column to order the records by. Default is null.
     * @return array The records that match the condition, columns, and order.
     * @throws HiddenException If an error occurs during the find operation.
     */
    public function find(?array $condition = null, ?array $columns = null, ?string $orderBy = null): array
    {
        if (!isset($this->tableInstance)) {
            return [];
        }

        DebugBar::timeStart('find', 'Find');

        try {
            $this->_prepareBind();

            if (!$_ENV['DATABASE']) {
                throw new MicroFrameworkException('Database is not configured');
            }

            $this->_prepareCondition($condition);
            $find = $this->tableInstance->find($condition, $columns, $orderBy);
            DebugBar::timeStop('find');

            return $find;
        } catch (Throwable $exception) {
            $message = $exception->getMessage();

            if ($exception instanceof DatabaseManagerException) {
                $message = $exception->getHiddenMessage();
            }

            DebugBar::addThrowable($exception);
            DebugBar::addFrameworkMessage($message, 'ERROR');

            throw new HiddenException($message);
        }
    }

    /**
     * Finds and returns records based on the provided condition and columns.
     * If no records are found, throws a NotFoundException.
     * @param array|null $condition The condition of the query.
     * @param array|null $columns The columns to select from the table.
     * @param string|null $orderBy The column to use for ordering the results.
     * @return array The array of found records.
     * @throws HiddenException
     * @throws NotFoundException if no records are found.
     */
    public function findOrThrow(?array $condition = null, ?array $columns = null, ?string $orderBy = null): array
    {
        $find = $this->find($condition, $columns, $orderBy);

        if (!$find) {
            throw new NotFoundException();
        }

        return $find;
    }

    /**
     * Find all elements
     * @param array|null $condition
     * @param array|null $columns
     * @param ?string $orderBy
     * @param ?string $limit
     * @param ?string $groupBy
     * @return array
     * @throws HiddenException
     */
    public function findAll(?array $condition = null, ?array $columns = null, ?string $orderBy = null, ?string $limit = null, ?string $groupBy = null): array
    {
        if (!isset($this->tableInstance)) {
            return [];
        }

        DebugBar::timeStart('findAll', 'Find all');

        try {
            $this->_prepareBind();

            if (!$_ENV['DATABASE']) {
                throw new MicroFrameworkException('Database is not configured');
            }

            $this->_prepareCondition($condition);
            $find = $this->tableInstance->findAll($condition, $columns, $orderBy, $limit, $groupBy);
            DebugBar::timeStop('findAll');

            return $find;
        } catch (Throwable $exception) {
            $message = $exception->getMessage();

            if ($exception instanceof DatabaseManagerException) {
                $message = $exception->getHiddenMessage();
            }

            DebugBar::addThrowable($exception);
            DebugBar::addFrameworkMessage($message, 'ERROR');

            throw new HiddenException($message);
        }
    }

    /**
     * Find the count of elements matching the condition
     * @param array|null $condition The condition to filter the elements (optional)
     * @param ?string $groupBy The column(s) to group by (optional)
     * @return int The count of elements matching the condition
     * @throws HiddenException Thrown if there is an error in the database operation
     */
    public function findCount(?array $condition = null, ?string $groupBy = null): int
    {
        if (!isset($this->tableInstance)) {
            return 0;
        }

        DebugBar::timeStart('findCount', 'Find count');

        try {
            $this->_prepareBind();

            if (!$_ENV['DATABASE']) {
                throw new MicroFrameworkException('Database is not configured');
            }

            $this->_prepareCondition($condition);
            $find = $this->tableInstance->findCount($condition, $groupBy);
            DebugBar::timeStop('findCount');

            return $find;
        } catch (Throwable $exception) {
            $message = $exception->getMessage();

            if ($exception instanceof DatabaseManagerException) {
                $message = $exception->getHiddenMessage();
            }

            DebugBar::addThrowable($exception);
            DebugBar::addFrameworkMessage($message, 'ERROR');

            throw new HiddenException($message);
        }
    }

    /**
     * Finds and returns whether records exist based on the provided condition.
     * @param array|null $condition The condition of the query.
     * @return bool True if records exist; otherwise, false.
     * @throws HiddenException
     */
    public function findIsset(?array $condition = null): bool
    {
        if (!isset($this->tableInstance)) {
            return false;
        }

        DebugBar::timeStart('findIsset', 'Find isset');

        try {
            $this->_prepareBind();

            if (!$_ENV['DATABASE']) {
                throw new MicroFrameworkException('Database is not configured');
            }

            $this->_prepareCondition($condition);
            $find = $this->tableInstance->findIsset($condition);
            DebugBar::timeStop('findIsset');

            return $find;
        } catch (Throwable $exception) {
            $message = $exception->getMessage();

            if ($exception instanceof DatabaseManagerException) {
                $message = $exception->getHiddenMessage();
            }

            DebugBar::addThrowable($exception);
            DebugBar::addFrameworkMessage($message, 'ERROR');

            throw new HiddenException($message);
        }
    }

    /**
     * Set the isolator value
     * @param int|string $isolator The isolator value to be set
     * @return self
     */
    public function setIsolator(int|string $isolator): self
    {
        $this->isolator = $isolator;

        return $this;
    }

    /**
     * Save data
     * @param array $data The data to be saved
     * @return bool True if data is saved successfully, false otherwise
     * @throws HiddenException
     */
    public function save(array $data): bool
    {
        if (!isset($this->tableInstance)) {
            return false;
        }

        DebugBar::timeStart('save', 'Save');

        try {
            if (!$_ENV['DATABASE']) {
                throw new MicroFrameworkException('Database is not configured');
            }

            if (is_null($this->id)) {
                $save = $this->tableInstance->insert($data);

                $this->id = $this->tableInstance->getId();
            } else {
                $save = $this->tableInstance->setId($this->id)->update($data);
            }
            DebugBar::timeStop('save');

            return $save;
        } catch (Throwable $exception) {
            $message = $exception->getMessage();

            if ($exception instanceof DatabaseManagerException) {
                $message = $exception->getHiddenMessage();
            }

            DebugBar::addThrowable($exception);
            DebugBar::addFrameworkMessage($message, 'ERROR');

            throw new HiddenException($message);
        }
    }

    /**
     * Delete an element by ID
     * @param int $id The ID of the element to delete
     * @return bool True if the element was deleted successfully, false otherwise
     * @throws HiddenException If an error occurs during the deletion process
     */
    public function del(int $id): bool
    {
        if (!isset($this->tableInstance)) {
            return false;
        }

        DebugBar::timeStart('delete', 'Delete');

        try {
            if (!$_ENV['DATABASE']) {
                throw new MicroFrameworkException('Database is not configured');
            }

            $delete = $this->tableInstance->delete($id);

            DebugBar::timeStop('delete');

            return $delete;
        } catch (Throwable $exception) {
            $message = $exception->getMessage();

            if ($exception instanceof DatabaseManagerException) {
                $message = $exception->getHiddenMessage();
            }

            DebugBar::addThrowable($exception);
            DebugBar::addFrameworkMessage($message, 'ERROR');

            throw new HiddenException($message);
        }
    }

    /**
     * Set the ID value of the object
     * @param int|null $id The ID value to set. Pass null to unset the ID.
     * @return self The current object with the updated ID value.
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Bind a table instance to the query builder.
     * @param BindType|array $bind The bind type or an array of bind types.
     * @param string|null $tableName The name of the table to bind to. If null, the previously bound table name will be used.
     * @param ?string $primaryKey The primary key column name. If null, the previously bound primary key will be used.
     * @param ?string $foreignKey The foreign key column name. If null, the previously bound foreign key will be used.
     * @param null|array|Condition $condition The condition to apply to the table binding. This can be either an array or a Condition object. If null, no condition will be applied.
     * @return self Returns the query builder instance.
     */
    public function bind(BindType|array $bind, string $tableName = null, ?string $primaryKey = null, ?string $foreignKey = null, null|array|Condition $condition = null): self
    {
        if (!isset($this->tableInstance)) {
            return $this;
        }

        $this->tableInstance->bind($bind, $tableName, $primaryKey, $foreignKey, $condition);

        return $this;
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

        return trigger_error(
            'Undefined model',
            E_USER_WARNING
        );
    }

    /**
     * Prepare the condition array for find operation.
     * If the condition is an array and the isolator property is set, add the isolator name and value to the condition array.
     * @param array|null &$condition The condition array to be prepared.
     * @return void
     */
    private function _prepareCondition(?array &$condition): void
    {
        if (!isset($this->isolator) || !$this->isolator) {
            return;
        }

        if (!is_array($condition)) {
            $condition = [];
        }

        $condition[$this->useTable . '.' . $this->isolatorName] = $this->isolator;
    }

    /**
     * Prepare the bind values
     */
    private function _prepareBind(): void
    {
        if ($this->autoBind) {
            return;
        }

        foreach ($this->bindLeftJoin as $bind => $bindData) {
            $primaryKey = null;
            $foreignKey = null;

            if (is_numeric($bind)) {
                $bind = $bindData;
                $bindData = [];
            }

            if (isset($bindData['primaryKey'])) {
                $primaryKey = $bindData['primaryKey'];
            }

            if (isset($bindData['foreignKey'])) {
                $foreignKey = $bindData['foreignKey'];
            }

            $this->bind(BindType::leftJoin, $bind, $primaryKey, $foreignKey);
        }

        $this->autoBind = true;
    }

    /**
     * Logs a message.
     * @param string $message The message to be logged.
     * @param string $level The log level of the message (default: 'INFO').
     * @param array $content Additional content to be logged (default: []).
     * @return bool Returns true if the message was successfully logged, false otherwise.
     * @throws Exception
     */
    public function log(string $message, string $level = 'INFO', array $content = []): bool
    {
        return Log::log($message, $level, $content);
    }

}