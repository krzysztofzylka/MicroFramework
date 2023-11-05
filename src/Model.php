<?php

namespace Krzysztofzylka\MicroFramework;

use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Exception\HiddenException;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Throwable;

/**
 * Class Model
 */
class Model
{

    /**
     * Model name
     * @var string
     */
    public string $name;

    /**
     * Use table
     * @var string|false|null
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
     * Find one element
     * @param array|null $condition
     * @param array|null $columns
     * @param ?string $orderBy
     * @return array
     * @throws HiddenException
     */
    public function find(?array $condition = null, ?array $columns = null, ?string $orderBy = null): array
    {
        DebugBar::timeStart('find', 'Find');
        try {
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
        DebugBar::timeStart('findAll', 'Find all');
        try {
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
     * Count
     * @param ?array $condition
     * @param ?string $groupBy
     * @return int
     * @throws HiddenException
     */
    public function findCount(?array $condition = null, ?string $groupBy = null): int
    {
        DebugBar::timeStart('findCount', 'Find count');
        try {
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
     * Set isolator
     * @param int|string $isolator
     * @return self
     */
    public function setIsolator(int|string $isolator): self
    {
        $this->isolator = $isolator;

        return $this;
    }

    /**
     * Save or update data
     * @param array $data
     * @return bool
     * @throws HiddenException
     */
    public function save(array $data): bool
    {
        DebugBar::timeStart('save', 'Save');
        try {
            if (!$_ENV['DATABASE']) {
                throw new MicroFrameworkException('Database is not configured');
            }

            if (!is_null($this->id)) {
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
     * Prepare condition
     * @param ?array $condition
     * @return void
     */
    private function _prepareCondition(?array &$condition): void
    {
        if (!is_array($condition) || is_null($this->isolator) || !$this->isolator) {
            return;
        }

        $condition[$this->useTable . '.' . $this->isolatorName] = $this->isolator;
    }

}