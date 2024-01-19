<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table;

use Krzysztofzylka\MicroFramework\Extension\Table\Helper\Html;

/**
 * Table cell
 */
class Cell
{

    /**
     * Value
     * @var string
     */
    public string $value;

    /**
     * Data
     * @var array
     */
    public array $data;

    /**
     * Html helper
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