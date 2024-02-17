<?php

namespace Krzysztofzylka\MicroFramework\Extension\Form;

use Krzysztofzylka\HtmlGenerator\HtmlGenerator;

trait InternalFormElements
{

    /**
     * Generates a hidden input HTML element.
     * @param string $name The name attribute of the input element.
     * @param ?string $value The value attribute of the input element (nullable).
     * @return void
     */
    public function hiddenInput(
        string  $name,
        ?string $value
    ): void
    {
        $this->input($name, null, ['value' => $value, 'type' => 'hidden']);
    }

    /**
     * Add an input field to the form.
     * @param string $name The name of the input field.
     * @param string|null $label The label for the input field (optional).
     * @param array $parameters Additional parameters for the input field (optional).
     * @param string|null $svgIcon The SVG icon to display with the input field (optional).
     * @return void
     */
    public function input(
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

        $id = InternalFormGenerator::generateId($name);
        $validationMessage = $this->validation[$name] ?? null;

        $labelTag = $label ? $this->createLabelTag($id, $label) : '';
        $validationTag = '';
        $inputTag = HtmlGenerator::createTag('input')
            ->setClass('bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500')
            ->addAttribute('type', 'text')
            ->setId($id)
            ->setName(InternalFormGenerator::generateName($name))
            ->addAttributes($parameters);

        if ($this->getData($name)) {
            $inputTag->addAttribute('value', $this->getData($name));
        }

        if ($validationMessage) {
            $inputTag->setClass('bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-red-100 dark:border-red-400');
            $validationTag = $this->createValidationTag($validationMessage);
        }

        if (!is_null($svgIcon)) {
            $inputTag->appendAttribute('class', 'pl-10');
            $inputTag = HtmlGenerator::createTag('div')
                ->setClass('relative')
                ->setContent(
                    HtmlGenerator::createTag('div')
                        ->setClass('absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none')
                        ->setContent($svgIcon)
                    . $inputTag
                );
        }

        $this->formHtml .= HtmlGenerator::createTag('div')->setContent($labelTag . $inputTag . $validationTag);
    }

    /**
     * Add file select input to the form.
     * @param string $name The name of the file select input.
     * @param string|null $label The label for the file select input.
     * @param array $parameters Additional parameters for the file select input.
     * @param string|null $helperText The helper text for the file select input.
     * @return void
     */
    public function fileSelect(
        string $name,
        ?string $label = null,
        array $parameters = [],
        ?string $helperText = null
    ): void
    {
        $this->data[$name] = [
            'label' => $label,
            'parameters' => $parameters
        ];

        $id = InternalFormGenerator::generateId($name);
        $validationMessage = $this->validation[$name] ?? null;

        $labelTag = $label ? $this->createLabelTag($id, $label) : '';
        $validationTag = '';
        $inputTag = HtmlGenerator::createTag('input')
            ->setClass('p-2.5 w-full')
            ->addAttribute('type', 'file')
            ->setId($id)
            ->setName(InternalFormGenerator::generateName($name))
            ->addAttributes($parameters);

        if ($this->getData($name)) {
            $inputTag->addAttribute('value', $this->getData($name));
        }

        if ($validationMessage) {
            $inputTag->setClass('block w-full text-sm text-red-900 border border-red-300 rounded-lg cursor-pointer bg-red-50 dark:text-red-300 focus:outline-none dark:bg-red-900 dark:border-red-600 dark:placeholder-red-400');
            $validationTag = $this->createValidationTag($validationMessage);
        }

        $helperTag = '';

        if (!is_null($helperText)) {
            $helperTag = HtmlGenerator::createTag('p')
                ->setClass('mt-1 text-sm text-gray-500 dark:text-gray-300')
                ->setId($id . '-helper')
                ->setContent($helperText);
        }

        $this->formHtml .= HtmlGenerator::createTag('div')
            ->setContent($labelTag . $inputTag . $validationTag . $helperTag)
            ->setClass('flex items-center bg-flex items-center bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 mt-2 mb-2');
    }

    /**
     * Add a textarea field to the form
     * @param string $name The name of the textarea field
     * @param string|null $label The label for the textarea field (optional)
     * @param string $content The content of the textarea field (default: '')
     * @param array $parameters Additional HTML attributes for the textarea field (optional)
     * @return void
     */
    public function textarea(
        string $name,
        ?string $label = null,
        string $content = '',
        array $parameters = []
    ): void
    {
        $this->data[$name] = [
            'label' => $label,
            'parameters' => $parameters
        ];

        $id = InternalFormGenerator::generateId($name);
        $validationMessage = $this->validation[$name] ?? null;

        $labelTag = $label ? $this->createLabelTag($id, $label) : '';
        $validationTag = '';
        $inputTag = HtmlGenerator::createTag('textarea')
            ->setClass('block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500')
            ->addAttribute('type', 'text')
            ->addAttribute('rows', 6)
            ->setId($id)
            ->setName(InternalFormGenerator::generateName($name))
            ->addAttributes($parameters)
            ->setContent($content);

        if ($this->getData($name)) {
            $inputTag->setContent($this->getData($name));
        }

        if ($validationMessage) {
            $inputTag->setClass('bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-red-100 dark:border-red-400');
            $validationTag = $this->createValidationTag($validationMessage);
        }

        $this->formHtml .= HtmlGenerator::createTag('div')->setContent($labelTag . $inputTag . $validationTag);
    }

    /**
     * Add a simple textarea to the form.
     * @param string $name The name attribute of the textarea.
     * @param array $parameters Additional parameters to be added as attributes to the textarea element.
     * @param string $buttonMessage The text content of the submit button.
     * @return void
     */
    public function simpleTextarea(
        string $name,
        array $parameters = [],
        string $buttonMessage = 'Add'
    ): void
    {
        $this->data[$name] = [
            'name' => $name,
            'parameters' => $parameters,
            'buttonMessage' => $buttonMessage
        ];

        $validationMessage = $this->validation[$name] ?? null;
        $id = InternalFormGenerator::generateId($name);

        $validationTag = '';
        $textareaTag = HtmlGenerator::createTag('textarea')
            ->setName(InternalFormGenerator::generateName($name))
            ->setId($id)
            ->addAttribute('rows', 3)
            ->setClass('w-full px-0 text-sm text-gray-900 bg-white border-0 dark:bg-gray-800 focus:ring-0 dark:text-white dark:placeholder-gray-400')
            ->addAttributes($parameters)
            ->setContent($this->getData($name) ?? '');
        $textareaDivTag = (new HtmlGenerator('div'))
            ->setClass('px-4 py-2 bg-white rounded-t-lg dark:bg-gray-800');
        $buttonTag = (new HtmlGenerator('button'))
            ->addAttribute('type', 'submit')
            ->setClass('line-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800')
            ->setContent($buttonMessage);

        if (!is_null($validationMessage)) {
            $textareaTag->setClass('w-full px-0 text-sm bg-red-50 border border-red-500 text-red-900 placeholder-red-700 border-0 dark:bg-gray-800 focus:ring-red-100 focus:border-red-500 dark:bg-red-100 dark:border-red-400');
            $textareaDivTag->setClass('px-4 py-2 bg-red-50 rounded-t-lg dark:bg-red-800');

            $validationTag = $this->createValidationTag($validationMessage);
        }

        $footerTag = HtmlGenerator::createTag(
            'div',
            $buttonTag,
            'flex items-center justify-between px-3 py-2 border-t dark:border-gray-600 w-1/2'
        );

        $this->formHtml .= HtmlGenerator::createTag('div')
                ->setClass('w-full mb-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700 dark:border-gray-600')
                ->setContent($textareaDivTag->setContent($textareaTag) . $footerTag) . $validationTag;
    }

    /**
     * Add a select input field to the form.
     * @param string $name The name of the select input field.
     * @param string|null $label The label for the select input field (optional).
     * @param array $selectData An array of options for the select input field.
     * @param array $parameters Additional parameters for the select input field (optional).
     * @return void
     */
    public function select(
        string $name,
        ?string $label = null,
        array $selectData = [],
        array $parameters = []
    ): void
    {
        $this->data[$name] = [
            'label' => $label,
            'parameters' => $parameters,
            'selectData' => $selectData
        ];

        $id = InternalFormGenerator::generateId($name);
        $validationMessage = $this->validation[$name] ?? null;

        $labelTag = '';
        $validationTag = '';
        $data = $this->getData($name);
        $content = '';

        foreach ($selectData as $optionKey => $optionName) {
            $optionTag = HtmlGenerator::createTag(
                'option',
                $optionName,
                null,
                ['value' => $optionKey]
            );

            if ($data === $optionKey) {
                $optionTag->addAttribute('selected', 'selected');
            }

            $content .= $optionTag;
        }

        $inputTag = HtmlGenerator::createTag('select')
            ->setClass('bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500')
            ->addAttribute('type', 'text')
            ->setId($id)
            ->setName(InternalFormGenerator::generateName($name))
            ->addAttributes($parameters)
            ->setContent($content);

        if ($label) {
            $labelTag = $this->createLabelTag($id, $label);
        }

        if ($validationMessage) {
            $inputTag->setClass('bg-red-50 border border-red-500 text-red-900 placeholder-red-700 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-red-100 dark:border-red-400');

            $validationTag = $this->createValidationTag($validationMessage);
        }

        $this->formHtml .= HtmlGenerator::createTag('div')->setContent($labelTag . $inputTag . $validationTag);
    }

    /**
     * Adds a checkbox element to the form.
     * @param string $name The name of the checkbox element.
     * @param string|null $label The label for the checkbox element. Default is null.
     * @param array $parameters Additional attributes for the checkbox element. Default is an empty array.
     * @param string|null $helperDescription The helper description for the checkbox element. Default is null.
     * @return void
     */
    public function checkbox(
        string $name,
        ?string $label = null,
        array $parameters = [],
        ?string $helperDescription = null
    ): void
    {
        $this->data[$name] = [
            'label' => $label,
            'parameters' => $parameters,
            'helperDescription' => $helperDescription
        ];

        $labelTag = '';
        $id = InternalFormGenerator::generateId($name);

        $checkboxTag = HtmlGenerator::createTag('input')
            ->addAttribute('type', 'checkbox')
            ->setName(InternalFormGenerator::generateName($name))
            ->setId($id)
            ->setClass('w-4 h-4 ml-2 p-2.5')
            ->addAttributes($parameters);

        if (!is_null($label)) {
            $labelTag = HtmlGenerator::createTag('label')
                ->addAttribute('for', $id)
                ->setClass('p-2.5 text-sm font-medium text-gray-900 dark:text-gray-300 w-full')
                ->setContent($label);
        }

        if (!is_null($helperDescription)) {
            $checkboxTag->setClass('mr-2 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600');

            $helperDescriptionHtml = HtmlGenerator::createTag('div')
                ->setClass('ms-2 text-sm')
                ->setContent(
                    HtmlGenerator::createTag('label')
                        ->addAttribute('for', $id)
                        ->setClass('font-medium text-gray-900 dark:text-gray-300')
                        ->setContent($label ?? '')
                    . HtmlGenerator::createTag('p')
                        ->setId($id . '-text')
                        ->setClass('text-xs font-normal text-gray-500 dark:text-gray-300')
                        ->setContent($helperDescription)
                );

            $this->formHtml .= HtmlGenerator::createTag('div')
                ->setClass('flex')
                ->setContent(
                    HtmlGenerator::createTag('div')->setClass('flex items-center h-5')->setContent($checkboxTag)
                    . $helperDescriptionHtml
                );

            return;
        }

        $this->formHtml .= HtmlGenerator::createTag('div')->setClass('flex items-center bg-flex items-center bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 mt-2 mb-2')->setContent($checkboxTag . $labelTag);
    }

    /**
     * Add a submit button to the form
     * @param string $value The value/label of the submit button
     * @param array $parameters Additional parameters for the submit button
     * @return void
     */
    public function submitButton(
        string $value,
        array $parameters = []
    ): void
    {
        $this->data[] = [
            'value' => $value,
            'parameters' => $parameters
        ];

        $this->formHtml .= HtmlGenerator::createTag(
            'button',
            $value,
            'text-white mt-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800',
            [
                'type' => 'submit'
            ]
        );
    }

    /**
     * Creates a label tag for an HTML form.
     * @param string $id The value of the 'for' attribute of the label.
     * @param string $label The content of the label.
     * @return HtmlGenerator Returns an HtmlGenerator instance representing the label tag.
     */
    private function createLabelTag(string $id, string $label): HtmlGenerator
    {
        return HtmlGenerator::createTag(
            'label',
            $label,
            'block mb-2 text-sm font-medium text-gray-900 dark:text-white mt-2',
            [
                'for' => $id
            ]
        );
    }

    /**
     * Create a validation tag.
     * @param mixed $validationMessage The validation message to be displayed in the tag.
     * @return HtmlGenerator Returns an instance of HtmlGenerator with the validation tag.
     */
    private function createValidationTag(mixed $validationMessage): HtmlGenerator
    {
        return HtmlGenerator::createTag(
            'p',
            $validationMessage,
            'mt-2 text-sm text-red-600 dark:text-red-500'
        );
    }

}