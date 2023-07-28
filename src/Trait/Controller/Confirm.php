<?php

namespace Krzysztofzylka\MicroFramework\Trait\Controller;

use Krzysztofzylka\MicroFramework\Exception\ViewException;

trait Confirm
{

    /**
     * Confirm action
     * @param string|null $message
     * @param string $title
     * @return bool
     * @throws ViewException
     * @todo translate
     */
    public function confirmAction(
        ?string $message = 'Czy na pewno chcesz wykonać tą operację?',
        string $title = 'Powierdzenie wykonania operacji'
    ): bool
    {
        if (isset($this->data['confirmAction']) && $this->data['confirmAction']) {
            return true;
        }

        $this->layout = 'dialogbox';
        $this->title = $title;

        $this->loadView([
            'message' => $message
        ], '/MicroFramework/ControllerActions/confirm');

        return false;
    }

}