<?php

namespace Krzysztofzylka\MicroFramework\Libs\Table\Helper;

use Krzysztofzylka\HtmlGenerator\HtmlGenerator;
use Krzysztofzylka\MicroFramework\Exception\HiddenException;
use Krzysztofzylka\MicroFramework\Libs\Table\Table;

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
     * @throws HiddenException
     */
    public function render(): string
    {
        $dataCount = $this->tableInstance->getDataCount();

        if ($this->tableInstance->getPage() === $this->tableInstance->getPages()) {
            $maxCount = $dataCount;
        } else {
            $maxCount = $this->tableInstance->getPage() * $this->tableInstance->getPageLimit();
        }

        $fromCount = $maxCount === 0 ? 0 : (($this->tableInstance->getPage() - 1) * $this->tableInstance->getPageLimit() + 1);

        $spanTag = HtmlGenerator::createTag(
            'span',
            'Showing '
            . HtmlGenerator::createTag(
                'span',
                $fromCount . '-' . $maxCount,
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
     * @throws HiddenException
     */
    private function renderPagination(): string
    {
        $previousDisabled = false;
        $nextDisabled = false;

        if ($this->tableInstance->getPage() === 1) {
            $previousDisabled = true;
        }

        if ($this->tableInstance->getPage() === $this->tableInstance->getPages()) {
            $nextDisabled = true;
        }

        $liTags = [$this->renderPaginationLi('<<', 'rounded-s-lg', false, $previousDisabled), $this->renderPaginationLi('<', '', false, $previousDisabled)];

        $current_page = $this->tableInstance->getPage();
        $total_pages = $this->tableInstance->getPages();
        $start = ($current_page - 3 > 0) ? $current_page - 3 : 1;
        $end = ($current_page + 3 <= $total_pages) ? $current_page + 3 : $total_pages;

        for ($page = $start; $page <= $end; $page++) {
            $liTags[] = $this->renderPaginationLi($page, '', $page === $current_page);
        }

        $liTags[] = $this->renderPaginationLi('>', '', false, $nextDisabled);
        $liTags[] = $this->renderPaginationLi('>>', 'rounded-e-lg', false, $nextDisabled);

        return HtmlGenerator::createTag(
            'ul',
            implode(PHP_EOL, $liTags),
            'inline-flex -space-x-px rtl:space-x-reverse text-sm h-8'
        );
    }

    /**
     * Render pagination li
     * @param string $value
     * @param string $hrefClass
     * @param bool $currentPage
     * @param bool $disabled
     * @return string
     * @throws HiddenException
     */
    private function renderPaginationLi(string $value, string $hrefClass = '', bool $currentPage = false, bool $disabled = false): string
    {
        $pageValue = $value;

        if ($pageValue === '<') {
            $pageValue = $this->tableInstance->getPage() - 1;
        } elseif ($pageValue === '>') {
            $pageValue = $this->tableInstance->getPage() + 1;
        } elseif ($pageValue === '<<') {
            $pageValue = 1;
        } elseif ($pageValue === '>>') {
            $pageValue = $this->tableInstance->getPages();
        }

        $aTag = HtmlGenerator::createTag(
            'a',
            $value,
            'flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white '  . ($disabled ? 'opacity-50 cursor-not-allowed' : 'ajaxtable'). ' ' . $hrefClass,
            [
                'data-action' => RenderAction::generate($this->tableInstance, 'pagination', ['page' => $pageValue])
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