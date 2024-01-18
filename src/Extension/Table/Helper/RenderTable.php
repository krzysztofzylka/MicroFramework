<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table\Helper;

use Krzysztofzylka\HtmlGenerator\HtmlGenerator;
use Krzysztofzylka\MicroFramework\Exception\HiddenException;
use Krzysztofzylka\MicroFramework\Extension\Table\Cell;
use Krzysztofzylka\MicroFramework\Extension\Table\Table;

class RenderTable
{

    /**
     * Table instance
     * @var Table
     */
    private Table $tableInstance;

    /**
     * Constructor
     * @param Table $tableInstance
     */
    public function __construct(Table $tableInstance)
    {
        $this->tableInstance = $tableInstance;
    }

    /**
     * Render table
     * @return string
     * @throws HiddenException
     */
    public function render(): string
    {
        return HtmlGenerator::createTag(
            'table',
            $this->renderHead() . $this->renderBody(),
            'w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400'
        );
    }

    /**
     * Render table head
     * @return string
     */
    private function renderHead(): string
    {
        $columns = [];

        foreach ($this->tableInstance->getColumns() as $column) {
            $columnTag = HtmlGenerator::createTag(
                'th',
                $column['name'],
                'px-6 py-' . ($this->tableInstance->isSlim() ? '2' : '3'),
                [
                    'scope' => 'col'
                ]
            );

            if (isset($column['attributes'])) {
                $columnTag->addAttributes($column['attributes']);
            }

            $columns[] = (string)$columnTag;
        }

        $trTag = HtmlGenerator::createTag(
            'tr',
            implode(PHP_EOL, $columns)
        );

        return HtmlGenerator::createTag(
            'thead',
            $trTag,
            'text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400'
        );
    }

    /**
     * Render table body
     * @return string
     * @throws HiddenException
     */
    private function renderBody(): string
    {
        $dataTags = [];

        foreach ($this->tableInstance->getData(false) as $data) {
            $tdTags = [];

            foreach ($this->tableInstance->getColumns() as $columnDataKey => $column) {
                $value = null;

                if (isset($column['value']) && is_string($column['value'])) {
                    $value = $column['value'];
                } elseif (isset($data[$columnDataKey]) && is_null($value)) {
                    $value = $data[$columnDataKey];
                } elseif (!is_null($this->tableInstance->getModel()) && is_null($value)) {
                    $generatedArray = '["' . implode('"]["', explode('.', $columnDataKey)) . '"]';
                    $value = @eval('return $data' . $generatedArray . ';');
                }

                if (isset($column['value']) and is_callable($column['value'])) {
                    $cell = new Cell();
                    $cell->value = $value;
                    $cell->data = $data;

                    $value = $column['value']($cell);
                }

                $tdTags[] = HtmlGenerator::createTag(
                    'td',
                    $value ?? '',
                    'px-6 py-' . ($this->tableInstance->isSlim() ? '2' : '4')
                );
            }

            $dataTags[] = HtmlGenerator::createTag(
                'tr',
                implode(PHP_EOL, $tdTags),
                'bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600'
            );
        }

        return HtmlGenerator::createTag(
            'tr',
            implode(PHP_EOL, $dataTags),
            'bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600'
        );
    }

}