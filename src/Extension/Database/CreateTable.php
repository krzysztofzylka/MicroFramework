<?php

namespace Krzysztofzylka\MicroFramework\Extension\Database;

use krzysztofzylka\DatabaseManager\Column;
use krzysztofzylka\DatabaseManager\Enum\ColumnDefault;
use krzysztofzylka\DatabaseManager\Enum\ColumnType;

class CreateTable extends \krzysztofzylka\DatabaseManager\CreateTable
{

    /**
     * Add datetime column
     * @param string $name column name
     * @param bool $null is nullable
     */
    public function addSimpleDateTimeColumn(string $name, bool $null = true) : self {
        $column = (new Column())
            ->setName('date_temp')
            ->setType(ColumnType::datetime)
            ->setDefault(ColumnDefault::currentTimestamp)
            ->setNull($null);

        $this->addColumn($column);

        return $this;
    }

}