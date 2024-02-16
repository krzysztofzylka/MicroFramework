<?php

namespace Krzysztofzylka\MicroFramework\Console\Traits;

use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Extension\Database\Database;
use Krzysztofzylka\MicroFramework\Kernel;

trait InternalConsoleMigration
{

    /**
     * Migration
     * @return void
     * @throws MicroFrameworkException
     */
    private function migrationRun(): void
    {
        $this->print('Run migrations');

        Kernel::$silent = true;
        new Kernel($this->path);

        if (!$_ENV['DATABASE']) {
            $this->print('Database is disabled', color: 'red', exit: true);
        }

        $migration = new Database();

        try {
            $migration->run();
        } catch (\Throwable $exception) {
            $this->print('Migration failed', exit: true, color: 'red');
        }

        $this->print('Successfully', color: 'green', exit: true);
    }

}