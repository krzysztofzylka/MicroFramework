<?php

use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\CreateTable;
use krzysztofzylka\DatabaseManager\Enum\ColumnDefault;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use Krzysztofzylka\MicroFramework\Extension\Database\Updater;

return (new class extends Updater {

    public function run(): void
    {
        (new CreateTable())
            ->setName('account_remember_field')
            ->addIdColumn()
            ->addSimpleIntColumn('account_id', false)
            ->addSimpleVarcharColumn('name')
            ->addColumn((new Column())
                ->setName('value')
                ->setType(ColumnType::text)
                ->setNull(true))
            ->addColumn((new Column())
                ->setName('date')
                ->setType(ColumnType::datetime)
                ->setDefault(ColumnDefault::currentTimestamp)
                ->setNull(false))
            ->addDateCreatedColumn()
            ->addDateModifyColumn()
            ->execute();

        return;
    }

});