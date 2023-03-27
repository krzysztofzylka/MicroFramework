<?php

namespace Krzysztofzylka\MicroFramework\Extension\Database;

use krzysztofzylka\DatabaseManager\DatabaseManager;
use krzysztofzylka\DatabaseManager\Table;

/**
 * Database updater
 * @package Extension\Database
 */
class Updater
{

    /**
     * Table object
     * @var Table
     */
    public Table $table;

    /**
     * Database manager object
     * @var DatabaseManager
     */
    public DatabaseManager $databaseManager;

    /**
     * Init updater
     */
    public function __construct()
    {
        $this->table = new Table();
        $this->databaseManager = new DatabaseManager();
        $this->run();
    }

    /**
     * Run script
     * @return void
     */
    public function run()
    {
    }

}