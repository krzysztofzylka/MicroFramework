<?php

namespace Krzysztofzylka\MicroFramework\Extension;

use Krzysztofzylka\MicroFramework\Libs\Table\Table;

class Loader
{

    /**
     * Load table
     * @return Table
     */
    public function table(): Table
    {
        return new Table();
    }

}