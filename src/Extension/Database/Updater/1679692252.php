<?php

use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\CreateTable;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use Krzysztofzylka\MicroFramework\Extension\Database\Updater;
use const krzysztofzylka\DatabaseManager\Enum\text;

return (new class extends Updater {

    public function run()
    {
        (new CreateTable())
            ->setName('cron_scheduled')
            ->addIdColumn()
            ->addSimpleVarcharColumn('time')
            ->addSimpleVarcharColumn('model')
            ->addSimpleVarcharColumn('method')
            ->addColumn((new Column())->setName('args')->setType(ColumnType::text)->setNull(true))
            ->addDateCreatedColumn()
            ->addDateModifyColumn()
            ->execute();
    }

});