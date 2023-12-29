<?php

namespace Krzysztofzylka\MicroFramework\Component;

use Exception;
use Krzysztofzylka\Env\Env;
use Krzysztofzylka\MicroFramework\Extension\DebugBar\DebugBar;
use Krzysztofzylka\MicroFramework\Extension\Log\Log;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\Reflection\Reflection;
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
    private static array $config = [];

    /**
     * Components
     * @var array
     */
    private static array $components = [];

    /**
     * Is ini
     * @var bool
     */
    public static bool $init = false;

    /**
     * Get component config
     * @return array
     */
    public static function getConfig(): array
    {
        return self::$config;
    }

    /**
     * Component is loaded
     * @param string $name
     * @return bool
     */
    public static function componentIsLoaded(string $name): bool
    {
        return array_key_exists($name, self::$components);
    }

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
            self::$init = true;

            foreach (self::$config['components'] as $component) {
                try {
                    $envFile = Reflection::getDirectoryPath($component) . '/.env';

                    if (file_exists($envFile)) {
                        (new Env($envFile))->load();
                    }
                } catch (Throwable) {
                }
            }
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