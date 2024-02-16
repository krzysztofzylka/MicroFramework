<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table\Helper;

use Krzysztofzylka\HtmlGenerator\HtmlGenerator;
use Krzysztofzylka\MicroFramework\Extension\Table\Table;
use Krzysztofzylka\MicroFramework\View;

class RenderHeader
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
     * Render header
     * @return string
     */
    public function render(): string
    {
        return HtmlGenerator::createTag(
            'div',
            $this->renderActions() . $this->renderSearch(),
            'flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 p-4 bg-white dark:bg-gray-900'
        );
    }

    /**
     * Render actions
     * @return string
     */
    private function renderActions(): string
    {
        return HtmlGenerator::createTag(
            'div',
            $this->renderActionButtons(),
            'w-full md:w-1/2'
        );
    }

    /**
     * Render action buttons
     * @return string
     */
    private function renderActionButtons(): string
    {
        $buttons = [];

        foreach ($this->tableInstance->getActions() ?? [] as $href => $action) {
            $buttons[] = HtmlGenerator::createTag(
                'a',
                $action['name'],
                'text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 ' . $action['class'],
                [
                    'href' => $href
                ]
            );
        }

        return implode('', $buttons);
    }

    /**
     * Render search
     * @return string
     */
    private function renderSearch(): string
    {
        $svgIcon = HtmlGenerator::createTag(
            'div',
            '<svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/></svg>',
            'absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none'
        );

        $inputTag = HtmlGenerator::createTag(
            'input',
            '',
            'block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
            [
                'type' => 'text',
                'id' => 'table-search',
                'placeholder' => 'Search',
                'name' => 'table-search',
                'value' => $this->tableInstance->getSearch() ?? ''
            ]
        );

        $formTag = HtmlGenerator::createTag(
            'form',
            $inputTag,
            'p-0 m-0 ajaxtableform',
            [
                'action' => View::$GLOBAL_VARIABLES['here'],
                'data-action' => RenderAction::generate($this->tableInstance, 'search'),
                'method' => 'POST'
            ]
        );

        return HtmlGenerator::createTag(
            'div',
            $svgIcon . $formTag,
            'relative'
        );
    }

}