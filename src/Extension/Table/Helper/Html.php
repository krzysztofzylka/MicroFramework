<?php

namespace Krzysztofzylka\MicroFramework\Extension\Table\Helper;

use Krzysztofzylka\HtmlGenerator\HtmlGenerator;

/**
 * Html
 */
class Html
{

    /**
     * Link
     * @param string $name
     * @param string $href
     * @param bool $isAjax
     * @param array $attributes
     * @return string
     */
    public function link(string $name, string $href, bool $isAjax = false, array $attributes = []): string
    {
        $linkTag =  HtmlGenerator::createTag(
            'a',
            $name,
            'font-medium text-blue-600 dark:text-blue-500 hover:underline',
            array_merge(
                [
                    'href' => $href
                ],
                $attributes
            )
        );

        if ($isAjax) {
            $linkTag->appendAttribute('class', 'ajaxlink');
        }

        return $linkTag;
    }

    /**
     * Badge
     * @param string $value
     * @param string $color
     * @param bool $rounded
     * @return string
     */
    public function badge(string $value, string $color = 'blue', bool $rounded = false): string
    {
        $nadgeTag = HtmlGenerator::createTag(
            'span',
            $value,
            'bg-' . $color . '-100 text-' . $color . '-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-' . $color . '-900 dark:text-' . $color . '-300'
        );

        if ($rounded) {
            $nadgeTag->appendAttribute('class', 'rounded-full');
        }

        return $nadgeTag;
    }

    /**
     * Progressbar
     * @param int $percent
     * @param string $color
     * @return string
     */
    public function progressbar(int $percent, string $color = 'blue'): string
    {
        $textPercent = $percent . '%';

        return HtmlGenerator::createTag(
            'div',
            HtmlGenerator::createTag(
                'div',
                '',
                'bg-' . $color . '-600 h-2.5 rounded-full',
                [
                    'style' => 'width: ' . $textPercent
                ]
            ),
            'w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700'
        );
    }

    /**
     * Text color
     * @param string $value
     * @param string $color
     * @return string
     */
    public function textColor(string $value, string $color = 'gray'): string
    {
        return HtmlGenerator::createTag(
            'span',
            $value,
            'text-left font-medium text-' . $color . '-500'
        );
    }

}