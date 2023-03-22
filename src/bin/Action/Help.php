<?php

namespace Krzysztofzylka\MicroFramework\bin\Action;

use Krzysztofzylka\MicroFramework\bin\Console\Console;
use Krzysztofzylka\MicroFramework\bin\Trait\Prints;

class Help {

    use Prints;

    /**
     * help
     */
    public function __construct()
    {
        $this->print('Help');
        $this->print('init                                          - Initialize project');
        $this->print('update                                        - Update project');
        $this->print('database update                               - Update database');
        $this->print('user create <login> <password> <is admin>     - Create user');
    }

}