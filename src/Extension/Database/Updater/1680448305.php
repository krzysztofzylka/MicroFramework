<?php

use krzysztofzylka\DatabaseManager\AlterTable;
use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use Krzysztofzylka\MicroFramework\Extension\Database\Updater;

return (new class extends Updater {

    public function run()
    {
        (new AlterTable('account'))
            ->addColumn((new Column())
                ->setName('api_key')
                ->setType(ColumnType::varchar, 256)
                ->setNull(true),
                'admin'
            )->execute();
    }

});