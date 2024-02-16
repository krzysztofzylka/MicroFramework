<?php

namespace Krzysztofzylka\MicroFramework\Console\Traits;

use Krzysztofzylka\Console\Generator\Table;

trait InternalConsoleComponent
{

    /**
     * Install component
     * @return void
     */
    private function installProjectComponent(): void
    {
        $componentName = $this->args['args'][2];
        $secured = isset($this->args['params']['secure']) ? (string)$this->args['params']['secure'] !== 'false' : true;

        $this->print('Install component: ' . $componentName);

        if (!$secured) {
            $this->print('Unsecured installer', 'yellow');
        }

        try {
            $packageName = $componentName;
            $this->print('Require composer package: ' . $packageName);
            $command = 'cd ../; composer require ' . $packageName;
            shell_exec($command);
            $packagePath = realpath(__DIR__ . '/../../vendor/' . $packageName);

            if (!file_exists($packagePath)) {
                $this->print('Failed install component', 'red', true);
            }

            $composerData = json_decode(file_get_contents($packagePath . '/composer.json'), true);
            $class = "\\" . array_key_first($composerData['autoload']['psr-4']) . 'Component';
            $componentList = json_decode(file_get_contents($this->path . '/component.json'), true);

            if (class_exists($class) && !in_array($class, $componentList['components'])) {
                $this->print('Add component to list');

                $componentList['components'][] = $class;

                file_put_contents($this->path . '/component.json', json_encode($componentList));
            }
        } catch (\Throwable $exception) {
            $this->print('Failed install component', 'red');
            $this->print($exception->getMessage(), 'red', true);
        }

        $this->print('Success install component', 'green');
    }

    /**
     * Project component lists
     * @return void
     */
    private function getProjectComponentList(): void
    {
        $componentFile = $this->path . '/component.json';

        if (!file_exists($componentFile)) {
            $this->print('Not found component.json', 'red', true);
        }

        $components = json_decode(file_get_contents($componentFile), true);
        $componentList = [];

        foreach ($components['components'] as $component) {
            $componentList[] = [
                'path' => $component
            ];
        }

        $table = new Table();
        $table->setData($componentList);
        $table->addColumn('Component', 'path');
        $table->render();
    }

}