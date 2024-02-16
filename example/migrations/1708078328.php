<?php

use krzysztofzylka\DatabaseManager\CreateTable;
use Krzysztofzylka\Generator\Generator;

new class extends \Krzysztofzylka\MicroFramework\Extension\Database\Migration
{

    public function run()
    {
        (new CreateTable())
            ->setName('test')
            ->addIdColumn()
            ->addSimpleVarcharColumn('a', 100)
            ->addSimpleVarcharColumn('b')
            ->addDateCreatedColumn()
            ->addDateModifyColumn()
            ->execute();

        $this->loadModel('test');

        for ($i=0; $i<=600; $i++) {
            $this->Test->setId(null)->save([
                'a' => Generator::uniqId(50),
                'b' => Generator::uniqId(50)
            ]);
        }
    }

};