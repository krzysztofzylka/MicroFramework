<?php

namespace Krzysztofzylka\MicroFramework\Executable;

use Exception;
use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\Condition;
use krzysztofzylka\DatabaseManager\CreateTable;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use krzysztofzylka\DatabaseManager\Exception\CreateTableException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Extension\Database\Enum\UpdateStatus;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\Trait\Log;

class DatabaseUpdater {

    use Log;

    /**
     * Updater table
     * @var Table
     * @ignore
     */
    public Table $updateTable;

    /**
     * Init updater
     * @throws CreateTableException
     */
    public function __construct() {
        $this->updateTable = (new Table())->setName('database_updater');

        if (!$this->updateTable->exists()) {
            (new CreateTable())
                ->setName('database_updater')
                ->addIdColumn()
                ->addColumn(
                    (new Column())
                        ->setName('name')
                        ->setType(ColumnType::varchar, 255)
                        ->setNull(false)
                )
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
        }
    }

    /**
     * Run script
     * @return void
     */
    public function run() : void {
        try {
            $updateFiles = glob(Kernel::getPath('database_updater') . '/*.php');

            foreach ($updateFiles as $filePath) {
                $name = str_replace('.' . pathinfo($filePath, PATHINFO_EXTENSION), '', basename($filePath));

                if (!$this->updateTable->findIsset((new Condition())->where('name', $name))) {
                    $this->updateTable->insert(['name' => $name]);

                    try {
                        include($filePath);

                        $this->updateTable->updateValue('status', UpdateStatus::Success->value);
                    } catch (Exception $exception) {
                        $this->updateTable->updateValue('status', UpdateStatus::Fail->value);

                        $this->log(
                            'Database update fail',
                            'ERROR',
                            [
                                'name' => $name,
                                'filePath' => $filePath,
                                'exception' => $exception
                            ]
                        );
                    }
                }
            }
        } catch (Exception $exception) {
            $this->log('Database init fail', 'ERROR', ['exception' => $exception]);
        }
    }

}