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
        $input = (new Html())->clearTag('input', null, [...$params, ...$attributes])->__toString() . $this->generateInvalidDiv($invalidText);

        return (new Html())->tag('div', $this->generateTitle($title, $params) . $input, ['class' => 'form-group mb-2']);
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
        return $this->input($name, $title, array_merge($attributes, ['type' => 'date']));
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

        return (new Html())->clearTag('label', $title, ['for' => $params['id'], 'class' => 'form-label'])->__toString();
    }

}