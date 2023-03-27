<?php

namespace Krzysztofzylka\MicroFramework\Extension\Account\Enum;

enum AuthControlAction
{

    /**
     * 401 Exception
     */
    case exception;

    /**
     * Redirect
     */
    case redirect;

}