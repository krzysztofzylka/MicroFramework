<?php

namespace Krzysztofzylka\MicroFramework;

/**
 * View VUE
 */
class ViewVue extends View
{

    /**
     * Generate vue
     * @return string
     */
    public function vue(): string
    {
        return '<script src="' . $_ENV['URL'] . '/public_files/vue/' . $this->action . '"></script>';
    }

    /**
     * Generate header
     * @return string
     */
    public function renderHeader(): string
    {
        return '<script src="https://cdn.jsdelivr.net/npm/vue"></script>';
    }

}