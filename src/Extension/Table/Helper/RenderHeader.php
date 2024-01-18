<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table\Helper;

use Krzysztofzylka\Generator\Generator;
use Krzysztofzylka\HtmlGenerator\HtmlGenerator;
use Krzysztofzylka\MicroFramework\Extension\Table\Table;
use Krzysztofzylka\MicroFramework\View;
use Random\RandomException;

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
     * @throws RandomException
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
//        if (is_null($this->tableInstance->getActions())) {
//            return HtmlGenerator::createTag(
//                'div',
//                '',
//                'w-full md:w-1/2'
//            );
//        }
//
//        $span = HtmlGenerator::createTag(
//            'span',
//            'Action'
//        );
//
//        $svgIcon = '<svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/></svg>';
//
//        $button = HtmlGenerator::createTag(
//            'button',
//            $span . $svgIcon,
//            'inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700',
//            [
//                'id' => 'action-button',
//                'data-dropdown-toggle' => $this->actionButtonId,
//                'type' => 'button'
//            ]
//        );

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
//
//        $ul = HtmlGenerator::createTag(
//            'ul',
//            implode(PHP_EOL, $buttons),
//            'py-1 text-sm text-gray-700 dark:text-gray-200',
//            [
//                'aria-labelledby' => 'dropdownActionButton'
//            ]
//        );
//
//        return HtmlGenerator::createTag(
//            'div',
//            $ul,
//            'z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600',
//            [
//                'id' => 'table-action-' . $this->tableInstance->getId()
//            ]
//        );
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