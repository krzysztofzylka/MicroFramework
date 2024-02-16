<?php

namespace Krzysztofzylka\MicroFramework\Extension\Database;

use krzysztofzylka\DatabaseManager\DatabaseManager;
use Krzysztofzylka\MicroFramework\Extension\ModelHelper;
use Krzysztofzylka\MicroFramework\Model;
use PDO;

/**
 * Migrations
 */
class Migration
{

    use ModelHelper;

    /**
     * PDO Connection
     * @var PDO
     */
    public PDO $pdoConnection;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->pdoConnection = DatabaseManager::$connection->getConnection();

        $this->run();
    }

    /**
     * Run code
     * @return void
     */
    public function run()
    {
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

}