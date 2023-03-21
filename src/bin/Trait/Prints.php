<?php

namespace Krzysztofzylka\MicroFramework\bin\Trait;

use krzysztofzylka\SimpleLibraries\Library\Date;

trait Prints {

    /**
     * Print data
     * @param string $value
     * @return void
     */
    private function print(string $value): void
    {
        echo $value . PHP_EOL;
    }

    /**
     * Print data and exit
     * @param string $value
     * @return never
     */
    private function dprint(string $value): never
    {
        die($value . PHP_EOL);
    }

    /**
     * Print data with date
     * @param string $value
     * @return void
     */
    private function tprint(string $value): void
    {
        $this->print('[' . Date::getSimpleDate() . '] ' . $value);
    }

    /**
     * Print data with date and exit
     * @param string $value
     * @return void
     */
    private function dtprint(string $value): void
    {
        $this->dprint('[' . Date::getSimpleDate() . '] ' . $value);
    }

}