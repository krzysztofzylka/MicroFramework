<?php

use Krzysztofzylka\MicroFramework\Extension\Database\CreateTable;
use Krzysztofzylka\MicroFramework\Extension\Database\Updater;

return (new class extends Updater {

    public function run()
    {
        (new CreateTable())
            ->setName('common_file')
            ->addIdColumn()
            ->addSimpleIntColumn('account_id', false)
            ->addSimpleVarcharColumn('name')
            ->addSimpleVarcharColumn('file_path', 2048)
            ->addSimpleVarcharColumn('file_extension')
            ->addSimpleVarcharColumn('file_size')
            ->addSimpleBoolColumn('is_public')
            ->addSimpleBoolColumn('is_temp')
            ->addSimpleDateTimeColumn('date_temp')
            ->addDateCreatedColumn()
            ->addDateModifyColumn()
            ->execute();
    }

});