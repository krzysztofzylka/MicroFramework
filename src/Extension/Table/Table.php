<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table;

use krzysztofzylka\DatabaseManager\Condition;
use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\DatabaseException;
use Krzysztofzylka\MicroFramework\Extension\Table\Trait\Render;
use Krzysztofzylka\MicroFramework\Extension\Table\Trait\Session;
use Krzysztofzylka\MicroFramework\Model;
use Krzysztofzylka\MicroFramework\Trait\Log;

class Table
{

    use Log;
    use Session;
    use Render;

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
     * Table columns:
     * [
     *   'key' => [ //key to database data in model eg. id for $result['id'] or user.name for $result['user']['name']
     *     'title' => 'Column title', //column title
     *     'width' => 200, //column width
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
     * Enable search
     * @var bool
     */
    public bool $activeSearch = true;
    /**
     * Post data
     * @var ?array
     */
    public ?array $data = null;
    /**
     * Enable pagination
     * @var bool
     */
    public bool $activePagination = true;
    /**
     * Elements per page
     * @var int
     */
    public int $paginationLimit = 20;
    /**
     * Actual page
     * @var int
     */
    public int $page = 1;
    /**
     * Pages
     * @var ?int
     */
    public ?int $pages = null;
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
     * Conditions
     * @var ?Condition
     */
    private ?array $conditions = [];
    /**
     * Have conditions
     * @var bool
     */
    private bool $haveCondition = false;
    /**
     * Session
     * @var mixed
     */
    private mixed $session = null;
    /**
     * SQL limit
     * @var ?string
     */
    private ?string $limit = null;

    /**
     * Render table
     * @return string
     * @throws DatabaseException
     */
    public function render(): string
    {
        $this->generateDefaultData();
        $this->session();
        $this->getResults();

        $this->html .= '<div class="tableRender" id="' . $this->id . '">';
        $this->renderAction();
        $this->html .= '<table class="table table-sm">';
        $this->renderHeaders();
        $this->renderBody();
        $this->html .= '</table>';
        $this->renderFooter();
        $this->html .= '</div>';

        return $this->html;
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
                $id .= '-' . implode('-', $this->controller->arguments);
            }

            $this->setId($id);
        }
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
     * Session
     * @return void
     * @throws DatabaseException
     */
    private function session(): void
    {
        $this->getSession();

        if (isset($this->session['search']) || isset($this->data['search'])) {
            $this->search = !is_null($this->session) && isset($this->session['search']) && !isset($this->data['search'])
                ? $this->session['search']
                : $this->data['search'] ?? '';
        }

        if (isset($this->model) && $this->activeSearch && $this->search) {
            $this->haveCondition = true;

            foreach (array_keys($this->columns) as $field) {
                $this->conditions['OR'][] = new Condition($field, 'LIKE', '%' . $this->search . '%');
            }
        }

        if (isset($this->session['page'])) {
            $this->page = (int)$this->session['page'];
        }

        if (isset($this->model)) {
            $conditions = $this->haveCondition ? $this->conditions : null;
            $this->pages = floor($this->model->findCount($conditions) / $this->paginationLimit);

            if ($this->activePagination) {
                if (isset($this->session['page']) || isset($this->data['page'])) {
                    if (isset($this->data['page'])) {
                        if ($this->data['page'] === "«") {
                            $this->page--;
                        } elseif ($this->data['page'] === "»") {
                            $this->page++;
                        } else {
                            $this->page = (int)$this->data['page'];
                        }
                    }
                }

                if ($this->page < 1) {
                    $this->page = 1;
                } elseif ($this->page > $this->pages) {
                    $this->page = $this->pages;
                }

                $page = ($this->page - 1);

                if ($this->page < 1) {
                    $page = 0;
                }

                $this->limit = ($page * $this->paginationLimit) . ',' . $this->paginationLimit;
            }
        }

        if (isset($this->data['table_id']) && $this->data['table_id'] === $this->id) {
            $this->saveSession([
                'search' => $this->data['search'] ?? $this->session['search'] ?? null,
                'page' => $this->page ?? 0
            ]);
        }
    }

    /**
     * Generate results
     * @return void
     * @throws DatabaseException
     */
    private function getResults(): void
    {
        if (empty($this->results) && isset($this->model)) {
            if (!$this->haveCondition) {
                $this->conditions = null;
            }

            $this->results = $this->model->findAll($this->conditions, null, $this->limit);
        }
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
     * Add custom query
     * @param array $conditions
     * @return void
     */
    public function query(array $conditions): void
    {
        if (empty($conditions)) {
            return;
        }

        $this->haveCondition = true;
        $this->conditions = array_merge($this->conditions, $conditions);
    }

}