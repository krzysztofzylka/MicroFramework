<?php

use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\CreateTable;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use Krzysztofzylka\MicroFramework\Extension\Database\Updater;

return (new class extends Updater {

    public function run()
    {
        $dateColumn = (new Column())
            ->setName('date')
            ->setType(ColumnType::date)
            ->setNull(false);

        (new CreateTable())
            ->setName('statistic')
            ->addIdColumn()
            ->addColumn($dateColumn)
            ->addSimpleIntColumn('unique')
            ->addSimpleIntColumn('visits')
            ->addDateCreatedColumn()
            ->addDateModifyColumn()
            ->execute();

        (new CreateTable())
            ->setName('statistic_ip')
            ->addIdColumn()
            ->addColumn($dateColumn)
            ->addSimpleVarcharColumn('ip', 128)
            ->addSimpleIntColumn('visits')
            ->addDateCreatedColumn()
            ->addDateModifyColumn()
            ->execute();

        (new CreateTable())
            ->setName('statistic_visits')
            ->addIdColumn()
            ->addSimpleIntColumn('statistic_ip_id')
            ->addSimpleVarcharColumn('country')
            ->addSimpleVarcharColumn('city')
            ->addSimpleVarcharColumn('continent')
            ->addSimpleVarcharColumn('browser')
            ->addSimpleVarcharColumn('page')
            ->addDateCreatedColumn()
            ->addDateModifyColumn()
            ->execute();
    }

});