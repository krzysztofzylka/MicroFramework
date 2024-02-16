<?php

namespace Krzysztofzylka\MicroFramework\Extension\Database;

use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\CreateTable;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use krzysztofzylka\DatabaseManager\Table;
use krzysztofzylka\DatabaseManager\Transaction;
use Krzysztofzylka\File\File;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Kernel;

class Database
{

    /**
     * Table instance
     * @var Table
     */
    public static Table $tableInstance;

    public function __construct()
    {
        try {
            self::$tableInstance = new Table('migrations');

            if (!self::$tableInstance->exists()) {
                (new CreateTable())
                    ->setName('migrations')
                    ->addIdColumn()
                    ->addSimpleIntColumn('timestamp', 11)
                    ->addSimpleVarcharColumn('status')
                    ->addColumn((new Column('error'))->setType(ColumnType::text))
                    ->addDateModifyColumn()
                    ->addDateCreatedColumn()
                    ->execute();
            }
        } catch (DatabaseManagerException $exception) {
            Log::throwableLog($exception, 'Migration init fail');
        }
    }

    public function run(): void
    {

        try {
            $list = array_column(array_column(self::$tableInstance->findAll(null, ['migrations.timestamp']), 'migrations'), 'timestamp');
            $migrations = $this->getMigrationList($list);

            foreach ($migrations as $migration) {
                if ($migration['extension'] === 'php') {
                    $transaction = new Transaction();
                    try {
                        $transaction->begin();
                        self::$tableInstance->insert([
                            'timestamp' => $migration['name'],
                            'status' => 'start'
                        ]);
                        include($migration['path_full']);
                        self::$tableInstance->update([
                            'status' => 'success'
                        ]);
                        $transaction->commit();
                    } catch (\Throwable $throwable) {
                        $transaction->rollback();
                        Log::throwableLog($throwable, 'Migration failed');

                        throw $throwable;
                    }
                }
            }
        } catch (DatabaseManagerException $exception) {
            Log::throwableLog($exception, 'Failed run migrator');

            throw $exception;
        }
    }

    /**
     * Migration list
     * @return array
     */
    private function getMigrationList(array $removeList = []): array
    {
        $list = [];
        $migrationDirs = [
            Kernel::getPath('migrations'),
            __DIR__ . '/Migrations'
        ];

        foreach ($migrationDirs as $migrationDir) {
            $scanDir = File::scanDir($migrationDir);

            foreach ($scanDir as $path) {
                $extension = File::getExtension($path);
                $name = str_replace('.' . $extension, '', basename($path));

                if (in_array($name, $removeList)) {
                    continue;
                }

                $list[] = [
                    'path' => $path,
                    'path_full' => $migrationDir . '/' . $path,
                    'migration_directory' => $migrationDir,
                    'extension' => $extension,
                    'name' => $name
                ];
            }
        }

        usort($list, function ($item1, $item2) {
            return $item1['name'] <=> $item2['name'];
        });

        return $list;
    }

}