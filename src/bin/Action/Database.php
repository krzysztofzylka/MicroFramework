<?php

namespace Krzysztofzylka\MicroFramework\bin\Action;

use Exception;
use jc21\CliTable;
use krzysztofzylka\DatabaseManager\AlterTable;
use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\CreateTable;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use krzysztofzylka\DatabaseManager\Exception\ConditionException;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use krzysztofzylka\DatabaseManager\Exception\InsertException;
use krzysztofzylka\DatabaseManager\Exception\SelectException;
use krzysztofzylka\DatabaseManager\Exception\UpdateException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\bin\Console\Console;
use Krzysztofzylka\MicroFramework\bin\Trait\Prints;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Extension\Database\Enum\UpdateStatus;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;

class Database
{

    use Prints;
    use \Krzysztofzylka\MicroFramework\bin\Trait\Database;

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
    public function __construct(Console $console, ?string $action = null)
    {
        $this->console = $console;
        $this->databaseConnect($this->console->path);
        $this->updateTable = (new Table())->setName('database_updater');

        switch ($action ?? $console->arg[2] ?? false) {
            case 'update':
                $this->update();

                break;
            case 'update_info':
                $table = new CliTable();
                $table->addField('Id', 'id');
                $table->addField('Name', 'name');
                $table->addField('Status', 'status', false, 'green');
                $table->addField('Created', 'date_created', false, 'blue');
                $table->addField('Modify', 'date_modify', false, 'yellow');
                $table->injectData(array_column($this->updateTable->findAll(), 'database_updater'));
                $table->display();

                break;
            default:
                $this->dprint('Action not found');

                break;
        }
    }

    /**
     * Update database
     * @return void
     * @throws InsertException
     * @throws UpdateException
     * @throws NotFoundException
     * @throws ConditionException
     * @throws SelectException
     * @throws SimpleLibraryException
     */
    public function update(): void
    {
        $this->databaseUpdaterPath = $this->console->path . '/database_updater';

        $this->tprint('Start update database');
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

        $columns = array_column((new Table('database_updater'))->columnList(), 'Field');

        if (!in_array('error', $columns)) {
            (new AlterTable('database_updater'))->addColumn(
                (new Column('error'))->setType(ColumnType::text),
                'status'
            )->execute();
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
        } catch (DatabaseManagerException $exception) {
            $this->updateTable->updateValue('status', UpdateStatus::Fail->value);
            $this->updateTable->updateValue('error', $exception->getHiddenMessage());

            $this->dtprint('Fail update file: ' . $databaseFile['path']);
        } catch (Exception $exception) {
            $this->updateTable->updateValue('status', UpdateStatus::Fail->value);
            $this->updateTable->updateValue('error', $exception->getMessage());

            $this->dtprint('Fail update file: ' . $databaseFile['path']);
        }
    }

}