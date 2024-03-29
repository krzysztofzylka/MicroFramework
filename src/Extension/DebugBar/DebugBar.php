<?php

namespace Krzysztofzylka\MicroFramework\Extension\DebugBar;

use DebugBar\DataCollector\ConfigCollector;
use DebugBar\DataCollector\MessagesCollector;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DebugBarException;
use DebugBar\JavascriptRenderer;
use DebugBar\StandardDebugBar;
use Exception;
use krzysztofzylka\DatabaseManager\DatabaseManager;
use Krzysztofzylka\File\File;
use Krzysztofzylka\MicroFramework\Kernel;

/**
 * Debug bar
 */
class DebugBar
{

    /**
     * Is init
     * @var bool
     */
    public static bool $init = false;

    /**
     * StandardDebugBar instance
     * @var StandardDebugBar
     */
    public static StandardDebugBar $standardDebugBar;

    /**
     * StandardDebugBarRenderer instance
     * @var JavascriptRenderer
     */
    private static JavascriptRenderer $standardDebugBarRenderer;

    /**
     * Add message
     * @param mixed $message
     * @param mixed $label
     * @return void
     */
    public static function addMessage(mixed $message, mixed $label = ''): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['messages']->addMessage($message, $label);
    }

    /**
     * Add message
     * @param mixed $message
     * @param mixed $label
     * @return void
     */
    public static function addFrameworkMessage(mixed $message, mixed $label = ''): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['framework']->addMessage($message, $label);
    }

    /**
     * Time start
     * @param string $name
     * @param string $label
     * @return void
     */
    public static function timeStart(string $name = 'time', string $label = ''): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['time']->startMeasure($name, $label);
    }

    /**
     * Time stop
     * @param string $name
     * @return void
     */
    public static function timeStop(string $name = 'time'): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['time']->stopMeasure($name);
    }

    /**
     * Add throwable
     * @param $exception
     * @return void
     */
    public static function addThrowable($exception): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['exceptions']->addThrowable($exception);
    }

    /**
     * Add model message
     * @param $model
     * @return void
     */
    public static function addModelMessage($model): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['models']->addMessage($model);
    }

    /**
     * Add log message
     * @param mixed $message
     * @param string $label
     * @return void
     */
    public static function addLogMessage(mixed $message, mixed $label = ''): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['logs']->addMessage($message, $label);
    }

    /**
     * Add log message
     * @param mixed $message
     * @param string $label
     * @return void
     */
    public static function addFormMessage(mixed $message, mixed $label = ''): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['forms']->addMessage($message, $label);
    }

    /**
     * Add components message
     * @param mixed $message
     * @param string $label
     * @return void
     */
    public static function addComponentsMessage(mixed $message, mixed $label = ''): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['components']->addMessage($message, $label);
    }

    /**
     * Add views message
     * @param mixed $message
     * @param string $label
     * @return void
     */
    public static function addViewMessage(mixed $message, mixed $label = ''): void
    {
        if (!self::$init) {
            return;
        }

        self::$standardDebugBar['views']->addMessage($message, $label);
    }

    /**
     * Render
     * @return string
     */
    public static function render(): string
    {
        if (!self::$init) {
            return '';
        }

        if (!isset(self::$standardDebugBar['ENV'])) {
            self::$standardDebugBar->addCollector(new ConfigCollector($_ENV, 'ENV'));
        }

        return self::$standardDebugBarRenderer->render();
    }

    /**
     * Init
     * @return void
     * @throws DebugBarException
     */
    public function init(): void
    {
        self::$init = true;
        self::$standardDebugBar = new StandardDebugBar();
        self::$standardDebugBarRenderer = self::$standardDebugBar->getJavascriptRenderer();
        self::$standardDebugBar->addCollector(new MessagesCollector('framework'));
        self::$standardDebugBar->addCollector(new MessagesCollector('models'));
        self::$standardDebugBar->addCollector(new MessagesCollector('logs'));
        self::$standardDebugBar->addCollector(new MessagesCollector('forms'));
        self::$standardDebugBar->addCollector(new MessagesCollector('components'));
        self::$standardDebugBar->addCollector(new MessagesCollector('views'));
    }

    /**
     * Add PDO collection
     * @return void
     * @throws DebugBarException
     */
    public static function addPdoCollection(): void
    {
        if (!self::$init) {
            return;
        }

        if ($_ENV['DATABASE']) {
            $pdoCollector = new PDOCollector();
            $pdoCollector->addConnection(DatabaseManager::$connection->getConnection());

            self::$standardDebugBar->addCollector($pdoCollector);
        }
    }

}