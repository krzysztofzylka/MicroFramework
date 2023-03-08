<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table;

use Krzysztofzylka\MicroFramework\Extension\Table\Extra\Cell;
use Krzysztofzylka\MicroFramework\Model;
use Krzysztofzylka\MicroFramework\Trait\Log;

class Table
{

    use Log;

    /**
     * Html table
     * @var string
     */
    private string $html = '';

    /**
     * Model to get data
     * @var ?Model
     */
    public ?Model $model = null;

    /**
     * Table columns:
     * [
     *   'key' => [ //key to database data in model eg. id for $result['id'] or user.name for $result['user']['name']
     *     'title' => 'Column title', //column title
     *     'attribute' => [], //html attribute eg. ['css' => 'color: red']
     *     'width' => 500, //column width
     *     'value' => '', //custom value
     *     'value' => function ($cell) {
     *       return $cell->val;
     *     }, //function custom value
     *     'maxLength' => 90 //max length
     *   ],
     *   ...
     * ]
     * @var array
     */
    public array $columns = [];

    /**
     * Data results for body
     * @var array
     */
    public array $results = [];

    /**
     * Render table
     * @return string
     */
    public function render(): string
    {
        $this->generateHeaders();

        if (empty($this->results) && isset($this->model)) {
            $this->results = $this->model->findAll();
        }

        $this->generateBody();

        return '<table class="table table-sm">' . $this->html . '</table>';
    }

    /**
     * Generate headers
     * @return void
     */
    private function generateHeaders(): void
    {
        $this->html .= '<thead><tr>';

        foreach ($this->columns as $column) {
            $this->html .= '<th>' . ($column['title'] ?? '') . '</th>';
        }

        $this->html .= '</tr></thead>';
    }

    /**
     * Generate body
     * @return void
     */
    private function generateBody(): void
    {
        $this->html .= '<tbody>';

        var_dump($this->results);

        foreach ($this->results as $result) {
            $this->html .= '<tr>';

            foreach ($this->columns as $column) {
                $cell = new Cell();
                $cell->val = $column['value'];
            }

            $this->html .= '</tr>';
        }

        $this->html .= '</tbody>';
    }

}