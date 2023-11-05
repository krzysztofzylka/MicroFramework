<?php

namespace Krzysztofzylka\MicroFramework;

use krzysztofzylka\DatabaseManager\Table;

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

}