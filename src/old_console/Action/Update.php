<?php

namespace Krzysztofzylka\MicroFramework\bin\Action;

use Krzysztofzylka\MicroFramework\bin\Console\Console;
use Krzysztofzylka\MicroFramework\bin\Trait\Prints;

class Update
{

    use Prints;

    /**
     * update project
     * @param Console $console
     */
    public function __construct(Console $console)
    {
        $this->console = $console;

        $this->tprint('Start update');
        Console::$disableDiePrint = true;
        $this->tprint('Update directory and files');
        new Init($this->console);
        $this->tprint('Update database');
        new Database($this->console, 'update');
        Console::$disableDiePrint = false;
        $this->dtprint('End update');

    }

}