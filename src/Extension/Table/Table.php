<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table;

use Exception;
use Krzysztofzylka\Generator\Generator;
use Krzysztofzylka\HtmlGenerator\HtmlGenerator;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Extension\Table\Helper\RenderFooter;
use Krzysztofzylka\MicroFramework\Extension\Table\Helper\RenderHeader;
use Krzysztofzylka\MicroFramework\Extension\Table\Helper\RenderTable;
use Random\RandomException;

/**
 * Table
 */
class Table
{

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
    protected int $pages = 1;

    /**
     * Get table ID
     * @return string The object's ID.
     * @throws RandomException
     */
    public function getId(): string
    {
        if (is_null($this->id)) {
            $this->setId(Generator::uuid());
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
        try {
            return HtmlGenerator::createTag(
                'div',
                (new RenderHeader($this))->render()
                    . (new RenderTable($this))->render()
                    . (new RenderFooter($this))->render(),
                'bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden',
                [
                    'id' => $this->getId()
                ]
            );
        } catch (\Throwable $throwable) {
            Log::log('Failed table render', 'ERR', ['exception' => $throwable->getMessage()]);

            throw new MicroFrameworkException('Failed table render');
        }
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
     * Get columns
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Add column
     * @param string $key
     * @param string $name
     * @return void
     */
    public function addColumn(string $key, string $name): void
    {
        $this->columns[$key] = [
            'name' => $name
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
     * Get data
     * @param bool $full
     * @return array
     */
    public function getData(bool $full = true): array
    {
        if (!$full) {
            return array_slice($this->data, (($this->page - 1) * $this->pageLimit), 10);
        }

        return $this->data;
    }

    /**
     * Set data
     * @param array $data
     * @return void
     */
    public function setData(array $data): void
    {
        $this->data = $data;
        $this->setPages(ceil(count($data) / $this->pageLimit));
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
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * Get pages count
     * @return int
     */
    public function getPages(): int
    {
        return $this->pages;
    }

    /**
     * Set pages count
     * @param int $pages
     * @return void
     */
    public function setPages(int $pages): void
    {
        $this->pages = max($pages, 1);
    }

    /**
     * Get data count
     * @return int
     */
    public function getDataCount(): int
    {
        return count($this->data);
    }

}