<?php

use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\CreateTable;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use Krzysztofzylka\MicroFramework\Extension\Database\Updater;

return (new class extends Updater {

    public function run() : void {
        (new CreateTable())
            ->setName('example')
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

});