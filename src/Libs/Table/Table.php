<?php

namespace Krzysztofzylka\MicroFramework\Libs\Table;

use Exception;
use krzysztofzylka\DatabaseManager\Condition;
use Krzysztofzylka\Generator\Generator;
use Krzysztofzylka\HtmlGenerator\HtmlGenerator;
use Krzysztofzylka\MicroFramework\Exception\HiddenException;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Libs\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Libs\Log\Log;
use Krzysztofzylka\MicroFramework\Libs\Table\Helper\RenderFooter;
use Krzysztofzylka\MicroFramework\Libs\Table\Helper\RenderHeader;
use Krzysztofzylka\MicroFramework\Libs\Table\Helper\RenderTable;
use Krzysztofzylka\MicroFramework\Libs\Table\Helper\TableReminder;
use Krzysztofzylka\MicroFramework\Model;
use Krzysztofzylka\MicroFramework\View;
use Krzysztofzylka\Request\Request;
use Throwable;

/**
 * Table
 */
class Table
{

    /**
     * Conditions
     * @var array
     */
    public array $conditions = [];
    /**
     * Table id
     * @var string|null
     */
    protected ?string $id = null;

    /**
     * Table actions
     * @var ?array
     */
    protected ?array $actions = null;

    /**
     * Columns
     * @var array
     */
    protected array $columns = [];

    /**
     * Table data
     * @var array
     */
    protected array $data = [];

    /**
     * Page limit
     * @var int
     */
    protected int $pageLimit = 10;

    /**
     * Actual page
     * @var int
     */
    protected int $page = 1;

    /**
     * Pages
     * @var int
     */
    protected ?int $pages = null;

    /**
     * Is ajax action
     * @var bool
     */
    protected bool $isAjaxAction = false;

    /**
     * Search data
     * @var string|null
     */
    protected ?string $search = null;

    /**
     * Slim table
     * @var bool
     */
    protected bool $slim = true;

    /**
     * Model
     * @var Model|null
     */
    protected ?Model $model = null;

    /**
     * order by
     * @var ?string
     */
    protected ?string $orderBy = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $data = TableReminder::getData($this);

        if (isset($data['page'])) {
            $this->page = $data['page'];
        }

        if (isset($data['search'])) {
            $this->search = $data['search'];
        }
    }

    /**
     * Get data
     * @param bool $full
     * @return array
     * @throws HiddenException
     */
    public function getData(bool $full = true): array
    {
        if (!is_null($this->search)) {
            $this->setData($this->search($this->data, $this->search));
        }

        if (!is_null($this->getModel())) {
            $this->setData($this->getModel()->findAll(
                $this->getConditions(),
                null,
                $this->getOrderBy(),
                $this->generateLimit()
            ));
        } elseif (!$full) {
            return array_slice($this->data, (($this->page - 1) * $this->pageLimit), $this->pageLimit);
        }

        return $this->data;
    }

    /**
     * Set data
     * @param array $data
     * @return void
     * @throws HiddenException
     */
    public function setData(array $data): void
    {
        $this->data = $data;

        $this->setPages(ceil($this->getDataCount() / $this->pageLimit));
    }

    /**
     * Search method
     * @param array $data
     * @param string $search
     * @return array
     * @throws Throwable
     */
    protected function search(array $data, string $search): array
    {
        if (!is_null($this->getModel())) {
            foreach (array_keys($this->getColumns()) as $columnKey) {
                if (str_contains($columnKey, '.')) {
                    $this->conditions['OR'][] = new Condition($columnKey, 'LIKE', '%' . htmlspecialchars($this->getSearch()) . '%');
                }
            }

            return [];
        }

        $matches = [];
        $regex = '/' . preg_quote($search, '/') . '/i';

        foreach ($data as $row) {
            foreach ($row as $value) {
                try {
                    if (is_string($value) && preg_match($regex, $value)) {
                        $matches[] = $row;

                        break;
                    }
                } catch (Throwable $throwable) {
                    DebugBar::addThrowable($throwable);
                    Log::log('Table search fail', 'INFO', ['value' => $value, 'regex' => $regex, 'search' => $search]);
                    TableReminder::clear($this);

                    throw $throwable;
                }
            }
        }


        return $matches;
    }

    /**
     * Get model
     * @return Model|null
     */
    public function getModel(): ?Model
    {
        return $this->model;
    }

    /**
     * Set model
     * @param Model|null $model
     * @return void
     */
    public function setModel(?Model $model): void
    {
        $this->model = $model;
    }

    /**
     * Get columns
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get search
     * @return string|null
     */
    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * Set search
     * @param string|null $search
     * @return void
     */
    public function setSearch(?string $search): void
    {
        if (!is_null($search) && empty($search)) {
            $search = null;
        }

        $this->search = $search;

        TableReminder::saveData($this, ['search' => $this->search]);
    }

    /**
     * Get conditions
     * @return array
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * Generate limit
     * @return string
     */
    protected function generateLimit(): string
    {
        return (($this->getPage() - 1) * $this->getPageLimit()) . ', ' . $this->getPageLimit();
    }

    /**
     * Get actual page
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Set actual page
     * @param int $page
     * @return void
     * @throws HiddenException
     */
    public function setPage(int $page): void
    {
        if ($page > $this->getPages()) {
            $page = $this->getPages();
        }

        $this->page = max($page, 1);

        TableReminder::saveData($this, ['page' => $this->page]);
    }

    /**
     * Get page limit
     * @return int
     */
    public function getPageLimit(): int
    {
        return $this->pageLimit;
    }

    /**
     * Set page limit
     * @param int $pageLimit
     * @return void
     */
    public function setPageLimit(int $pageLimit): void
    {
        $this->pageLimit = $pageLimit;
    }

    /**
     * Get pages count
     * @return int
     * @throws HiddenException
     */
    public function getPages(): int
    {
        if (is_null($this->pages)) {
            $this->setPages(ceil($this->getDataCount() / $this->pageLimit));
        }

        return $this->pages ?? 1;
    }

    /**
     * Set pages count
     * @param int $pages
     * @return void
     */
    public function setPages(int $pages): void
    {
        $this->pages = max($pages, 1);

        if ($this->getPage() > $this->pages) {
            $this->setPage($this->pages);
        }
    }

    /**
     * Get data count
     * @return int
     * @throws HiddenException
     */
    public function getDataCount(): int
    {
        if (!is_null($this->getModel())) {
            return $this->getModel()->findCount($this->getConditions());
        }

        return count($this->data);
    }

    /**
     * Generate table
     * @return string
     * @throws MicroFrameworkException
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Render table
     * @return string
     * @throws MicroFrameworkException
     * @throws Exception
     */
    public function render(): string
    {
        $debugId = Generator::uniqId();
        DebugBar::timeStart($debugId, 'Render table');
        $this->ajaxAction();

        try {
            $tableContent = HtmlGenerator::createTag(
                'div',
                (new RenderHeader($this))->render()
                . (new RenderTable($this))->render()
                . (new RenderFooter($this))->render(),
                'bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden overflow-x-auto',
                [
                    'id' => $this->getId()
                ]
            );

            if ($this->isAjaxAction) {
                ob_clean();
                echo $tableContent;
                exit;
            }

            DebugBar::addFrameworkMessage([
                'id' => $this->getId(),
                'model' => is_null($this->getModel()) ? false : $this->getModel()->name,
                'pages' => $this->getPages(),
                'page' => $this->getPage(),
                'dataCount' => $this->getDataCount(),
                'orderBy' => $this->getOrderBy(),
                'search' => $this->getSearch(),
                'conditions' => $this->getConditions(),
                'actions' => $this->getActions(),
                'columns' => $this->getColumns()
            ], 'Render table');
            DebugBar::timeStop($debugId);
            return $tableContent;
        } catch (Throwable $throwable) {
            DebugBar::addThrowable($throwable);
            Log::log('Failed table render', 'ERR', ['exception' => $throwable->getMessage()]);

            throw new MicroFrameworkException('Failed table render');
        }
    }

    /**
     * Ajax actions
     * @return void
     * @throws HiddenException
     */
    protected function ajaxAction(): void
    {
        if (!Request::isPost() || !Request::isAjaxRequest() || Request::getPostData('layout') !== 'table') {
            return;
        }

        $this->isAjaxAction = true;
        $params = Request::getPostData('params');

        switch (Request::getPostData('action')) {
            case 'pagination':
                $this->setPage($params['page']);
                break;
            case 'search':
                $this->setSearch($params['table-search']);
                break;
        }
    }

    /**
     * Get table ID
     * @return string The object's ID.
     */
    public function getId(): string
    {
        if (is_null($this->id)) {
            $this->setId('table_' . str_replace(['/', '&dialogbox=1'], ['_', ''], substr(View::$GLOBAL_VARIABLES['here'], 1)));

        }

        return $this->id;
    }

    /**
     * Set table ID
     * @param string|null $id The ID to set for the object. Set to null if the automatic generate ID.
     * @return void
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * Get actions
     * @return ?array
     */
    public function getActions(): ?array
    {
        return $this->actions;
    }

    /**
     * Add actions
     * @param string $name
     * @param string $href
     * @param ?string $class
     * @return void
     */
    public function addAction(string $name, string $href, ?string $class = ''): void
    {
        $this->actions[$href] = [
            'name' => $name,
            'class' => $class
        ];
    }

    /**
     * Remove action
     * @param string $html
     * @return void
     */
    public function removeAction(string $html): void
    {
        unset($this->actions[$html]);
    }

    /**
     * Add column
     * @param string $key
     * @param string $name
     * @param callable|string|null $value
     * @param array $attributes
     * @param string|null $textAlign
     * @return void
     */
    public function addColumn(
        string $key,
        string $name,
        null|callable|string $value = null,
        array $attributes = [],
        ?string $textAlign = null
    ): void
    {
        $this->columns[$key] = [
            'name' => $name,
            'attributes' => $attributes,
            'value' => $value,
            'textAlign' => $textAlign
        ];
    }

    /**
     * Remove column
     * @param string $key
     * @return void
     */
    public function removeColumn(string $key): void
    {
        unset($this->columns[$key]);
    }

    /**
     * Get slim
     * @return bool
     */
    public function isSlim(): bool
    {
        return $this->slim;
    }

    /**
     * Set slim
     * @param bool $slim
     * @return void
     */
    public function setSlim(bool $slim): void
    {
        $this->slim = $slim;
    }

    /**
     * Add condition
     * @param Condition $condition
     * @return void
     */
    public function addCondition(Condition $condition): void
    {
        $this->conditions[] = $condition;
    }

    /**
     * Get order by
     * @return string|null
     */
    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    /**
     * Set order by
     * @param string|null $orderBy
     * @return void
     */
    public function setOrderBy(?string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

}