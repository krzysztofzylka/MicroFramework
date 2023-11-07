<?php

namespace Krzysztofzylka\MicroFramework\Component;

use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Kernel;

/**
 * Component loader
 */
class Loader
{

    /**
     * Components
     * @var array
     */
    public static array $config = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        DebugBar::timeStart('component', 'Load components');
        self::$config = json_decode(file_get_contents(Kernel::getPath('components_config')), true);
        DebugBar::timeStop('component');
        DebugBar::addComponentsMessage(self::$config, 'Config');

        DebugBar::timeStart('component', 'Init components');
        $this->initComponents();
        DebugBar::timeStop('component');
    }

    /**
     * Initialize components
     * @return void
     * @throws \Exception
     */
    Public function initComponents(): void
    {
        foreach (self::$config['components'] as $component) {
            DebugBar::addComponentsMessage($component, 'Init component');

            try {
                /** @var Component $componentClass */
                $componentClass = new $component();
                $componentClass->init();
            } catch (\Throwable $exception) {
                Log::log('Fail initialize component ' . $component, 'ERROR');
                DebugBar::addComponentsMessage('Fail initialize component ' . $component, 'ERROR');
                DebugBar::addThrowable($exception);

                continue;
            }
        }
    }

}