<?php

namespace Krzysztofzylka\MicroFramework\bin\Action;

use Exception;
use config\Config;
use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\CreateTable;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use krzysztofzylka\DatabaseManager\Exception\InsertException;
use krzysztofzylka\DatabaseManager\Exception\UpdateException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\bin\Console\Console;
use Krzysztofzylka\MicroFramework\bin\Trait\Prints;
use Krzysztofzylka\MicroFramework\Exception\DatabaseException;
use Krzysztofzylka\MicroFramework\Extension\Database\Enum\UpdateStatus;
use Krzysztofzylka\MicroFramework\Kernel;

class DatabaseUpdate
{

    use Prints;

    /**
     * Console object
     * @var Console
     */
    private Console $console;

    /**
     * Database updater path
     * @var string
     */
    public string $databaseUpdaterPath;

    /**
     * Updater table
     * @var Table
     * @ignore
     */
    public Table $updateTable;

    /**
     * Init project
     * @param Console $console
     */
    public function __construct(Console $console)
    {
        $this->console = $console;
        $this->databaseUpdaterPath = $this->console->path . '/database_updater';

        $this->tprint('Start update database');

        try {
            Kernel::initPaths($this->console->path);
            Kernel::autoload();
            Kernel::setConfig(new Config());
            Kernel::configDatabaseConnect();
            $this->updateTable = (new Table())->setName('database_updater');
        } catch (DatabaseManagerException $exception) {
            $this->dtprint('Database fail: ' . $exception->getHiddenMessage());
        } catch (DatabaseException $exception) {
            $this->dtprint('Database fail: ' . $exception->getHiddenMessage());
        } catch (Exception $exception) {
            $this->dtprint('Database fail: ' . $exception->getMessage());
        }

        $this->tprint('Init update table');
        $this->initUpdateTable();
        $this->tprint('Scan directory "' . $this->databaseUpdaterPath . '"');

        $databaseFiles = $this->globPath();

        foreach ($databaseFiles as $databaseFile) {
            if (!$this->updateTable->findIsset(['name' => $databaseFile['name']])) {
                $this->installScript($databaseFile);
            }
        }

        $this->dtprint('End update database');
    }

    /**
     * Init main updater table
     * @return void
     */
    private function initUpdateTable(): void
    {
        if (!$this->updateTable->exists()) {
            (new CreateTable())
                ->setName('database_updater')
                ->addIdColumn()
                ->addSimpleVarcharColumn('name', 255, false)
                ->addColumn(
                    (new Column())
                        ->setName('status')
                        ->setType(ColumnType::enum, ['Init', 'Success', 'Fail'])
                        ->setDefault('Init')
                        ->setNull(false)
                )
                ->addDateCreatedColumn()
                ->addDateModifyColumn()
                ->execute();

            $this->tprint('Success create table');
        } else {
            $this->tprint('Table already exists');
        }
    }

    /**
     * Global updater path
     * @return array
     */
    private function globPath(): array
    {
        $return = [];
        $updateFiles = glob(__DIR__ . '/../../Extension/Database/Updater/*.php');
        $updateFiles = array_merge($updateFiles, glob($this->databaseUpdaterPath . '/*.php'));

        foreach ($updateFiles as $path) {
            $return[] = [
                'path' => realpath($path),
                'name' => str_replace('.' . pathinfo($path, PATHINFO_EXTENSION), '', basename($path))
            ];
        }

        return $return;
    }

    /**
     * Install script
     * @param array $databaseFile
     * @return void
     * @throws InsertException
     * @throws UpdateException
     */
    private function installScript(array $databaseFile): void
    {
        $this->tprint('Run update file: ' . $databaseFile['name']);
        $this->updateTable->insert(['name' => $databaseFile['name']]);

        try {
            include($databaseFile['path']);

            $this->updateTable->updateValue('status', UpdateStatus::Success->value);
        } catch (Exception) {
            $this->updateTable->updateValue('status', UpdateStatus::Fail->value);

            $this->dtprint('Fail update file: ' . $databaseFile['path']);
        }
    }

}