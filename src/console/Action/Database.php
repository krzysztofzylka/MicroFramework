<?php

namespace Krzysztofzylka\MicroFramework\console\Action;

use Exception;
use krzysztofzylka\DatabaseManager\AlterTable;
use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\CreateTable;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use krzysztofzylka\DatabaseManager\Exception\ConditionException;
use krzysztofzylka\DatabaseManager\Exception\CreateTableException;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use krzysztofzylka\DatabaseManager\Exception\InsertException;
use krzysztofzylka\DatabaseManager\Exception\SelectException;
use krzysztofzylka\DatabaseManager\Exception\TableException;
use krzysztofzylka\DatabaseManager\Exception\UpdateException;
use krzysztofzylka\DatabaseManager\Exception\UpdateTableException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Extension\Database\Enum\UpdateStatus;
use krzysztofzylka\SimpleLibraries\Library\Console\Prints;

class Database
{

    use \Krzysztofzylka\MicroFramework\console\Trait\Database;

    /**
     * Console object
     */
    private $console;

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
     * @param $console
     * @param string|null $action
     * @throws ConditionException
     * @throws CreateTableException
     * @throws InsertException
     * @throws SelectException
     * @throws TableException
     * @throws UpdateException
     * @throws UpdateTableException
     */
    public function __construct($console, ?string $action = null)
    {
        $this->console = $console;
        $this->databaseConnect($this->console->path);
        $this->updateTable = (new Table())->setName('database_updater');

        switch ($action ?? $console->arg[2] ?? false) {
            case 'update':
                $this->update();

                break;
            case 'update_info':
                try {
                    $table = new \krzysztofzylka\SimpleLibraries\Library\Console\Generator\Table();
                    $table->addColumn('Id', 'id');
                    $table->addColumn('Name', 'name');
                    $table->addColumn('Status', 'status');
                    $table->addColumn('Created', 'date_created');
                    $table->addColumn('Modify', 'date_modify');
                    $table->setData(array_column($this->updateTable->findAll(), 'database_updater'));
                    $table->render();
                    exit;
                } catch (Exception $exception) {
                    Prints::print($exception->getMessage(), false, true);
                }

                break;
            case 'debug_list':
                $this->debug_list();

                break;
            default:
                Prints::print('Action not found', false, true);

                break;
        }
    }

    /**
     * Update database
     * @return void
     * @throws ConditionException
     * @throws CreateTableException
     * @throws InsertException
     * @throws SelectException
     * @throws TableException
     * @throws UpdateException
     * @throws UpdateTableException
     */
    public function update(): void
    {
        $this->databaseUpdaterPath = $this->console->path . '/database_updater';

        Prints::print('Start update database', true);
        Prints::print('Init update table', true);
        $this->initUpdateTable();
        Prints::print('Scan directory "' . $this->databaseUpdaterPath . '"', true);

        $databaseFiles = $this->globPath();

        foreach ($databaseFiles as $databaseFile) {
            if (!$this->updateTable->findIsset(['name' => $databaseFile['name']])) {
                $this->installScript($databaseFile);
            }
        }

        Prints::print('End update database', true, true);
    }

    /**
     * Init main updater table
     * @return void
     * @throws CreateTableException
     * @throws TableException
     * @throws UpdateTableException
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

            Prints::print('Success create table', true);
        } else {
            Prints::print('Table already exists', true);
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
        $return = $this->globDirectory(__DIR__ . '/../../Extension/Database/Updater/');
        return array_merge($return, $this->globDirectory($this->databaseUpdaterPath));
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
        Prints::print('Run update file: ' . $databaseFile['name'], true);
        $this->updateTable->insert(['name' => $databaseFile['name']]);

        try {
            if ($databaseFile['type'] === 'php') {
                include($databaseFile['path']);
            } elseif ($databaseFile['type'] === 'sql') {
                $sql = file_get_contents($databaseFile['path']);
                $this->updateTable->query($sql);
            }

            $this->updateTable->updateValue('status', UpdateStatus::Success->value);
        } catch (DatabaseManagerException $exception) {
            $this->updateTable->updateValue('status', UpdateStatus::Fail->value);
            $this->updateTable->updateValue('error', $exception->getHiddenMessage());

            $this->dtprint('Fail update file: ' . $databaseFile['path']);
        } catch (Exception $exception) {
            $this->updateTable->updateValue('status', UpdateStatus::Fail->value);
            $this->updateTable->updateValue('error', $exception->getMessage());

            Prints::print('Fail update file: ' . $databaseFile['path'], true, true);
        }
    }

    /**
     * Debug list
     * @return void
     */
    private function debug_list(): void
    {
        $this->databaseUpdaterPath = $this->console->path . '/database_updater';

        $table = new \krzysztofzylka\SimpleLibraries\Library\Console\Generator\Table();
        $table->addColumn('Name', 'name');
        $table->addColumn('Type', 'type');
        $table->addColumn('Path', 'path');
        $table->setData($this->globPath());
        $table->render();
    }

    /**
     * Glob directory with subdirectory
     * @param string $directoryPath
     * @return array
     */
    private function globDirectory(string $directoryPath): array
    {
        $return = [];

        $arrays = glob($directoryPath . '/{*,*/*,*/*/*,*/*/*/*}.{php,sql}', GLOB_BRACE);

        foreach ($arrays as $path) {
            $name = str_replace('.' . pathinfo($path, PATHINFO_EXTENSION), '', basename($path));

            $return[$name] = [
                'path' => realpath($path),
                'name' => $name,
                'type' => pathinfo($path, PATHINFO_EXTENSION)
            ];
        }

        array_multisort(array_column($return, "name"), SORT_ASC, $return);

        return $return;
    }

}