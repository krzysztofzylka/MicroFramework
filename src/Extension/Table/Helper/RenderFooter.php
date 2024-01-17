<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table\Helper;

use Krzysztofzylka\HtmlGenerator\HtmlGenerator;
use Krzysztofzylka\MicroFramework\Extension\Table\Table;

class RenderFooter
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
     * Render footer
     * @return string
     */
    public function render(): string
    {
        $dataCount = $this->tableInstance->getDataCount();

        if ($this->tableInstance->getPage() === $this->tableInstance->getPages()) {
            $maxCount = $dataCount;
        } else {
            $maxCount = $this->tableInstance->getPage() * $this->tableInstance->getPageLimit();
        }

        $spanTag = HtmlGenerator::createTag(
            'span',
            'Showing '
                . HtmlGenerator::createTag(
                    'span',
                    (($this->tableInstance->getPage() - 1) * $this->tableInstance->getPageLimit() + 1). '-' . $maxCount,
                    'font-semibold text-gray-900 dark:text-white'
                )
                . ' of '
                . HtmlGenerator::createTag(
                    'span',
                $dataCount,
                'font-semibold text-gray-900 dark:text-white'
            ),
            'text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto'
        );

        return HtmlGenerator::createTag(
            'nav',
            $spanTag . $this->renderPagination(),
            'flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 p-4 bg-white dark:bg-gray-900'
        );
    }

    /**
     * Render pagination
     * @return string
     */
    private function renderPagination(): string
    {
        $liTags = [$this->renderPaginationLi('<', '#', 'rounded-s-lg')];

        for ($page = 1; $page <= $this->tableInstance->getPages(); $page++) {
            $liTags[] = $this->renderPaginationLi($page, '#', '', $page === $this->tableInstance->getPage());
        }

        $liTags[] = $this->renderPaginationLi('>', '#', 'rounded-e-lg');

        return HtmlGenerator::createTag(
            'ul',
            implode(PHP_EOL, $liTags),
            'inline-flex -space-x-px rtl:space-x-reverse text-sm h-8'
        );
    }

    /**
     * Render pagination li
     * @param string $value
     * @param string $href
     * @param string $hrefClass
     * @param bool $currentPage
     * @return string
     */
    private function renderPaginationLi(string $value, string $href, string $hrefClass = '', bool $currentPage = false): string
    {
        $aTag = HtmlGenerator::createTag(
            'a',
            $value,
            'flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white ' . $hrefClass,
            [
                'href' => $href
            ]
        );

        if ($currentPage) {
            $aTag->addAttribute('aria-current', 'page');
            $aTag->setClass('flex items-center justify-center px-3 h-8 text-blue-600 border border-gray-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white ' . $hrefClass);
        }

        return HtmlGenerator::createTag(
            'li',
            $aTag
        );
    }

}