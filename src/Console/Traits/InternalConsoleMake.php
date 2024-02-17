<?php

namespace Krzysztofzylka\MicroFramework\Console\Traits;

use Krzysztofzylka\Console\Form;
use Krzysztofzylka\File\File;
use Krzysztofzylka\MicroFramework\Exception\MicroFrameworkException;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\Reflection\Reflection;
use ReflectionException;

trait InternalConsoleMake
{

    /**
     * Make method
     * @param string $name
     * @return void
     * @throws MicroFrameworkException
     * @throws ReflectionException
     */
    private function make(string $name): void
    {
        switch ($name) {
            case 'controller':
                $controllerName = Form::input('Controller name:');
                $controllerDirectory = $this->path . '/src/Controller';
                $controllerPath = $controllerDirectory . '/' . $controllerName . '.php';

                if (file_exists($controllerPath)) {
                    $this->print('Controller ' . $controllerName . ' is already exists', color: 'red', exit: true);
                }

                File::mkdir([
                    $controllerDirectory,
                    $this->path . '/src/View/' . $controllerName
                ]);

                $controllerContent = file_get_contents($this->frameworkPath . '/Console/resources/make/controller.php');
                $controllerContent = str_replace('controllerName', $controllerName, $controllerContent);

                file_put_contents($controllerPath, $controllerContent);

                $this->print('Success create controller ' . $controllerName, color: 'green');

                break;
            case 'controller::method':
                Kernel::$silent = true;
                new Kernel($this->path);

                $controllerName = Form::input('Controller name:');
                $controllerDirectory = $this->path . '/src/Controller';
                $controllerPath = $controllerDirectory . '/' . $controllerName . '.php';

                if (!file_exists($controllerPath)) {
                    $this->print('Controller ' . $controllerName . ' not exists', color: 'red', exit: true);
                }

                $methodName = Form::input('Method name:');
                $methodList = array_column(Reflection::getClassMethods('\src\Controller\\' . $controllerName), 'method');

                if (in_array($methodName, $methodList)) {
                    $this->print('Method ' . $methodName . ' is already exists', color: 'red', exit: true);
                }

                $makeFile = 'method';

                if (Form::prompt('Create view', exit: false)) {
                    File::mkdir($this->path . '/src/View/' . $controllerName);
                    file_put_contents($this->path . '/src/View/' . $controllerName . '/' . $methodName . '.twig', '');
                    $makeFile = 'method_view';
                }

                $controllerContent = file_get_contents($controllerPath);
                $lastBracketPosition = $this->findLastBracketPosition($controllerContent) - 1;
                $methodContent = file_get_contents($this->frameworkPath . '/Console/resources/make/controller/' . $makeFile . '.php') . "\n\n";
                $methodContent = str_replace('methodName', $methodName, $methodContent);

                $newControllerContent = substr_replace(
                    $controllerContent,
                    $methodContent,
                    $lastBracketPosition-1,
                    0
                );

                file_put_contents($controllerPath, $newControllerContent);

                $this->print('Success create method ' . $methodName . ' in controller ' . $controllerName, color: 'green');
                break;
            case 'model':
                $modelName = $this->input('Model name:');
                $modelDirectory = $this->path . '/src/Model';
                $modelPath = $modelDirectory . '/' . $modelName . '.php';

                if (file_exists($modelPath)) {
                    $this->print('Model ' . $modelName . ' is already exists', color: 'red', exit: true);
                }

                File::mkdir($modelDirectory);

                $modelContent = file_get_contents($this->frameworkPath . '/Console/resources/make/model.php');
                $modelContent = str_replace('modelName', $modelName, $modelContent);

                file_put_contents($modelPath, $modelContent);

                $this->print('Success create model ' . $modelName, color: 'green');

                break;
            default:
                $this->renderHelp();
                break;
        }
    }

    /**
     * Find last bracket position
     * @param string $content
     * @return false|int
     */
    private function findLastBracketPosition(string $content): false|int
    {
        $lastBracketPosition = 0;
        $appendCount = 0;

        foreach (explode("\n", $content) as $line => $data) {
            if (trim($data) === '}') {
                $lastBracketPosition += $appendCount + strlen($data) + 1;
                $appendCount = 0;
            } else {
                $appendCount += strlen($data) + 1;
            }
        }

        return $lastBracketPosition === 0 ? false : $lastBracketPosition;
    }

}