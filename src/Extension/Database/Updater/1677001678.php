<?php

use krzysztofzylka\DatabaseManager\CreateTable;
use Krzysztofzylka\MicroFramework\Extension\Database\Updater;

return (new class extends Updater {

    public function run()
    {
        (new CreateTable())
            ->setName('account')
            ->addIdColumn()
            ->addUsernameColumn()
            ->addPasswordColumn()
            ->addEmailColumn()
            ->addDateCreatedColumn()
            ->addDateModifyColumn()
            ->execute();
    }

});