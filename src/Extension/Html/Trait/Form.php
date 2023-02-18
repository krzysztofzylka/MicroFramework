<?php

namespace Krzysztofzylka\MicroFramework\Extension\Html\Trait;

use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Extension\Html\Html;
use Krzysztofzylka\MicroFramework\Kernel;

/**
 * Html helper - forms
 */
trait Form {

    /**
     * Create form tag
     * @param string $content
     * @return Html
     * @throws MicroFrameworkException
     */
    public function form(string $content) : Html {
        return $this->tag('form', $content, ['method' => 'post']);
    }

    /**
     * Input
     * @param string $name Nazwa elementu w formacie abc/def...
     * @param ?string $title Tytuł elementu
     * @param array $attributes Dodatkowe atrybuty dla input
     * @return Html
     * @throws MicroFrameworkException
     */
    public function input(string $name, ?string $title = null, array $attributes = []) : Html {
        $invalidText = $this->getInvalidText($name);

        $params = [
            'class' => 'form-control',
            'type' => 'text',
            'name' => $this->formName($name),
            'id' => $this->formId($name)
        ];

        if ($invalidText) {
            $params['class'] .= ' is-invalid';
        }

        $this->getData($name, $params, $attributes);
        $input = $this->clearTag('input', null, [...$params, ...$attributes])->__toString() . $this->generateInvalidDiv($invalidText);

        return $this->tag('div', $this->generateTitle($title, $params) . $input, ['class' => 'form-group mb-2']);
    }

    /**
     * Input wgrywania pliku
     * @param string $name
     * @param ?string $title
     * @param array $attributes
     * @return Html
     * @throws MicroFrameworkException
     */
    public function file(string $name, ?string $title = null, array $attributes = []) : Html {
        $params = [
            'class' => 'form-control',
            'type' => 'file',
            'name' => $this->formName($name),
            'id' => $this->formId($name)
        ];

        $this->getData($name, $params, $attributes);
        $input = $this->clearTag('input', null, [...$params, ...$attributes])->__toString();

        return $this->tag('div', $this->generateTitle($title, $params) . $input, ['class' => 'form-group mb-2']);
    }

    /**
     * Select
     * @param string $name nazwa elementu w formacie abc/def...
     * @param array $options opcje selecta ['name' => 'value', ...]
     * @param ?string $selected nazwa aktualnie wyranego elementu
     * @param ?string $title tytuł elementu
     * @param array $attributes dodatkowe atrybuty dla input
     * @return Html
     * @throws MicroFrameworkException
     */
    public function select(string $name, array $options, ?string $selected = null, ?string $title = null, array $attributes = []) : Html {
        $invalidText = $this->getInvalidText($name);

        $params = [
            'class' => 'form-select',
            'name' => $this->formName($name),
            'id' => $this->formId($name)
        ];

        if ($invalidText) {
            $params['class'] .= ' is-invalid';
        }

        $optionsString = '';

        $data = $this->getData($name);

        foreach ($options as $name => $value) {
            $htmlOption = new Html();
            $optionAttributes = ['value' => $name];

            if ($data) {
                if ($data === $name) {
                    $optionAttributes['selected'] = 'selected';
                }
            } elseif ($name === $selected) {
                $optionAttributes['selected'] = 'selected';
            }

            $optionsString .= $htmlOption->tag('option', $value, $optionAttributes);
        }

        $select = $this->clearTag('select', $optionsString, [...$params, ...$attributes]) . $this->generateInvalidDiv($invalidText);

        return $this->tag('div', $this->generateTitle($title, $params) . $select, ['class' => 'form-group mb-2']);
    }

    /**
     * Select2
     * @param string $name nazwa elementu w formacie abc/def...
     * @param array $options opcje selecta ['name' => 'value', ...]
     * @param ?string|array $selected nazwa aktualnie wyranego elementu
     * @param ?string $title tytuł elementu
     * @param array $attributes dodatkowe atrybuty dla input
     * @param array $select2attr attrybuty dla select2
     * @return Html
     * @throws MicroFrameworkException
     */
    public function select2(string $name, array $options, string|array $selected = null, ?string $title = null, array $attributes = [], array $select2attr = []) : Html {
        $invalidText = $this->getInvalidText($name);

        $params = [
            'class' => 'form-select',
            'name' => $this->formName($name),
            'id' => $this->formId($name)
        ];

        if ($invalidText) {
            $params['class'] .= ' is-invalid';
        }

        $optionsString = '';

        $data = $this->getData($name);

        if (!is_array($selected)) {
            $selected = [$selected];
        }

        if (isset($attributes['multiple']) && $attributes['multiple'] === true && str_starts_with($data, '[') && str_ends_with($data, ']')) {
            $data = json_decode($data, true);
        }

        foreach ($options as $name => $value) {
            $htmlOption = new Html();
            $optionAttributes = ['value' => $name];

            if ($data) {
                if ($data === $name || (is_array($data) && in_array($name, $data))) {
                    $optionAttributes['selected'] = 'selected';
                }
            } elseif (in_array($name, $selected)) {
                $optionAttributes['selected'] = 'selected';
            }

            $optionsString .= $htmlOption->tag('option', $value, $optionAttributes);
        }

        $select = $this->clearTag('select', $optionsString, [...$params, ...$attributes]) . $this->generateInvalidDiv($invalidText);

        return $this->tag('div', $this->generateTitle($title, $params) . $select, ['class' => 'form-group mb-2'])
            ->tag(
                'script',
                "$(document).ready(function () {
                    $('#" . $params['id'] . "').select2({
                        theme: 'bootstrap-5',
                        " . $this->generateSelect2Options($select2attr) . "
                    });
                });"
            );
    }

    /**
     * @param $options
     * @return string
     * @ignore
     */
    private function generateSelect2Options($options) : string {
        $data = '';

        foreach ($options as $key => $value) {
            $data .= $key . ': ';

            if (is_bool($value)) {
                $data .= $value ? 'true' : 'false';
            } elseif (is_int($value)) {
                $data .= $value;
            } else {
                $data .= '"' . $value . '"';
            }
        }

        return $data;
    }

    /**
     * Checkbox
     * @param string $name nazwa elementu w formacie abc/def...
     * @param ?string $title tytuł elementu
     * @param array $attributes dodatkowe atrybuty dla input
     * @return Html
     * @throws MicroFrameworkException
     */
    public function checkbox(string $name, ?string $title = null, array $attributes = []) : Html {
        $params = [
            'class' => 'form-check-input',
            'type' => 'checkbox',
            'name' => $this->formName($name),
            'id' => $this->formId($name),
            'onclick' => "$(this).parent().find(\"input:last\").attr(\"value\", $(this).is(\":checked\") ? \"1\" : \"0\")",
        ];

        if (!isset($attributes['checked'])) {
            $data = $this->getData($name, $params, $attributes);

            if ($data) {
                $attributes['checked'] = $data;
            }
        }

        if (isset($attributes['checked']) && !$attributes['checked']) {
            unset($attributes['checked']);
        }

        return $this->tag(
            'div',
            $this->clearTag(
                'label',
                $title
                . $this->clearTag('input', null, [...$params, ...$attributes])
                . $this->clearTag('span', '', ['class' => 'checkmark'])
                . $this->hidden($name, ['value' => isset($attributes['checked']) ? ($attributes['checked'] ? 1 : 0) : 0]),
                ['class' => 'form-check-label', 'for' => $params['id']]
            ),
            ['class' => 'form-group mb-2 form-check']
        );
    }

    /**
     * Ukryty input
     * @param string $name nazwa elementu w formacie abc/def...
     * @param array $attributes dodatkowe atrybuty dla input
     * @return Html
     * @throws MicroFrameworkException
     */
    public function hidden(string $name, array $attributes = []) : Html {
        $params = [
            'class' => 'd-none',
            'type' => 'text',
            'name' => $this->formName($name),
            'id' => $this->formId($name)
        ];

        $this->getData($name, $params, $attributes);

        return $this->tag('input', null, [...$params, ...$attributes]);
    }

    /**
     * Textarea
     * @param string $name nazwa elementu w formacie abc/def...
     * @param ?string $title tytuł elementu
     * @param array $attributes dodatkowe atrybuty dla input
     * @param string|null $value
     * @return Html
     * @throws MicroFrameworkException
     */
    public function textarea(string $name, ?string $title = null, array $attributes = [], ?string $value = null) : Html {
        $invalidText = $this->getInvalidText($name);

        $params = [
            'class' => 'form-control',
            'name' => $this->formName($name),
            'id' => $this->formId($name)
        ];

        if ($invalidText) {
            $params['class'] .= ' is-invalid';
        }

        $data = $value ?? $this->getData($name);
        $textarea = $this->clearTag('textarea', $data ?? '', [...$params, ...$attributes]) . $this->generateInvalidDiv($invalidText);

        return $this->tag('div', $this->generateTitle($title, $params) . $textarea, ['class' => 'form-group mb-2']);
    }

    /**
     * Textarea ukryta
     * @param string $name nazwa elementu w formacie abc/def...
     * @param ?string $value
     * @return Html
     * @throws MicroFrameworkException
     */
    public function textareaHidden(string $name, ?string $value = null) : Html {
        $params = [
            'name' => $this->formName($name),
            'id' => $this->formId($name),
            'style' => 'display:none'
        ];

        $data = $value ?? $this->getData($name);

        return $this->tag('textarea', $data ?? '', $params);
    }

    /**
     * Przycisk (input submit)
     * @param string $value treść przycisku
     * @param ?string $name nazwa przycisku w formacie abc/def...
     * @param array $attributes dodatkowe atrybuty
     * @return Html
     * @throws MicroFrameworkException
     */
    public function button(string $value, ?string $name = null, array $attributes = []) : Html {
        $params = [
            'class' => 'form-control btn btn-primary',
            'type' => 'submit',
            'value' => $value
        ];

        if ($name) {
            $params = [
                'name' => $this->formName($name),
                'id' => $this->formId($name),
                ...$params
            ];
        }

        return $this->tag('input', null, [...$params, ...$attributes]);
    }

    /**
     * Create quill editor
     * @param string $name
     * @param ?string $title
     * @param array $attributes
     * @param ?string $value
     * @return Html
     * @throws MicroFrameworkException
     */
    public function quillEditor(string $name, ?string $title = null, array $attributes = [], ?string $value = null) : Html {
        $id = 'quill' . $this->formId($name);
        $textareaId = $this->formId($name);
        $jsScript = "html.quillRender('" . $id . "', '" . $textareaId . "')";
        $data = stripslashes($value ?? $this->getData($name));
        $content = $this->tag('div', $data ?? '', ['id' => $id])->tag('script', $jsScript)->textareaHidden($name);

        return $this->tag(
            'div',
            $content,
            [
                ...['style' => 'height: 500px'],
                ...$attributes,
                ...['class' => 'quill-container mt-2 mb-2']
            ]
        );
    }

    /**
     * Pobieranie danych z formularza za pomocą nazwy
     * @param string $name nazwa pola formularza w formacie abc/def...
     * @param ?array $params parametry
     * @param array $attributes atrybuty
     * @return ?string
     * @ignore
     */
    private function getData(string $name, ?array &$params = null, array &$attributes = []) : mixed {
        $generatedArray = '["' . implode('"]["', explode('/', $name)) . '"]';

        if ($generatedArray === '[""]') {
            return null;
        }

        $data = Kernel::getData();
        $generatedArray = str_replace('[""]', '', $generatedArray);
        $dataString = @eval('return $data' . $generatedArray . ';');

        if ($dataString && !is_null($params)) {
            unset($attributes['value']);

            $params = [...$params, 'value' => $dataString];
        }

        return $dataString;
    }

    /**
     * Generowanie tytułu formularza
     * @param ?string $title
     * @param array $params
     * @return string
     * @throws MicroFrameworkException
     * @ignore
     */
    private function generateTitle(?string $title, array $params) : string {
        if (is_null($title)) {
            return '';
        }

        return $this->clearTag('label', $title, ['for' => $params['id'], 'class' => 'form-label'])->__toString();
    }


    /**
     * Generowanie nazwy dla elementów formularza
     * @param mixed $name nazwa elementu formularza w formacie abc/def...
     * @param string $preffix preffix
     * @return string
     */
    private static function formName(string $name, string $preffix = '') : string {
        $core = str_starts_with($name, '/');

        if ($core) {
            $name = substr($name, 1);
            $preffix .= '/';
        }

        $explode = explode('/', $name, 2);

        return $preffix . $explode[0] . (isset($explode[1]) ? ('[' . implode('][', explode('/', $explode[1])) . ']') : '');
    }

    /**
     * Generowanie id dla elementów formularza
     * @param string $name nazwa elementu formularza w formacie abc/def...
     * @return string
     */
    private static function formId(string $name) : string {
        $return = '';
        $explode = explode('/', $name);

        foreach ($explode as $value) {
            $value = mb_strtolower($value);
            $return .= empty($return) ? $value : ucfirst($value);
        }

        return $return;
    }

    /**
     * Ger invalid text
     * @param string $name
     * @return string|false
     */
    private function getInvalidText(string $name) : string|false {
        if (!isset($this->formValidation)) {
            return false;
        }

        $validation = $this->formValidation;

        foreach (explode('/', $name) as $explodeName) {
            if (!isset($validation[$explodeName])) {
                return false;
            }

            $validation = $validation[$explodeName];
        }

        return $validation;
    }

    /**
     * Generate invalid feedback tag
     * @param string|false $invalidText
     * @return string
     * @throws MicroFrameworkException
     */
    private function generateInvalidDiv(string|false $invalidText) : string {
        if ($invalidText) {
            return $this->clearTag('div', $invalidText, ['id' => 'fieldError', 'class' => 'invalid-feedback'])->__toString();
        }

        return '';
    }

}