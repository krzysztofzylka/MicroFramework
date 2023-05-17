<?php

namespace Krzysztofzylka\MicroFramework\Trait\Controller;

trait Confirm
{

    public function confirmAction(?string $message = 'Czy na pewno chcesz wykonać tą operację?', string $title = 'Powierdzenie wykonania operacji'): bool
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