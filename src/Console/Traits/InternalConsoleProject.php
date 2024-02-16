<?php

namespace Krzysztofzylka\MicroFramework\Console\Traits;

use Krzysztofzylka\File\File;
use Krzysztofzylka\MicroFramework\Kernel;

trait InternalConsoleProject
{

    /**
     * Initialize project
     * @param string $path
     * @return void
     */
    private function initializeProject(string $path): void
    {
        $this->print('Initialize project');
        $this->print('Project path: ' . $path);

        try {
            File::mkdir([
                $path,
                $path . '/public'
            ], 0755);
            File::copyDirectory($this->frameworkPath . '/Console/resources/public', $path . '/public');
            new Kernel($path);
        } catch (\Throwable $exception) {
            $this->print('Fail initialize project', 'red');
            $this->print($exception->getMessage(), 'red', true);
        }

        $this->print('Success initialize project', 'green');
    }

}