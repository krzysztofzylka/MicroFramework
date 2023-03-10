<?php

use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\CreateTable;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;
use Krzysztofzylka\MicroFramework\Extension\Database\Updater;

return (new class extends Updater {

    public function run() : void {
        $this->table->setName('example');

        try {
            $faker = Faker\Factory::create();

            for ($i=0; $i < 1000; $i++) {
                $this->table->insert([
                    'name' => $faker->name,
                    'status' => $faker->randomElement(['Init', 'Success', 'Fail'])
                ]);
            }
        } catch (Exception $exception) {
            var_dump($exception);

            throw new Exception($exception->getMessage(), $exception->getCode());
        }

        return;
    }

});