<?php

use krzysztofzylka\DatabaseManager\CreateTable;

new class extends \Krzysztofzylka\MicroFramework\Extension\Database\Migration
{

    public function run()
    {
        (new CreateTable())
            ->setName('account')
            ->addIdColumn()
            ->addSimpleVarcharColumn('email', 128)
            ->addSimpleVarcharColumn('password', 80)
            ->addDateCreatedColumn()
            ->addDateModifyColumn()
            ->execute();
    }

};