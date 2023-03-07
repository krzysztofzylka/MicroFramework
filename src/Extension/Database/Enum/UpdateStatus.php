<?php

namespace Krzysztofzylka\MicroFramework\Extension\Database\Enum;

/**
 * Update status
 */
enum UpdateStatus: string
{

    /**
     * Init
     */
    case Init = 'Init';

    /**
     * Success
     */
    case Success = 'Success';

    /**
     * Fail
     */
    case Fail = 'Fail';

}