<?php

namespace Krzysztofzylka\MicroFramework\Component;

use Exception;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Kernel;
use Throwable;

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
     * Components
     * @var array
     */
    public static array $components = [];

    /**
     * Is ini
     * @var bool
     */
    public static bool $init = false;

    /**
     * Constructor
     * @throws Exception
     */
    public function __construct()
    {
        if (!self::$init) {
            DebugBar::timeStart('component', 'Load components');
            self::$config = json_decode(file_get_contents(Kernel::getPath('components_config')), true);
            DebugBar::timeStop('component');
            DebugBar::addComponentsMessage(self::$config, 'Config');

            DebugBar::timeStart('component', 'Init components');
            $this->initComponents();
            DebugBar::timeStop('component');

            self::$init = true;
        }
    }

    /**
     * Initialize components
     * @return void
     * @throws Exception
     */
    public function initComponents(): void
    {
        foreach (self::$config['components'] as $component) {
            DebugBar::addComponentsMessage($component, 'Init component');

            try {
                /** @var Component $componentClass */
                $componentClass = new $component();
                self::$components[$component] = $componentClass;
                $componentClass->componentInit();
            } catch (Throwable $exception) {
                Log::log('Fail initialize component ' . $component, 'ERROR');
                DebugBar::addComponentsMessage('Fail initialize component ' . $component, 'ERROR');
                DebugBar::addThrowable($exception);

                continue;
            }
        }
    }

    /**
     * Initialize components after view
     * @return void
     * @throws Exception
     */
    public function initAfterComponents(): void
    {
        foreach (self::$components as $componentClass) {
            try {
                DebugBar::addComponentsMessage($componentClass, 'Init after component');
                $componentClass->componentInitAfter();
            } catch (Throwable $exception) {
                Log::log('Fail initialize after component', 'ERROR');
                DebugBar::addComponentsMessage('Fail initialize after component', 'ERROR');
                DebugBar::addThrowable($exception);

                continue;
            }
        }
    }

}