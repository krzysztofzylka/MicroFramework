<?php

namespace Krzysztofzylka\MicroFramework\bin\Trait;

use Krzysztofzylka\MicroFramework\bin\Console\Console;
use krzysztofzylka\SimpleLibraries\Library\Date;

trait Prints
{

    /**
     * Print data
     * @param string $value
     * @return void
     */
    private function print(string $value): void
    {
        \krzysztofzylka\SimpleLibraries\Library\Console\Prints::print($value);
    }

    /**
     * Print data and exit
     * @param string $value
     * @return never
     */
    private function dprint(string $value): void
    {
        \krzysztofzylka\SimpleLibraries\Library\Console\Prints::print($value, false, true);
    }

    /**
     * Print data with date
     * @param string $value
     * @return void
     */
    private function tprint(string $value): void
    {
        \krzysztofzylka\SimpleLibraries\Library\Console\Prints::print($value, true);
    }

    /**
     * Print data with date and exit
     * @param string $value
     * @return void
     */
    private function dtprint(string $value): void
    {
        \krzysztofzylka\SimpleLibraries\Library\Console\Prints::print($value, true, true);
    }

}