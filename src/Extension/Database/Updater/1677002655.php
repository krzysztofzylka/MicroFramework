<?php

use krzysztofzylka\DatabaseManager\AlterTable;
use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use Krzysztofzylka\MicroFramework\Extension\Database\Updater;

return (new class extends Updater {

    public function run(): void
    {
        $adminColumn = new Column();
        $adminColumn->setName('admin');
        $adminColumn->setType(ColumnType::tinyint, 1);
        $adminColumn->setDefault(0);

        $alterTable = new AlterTable('account');
        $alterTable->addColumn($adminColumn, 'email');
        $alterTable->execute();
    }

});