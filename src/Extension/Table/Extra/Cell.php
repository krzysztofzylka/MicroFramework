<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table\Extra;

use Krzysztofzylka\MicroFramework\Extension\Html\Html;

/**
 * Cell
 * @package Extension\Table\Extra
 */
class Cell
{

    /**
     * Column value
     * @var mixed
     */
    public mixed $val;

    /**
     * Single row data
     * @var array
     */
    public array $data;

    /**
     * Html generator
     * @var Html
     */
    public Html $html;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->html = new Html();
    }

}