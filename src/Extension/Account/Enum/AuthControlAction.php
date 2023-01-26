<?php

namespace Krzysztofzylka\MicroFramework\Extension\Account\Enum;

/**
 * Auth control action
 * @package Extension
 */
enum AuthControlAction {

    /**
     * 401 Exception
     */
    case exception;

    /**
     * Redirect
     */
    case redirect;

}