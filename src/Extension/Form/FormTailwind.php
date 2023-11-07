<?php

namespace Krzysztofzylka\MicroFramework\Extension\Form;

/**
 * Form generator
 */
class FormTailwind extends Form
{

    /**
     * Label
     * @param string $name
     * @param string|null $label
     * @param array $parameters
     * @param string|null $svgIcon
     * @return void
     */
    public function label(
        string $name,
        ?string $label = null,
        array $parameters = [],
        ?string $svgIcon = null
    ): void
    {
        $this->data[$name] = [
            'label' => $label,
            'parameters' => $parameters,
            'svgIcon' => $svgIcon
        ];

        if ($this->getData($name)) {
            $parameters['value'] = $this->getData($name);
        }

        $html = '';
        $parametersHtml = '';
        $validationMessage = $this->validation[$name] ?? null;
        $id = $this->generateId($name);
        $name = $this->generateName($name);
        $parameters = $parameters
            + [
                'type' => 'text',
                'name' => $name,
                'id' => $id,
                'class' => 'bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500'
            ];

        if (!is_null($validationMessage)) {
            $parameters['class'] = 'bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-red-100 dark:border-red-400';
        }

        if (!is_null($svgIcon)) {
            $parameters['class'] .= 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500';
        }

        foreach ($parameters as $key => $value) {
            $parametersHtml .= ' ' . $key . '="' . $value . '"';
        }

        if (!is_null($label)) {
            $html .= '<label for="' . $id . '" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">' . $label . '</label>';
        }

        if (!is_null($svgIcon)) {
            $html .= '<div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">' . $svgIcon . '</div>
                <input ' . $parametersHtml . '>
            </div>';
        } else {
            $html .= '<input' . $parametersHtml . '>';
        }

        if (!is_null($validationMessage)) {
            $html .= '<p class="mt-2 text-sm text-red-600 dark:text-red-500">' . $validationMessage . '</p>';
        }

        $this->formHtml .= '<div>' . $html . '</div>';
    }

    /**
     * Checkbox
     * @param string $name
     * @param string|null $label
     * @param array $parameters
     * @return void
     */
    public function checkbox(
        string $name,
        ?string $label = null,
        array $parameters = []
    ): void
    {
        $this->data[$name] = [
            'label' => $label,
            'parameters' => $parameters
        ];

        $html = '';
        $parametersHtml = '';
        $id = $this->generateId($name);
        $name = $this->generateName($name);
        $parameters = $parameters
            + [
                'type' => 'checkbox',
                'name' => $name,
                'id' => $id,
                'class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600'
            ];

        foreach ($parameters as $key => $value) {
            $parametersHtml .= ' ' . $key . '="' . $value . '"';
        }

        $html .= '<input' . $parametersHtml . '>';

        if (!is_null($label)) {
            $html .= '<label for="' . $id . '" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">' . $label . '</label>';
        }

        $this->formHtml .= '<div class="flex items-center mb-4">' . $html . '</div>';
    }

}