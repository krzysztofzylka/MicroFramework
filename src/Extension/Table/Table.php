<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table;

use krzysztofzylka\DatabaseManager\Condition;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\DatabaseException;
use Krzysztofzylka\MicroFramework\Extension\Table\Extra\Cell;
use Krzysztofzylka\MicroFramework\Model;
use Krzysztofzylka\MicroFramework\Trait\Log;
use krzysztofzylka\SimpleLibraries\Library\Session;

class Table
{

    use Log;

    /**
     * Table ID
     * @var string
     */
    private string $id = '';

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
     * Controller
     * @var Controller
     */
    public Controller $controller;

    /**
     * Conditions
     * @var Condition
     */
    private Condition $conditions;

    /**
     * Have conditions
     * @var bool
     */
    private bool $haveCondition = false;

    /**
     * Table columns:
     * [
     *   'key' => [ //key to database data in model eg. id for $result['id'] or user.name for $result['user']['name']
     *     'title' => 'Column title', //column title
     *     'value' => '', //custom value
     *     'value' => function ($cell) {
     *       return $cell->val;
     *     }, //function custom value
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
     * Search string
     * @var string
     */
    public string $search = '';

    /**
     * Active search
     * @var bool
     */
    public bool $activeSearch = true;

    /**
     * Post data
     * @var ?array
     */
    public ?array $data = null;

    /**
     * Session
     * @var mixed
     */
    private mixed $session = null;

    /**
     * Render table
     * @return string
     * @throws DatabaseException
     */
    public function render(): string
    {
        $this->generateDefaultData();
        $this->query();
        $this->getResults();

        $this->html .= '<div class="tableRender" id="' . $this->id . '">';
        $this->renderAction();
        $this->renderHeaders();

        $this->renderBody();

        $this->html .= '</div>';

        $html = '<table class="table table-sm">' . $this->html . '</table>';

        return $html;
    }

    /**
     * Set table ID
     * @param string $id
     * @return void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * Render headers
     * @return void
     */
    private function renderHeaders(): void
    {
        $this->html .= '<thead><tr>';

        foreach ($this->columns as $column) {
            $this->html .= '<th>' . ($column['title'] ?? '') . '</th>';
        }

        $this->html .= '</tr></thead>';
    }

    /**
     * Render body
     * @return void
     */
    private function renderBody(): void
    {
        $this->html .= '<tbody>';

        foreach ($this->results as $result) {
            $this->html .= '<tr>';

            foreach ($this->columns as $columnKey => $column) {
                $cell = new Cell();
                $cell->val = $this->getArrayData($columnKey, $result);
                $cell->data = $result;
                $this->html .= '<td>';

                if (isset($column['value']) && is_string($column['value'])) {
                    $this->html .= $column['value'];
                } elseif (isset($column['value']) && is_object($column['value'])) {
                    $this->html .= $column['value']($cell);
                } else {
                    $this->html .= $cell->val;
                }

                $this->html .= '</td>';
            }

            $this->html .= '</tr>';
        }

        $this->html .= '</tbody>';
    }

    /**
     * Render actions
     * @return void
     */
    private function renderAction(): void
    {
        $this->html .= '<div class="actions float-end">';

        if ($this->activeSearch) {
            $this->html .= '<form method="POST"><input type="hidden" name="table_id" value="' . $this->id . '" /><input name="search" class="form-control" placeholder="Search..." value="' . $this->search . '" /></form>';
        }

        $this->html .= '</div>';
    }

    /**
     * Get data from array
     * @param string $name
     * @param array $data
     * @return mixed
     */
    private function getArrayData(string $name, array $data): mixed
    {
        $arrayData = '$data[\'' . implode('\'][\'', explode('.', $name)) . '\']';

        return @eval("return $arrayData;");
    }

    /**
     * Generate default data
     * @return void
     */
    private function generateDefaultData(): void
    {
        if (empty($this->id)) {
            $id = $this->controller->name . '-' . $this->controller->method;

            if (!empty($this->controller->arguments)) {
                $id .= '-' . implode('-', $this->controller->arguments );
            }

            $this->setId($id);
        }

        $this->conditions = new Condition();
    }

    /**
     * Query
     * @return void
     */
    private function query(): void
    {
        $this->getSession();

        $this->search = (isset($this->session['search']) && !isset($this->data['search'])) ? $this->session['search'] : $this->data['search'];

        if ($this->activeSearch && $this->search) {
            $this->haveCondition = true;
            $orCondition = new Condition();

            foreach (array_keys($this->columns) as $field) {
                $orCondition->where($field, '%' . $this->search . '%', 'LIKE');
            }

            $this->conditions->orWhere($orCondition);
        }

        $this->saveQuery();
    }

    /**
     * Generate results
     * @return void
     * @throws DatabaseException
     */
    private function getResults(): void
    {
        if (empty($this->results) && isset($this->model)) {
            if ($this->haveCondition) {
                $this->results = $this->model->findAll($this->conditions);
            } else {
                $this->results = $this->model->findAll();
            }
        }
    }

    /**
     * Save query
     * @return void
     */
    private function saveQuery(): void
    {
        if (!isset($this->data['table_id']) || $this->data['table_id'] !== $this->id) {
            return;
        }

        $this->saveSession([
            'search' => $this->data['search'] ?? null
        ]);
    }

    /**
     * Get saved session
     * @return mixed
     */
    private function getSession(): mixed
    {
        if (!$this->session) {
            $this->session = Session::get('table_' . $this->id . '_parameters');
        }

        return $this->session;
    }

    /**
     * Save session data
     * @param array $data
     * @return void
     */
    private function saveSession(array $data): void
    {
        Session::set('table_' . $this->id . '_parameters', $data);
    }

}