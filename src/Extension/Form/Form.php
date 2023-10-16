<?php

namespace Krzysztofzylka\MicroFramework\Extension\Form;

use Krzysztofzylka\MicroFramework\Controller;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Extension\Form\Helper\FormHelper;
use Krzysztofzylka\MicroFramework\Extension\Form\Trait\Validation;
use Krzysztofzylka\MicroFramework\Extension\Html\Html;

/**
 * Form generator
 * @package Extension\Form
 */
class Form
{

    use Validation;

    /**
     * Controller
     * @var Controller
     */
    private Controller $controller;

    /**
     * Data
     * @var array|null
     */
    private ?array $data;

    /**
     * Constructor
     * @param $controller
     */
    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->data = $this->controller->data;
    }

    /**
     * Input
     * @param string $name Name eg. abc/def...
     * @param ?string $title Title
     * @param array $attributes Attributes
     * @return Html
     * @throws MicroFrameworkException
     */
    public function input(string $name, ?string $title = null, array $attributes = []): Html
    {
        $invalidText = $this->getValidation($name);

        $params = [
            'class' => 'form-control',
            'type' => 'text',
            'name' => FormHelper::generateName($name),
            'id' => FormHelper::generateId($name)
        ];

        if ($invalidText) {
            $params['class'] .= ' is-invalid';
        }

        FormHelper::getData($name, $params, $attributes, $this->data);
        $input = (new Html())
                ->clearTag(
                    'input',
                    null,
                    [...$params, ...$attributes]
                )->__toString() . $this->generateInvalidDiv($invalidText);

        return (new Html())
            ->tag(
                'div',
                $this->generateTitle($title, $params) . $input,
                ['class' => 'form-group mb-2']
            );
    }

    /**
     * Date
     * @param string $name Name eg. abc/def...
     * @param ?string $title Title
     * @param array $attributes Attributes
     * @return Html
     * @throws MicroFrameworkException
     */
    public function date(string $name, ?string $title = null, array $attributes = []): Html
    {
        return $this->input(
            $name,
            $title,
            array_merge($attributes, ['type' => 'date'])
        );
    }

    /**
     * Select
     * @param string $name
     * @param array $options
     * @param ?string $selected
     * @param ?string $title
     * @param array $attributes
     * @return Html
     * @throws MicroFrameworkException
     */
    public function select(string $name, array $options, ?string $selected = null, ?string $title = null, array $attributes = []): Html
    {
        $invalidText = $this->getValidation($name);

        $params = [
            'class' => 'form-select',
            'name' => FormHelper::generateName($name),
            'id' => FormHelper::generateId($name)
        ];

        if ($invalidText) {
            $params['class'] .= ' is-invalid';
        }

        $optionsString = '';

        $emptyArray = [];
        $data = FormHelper::getData($name, $emptyArray, $emptyArray, $this->data);

        foreach ($options as $name => $value) {
            $htmlOption = new Html();
            $optionAttributes = ['value' => $name];

            if (($data ? (string)$data : (string)$selected) === (string)$name) {
                $optionAttributes['selected'] = 'selected';
            }

            $optionsString .= $htmlOption->tag('option', $value, $optionAttributes);
        }

        $select = (new Html())
                ->clearTag(
                    'select',
                    $optionsString,
                    [...$params, ...$attributes]
                ) . $this->generateInvalidDiv($invalidText);

        return (new Html())
            ->tag(
                'div',
                $this->generateTitle($title, $params) . $select,
                ['class' => 'form-group mb-2']
            );
    }

    /**
     * Checkbox
     * @param string $name
     * @param ?string $title
     * @param array $attributes
     * @return Html
     * @throws MicroFrameworkException
     */
    public function checkbox(string $name, ?string $title = null, array $attributes = []): Html
    {
        $params = [
            'class' => 'form-check-input',
            'type' => 'checkbox',
            'name' => FormHelper::generateName($name),
            'id' => FormHelper::generateId($name),
            'onclick' => "$(this).parent().find(\"input:last\").attr(\"value\", $(this).is(\":checked\") ? \"1\" : \"0\")",
        ];

        if (!isset($attributes['checked'])) {
            $data = FormHelper::getData($name, $params, $attributes, $this->data);

            if ($data) {
                $attributes['checked'] = $data;
            }
        }

        if (isset($attributes['checked']) && !$attributes['checked']) {
            unset($attributes['checked']);
        }

        return (new Html())->tag(
            'div',
            (new Html())->clearTag(
                'label',
                $title
                    . (new Html())->clearTag('input', null, [...$params, ...$attributes])
                    . (new Html())->clearTag('span', '', ['class' => 'checkmark'])
                    . $this->hidden($name, ['value' => isset($attributes['checked']) ? ($attributes['checked'] ? 1 : 0) : 0]),
                ['class' => 'form-check-label', 'for' => $params['id']]
            ),
            ['class' => 'form-group mb-2 form-check']
        );
    }

    /**
     * Hidden input
     * @param string $name
     * @param array $attributes
     * @return Html
     * @throws MicroFrameworkException
     */
    public function hidden(string $name, array $attributes = []): Html
    {
        $params = [
            'class' => 'd-none',
            'type' => 'text',
            'name' => FormHelper::generateName($name),
            'id' => FormHelper::generateId($name)
        ];

        FormHelper::getData($name, $params, $attributes, $this->data);

        return (new Html())
            ->tag(
                'input',
                null,
                [...$params, ...$attributes]
            );
    }


    /**
     * Textarea
     * @param string $name
     * @param ?string $title
     * @param array $attributes
     * @param string|null $value
     * @return Html
     * @throws MicroFrameworkException
     */
    public function textarea(string $name, ?string $title = null, array $attributes = [], ?string $value = null): Html
    {
        $invalidText = $this->getValidation($name);

        $params = [
            'class' => 'form-control',
            'name' => FormHelper::generateName($name),
            'id' => FormHelper::generateId($name)
        ];

        if ($invalidText) {
            $params['class'] .= ' is-invalid';
        }

        $emptyArray = [];
        $data = $value ?? FormHelper::getData($name, $emptyArray, $emptyArray, $this->data);
        $textarea = (new Html())->clearTag('textarea', $data ?? '', [...$params, ...$attributes]) . $this->generateInvalidDiv($invalidText);

        return (new Html())->tag(
            'div',
            $this->generateTitle($title, $params) . $textarea,
            ['class' => 'form-group mb-2']
        );
    }

    /**
     * Generate form title
     * @param string|null $title
     * @param array $params
     * @return string
     * @throws MicroFrameworkException
     */
    private function generateTitle(?string $title, array $params): string
    {
        if (is_null($title)) {
            return '';
        }

        return (new Html())
            ->clearTag(
                'label',
                $title,
                ['for' => $params['id'], 'class' => 'form-label']
            )->__toString();
    }

}