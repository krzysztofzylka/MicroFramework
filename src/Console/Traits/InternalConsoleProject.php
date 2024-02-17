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

            File::mkdir([
                $path,
                $path . '/resources/ajax'
            ], 0777);

            File::copyDirectory($this->frameworkPath . '/Console/resources/public', $path . '/public');
            File::copyDirectory($this->frameworkPath . '/Console/resources/resources', $path . '/resources', 0777);

            new Kernel($path);

            File::copy($this->frameworkPath . '/Console/resources/.gitignore', $path . '/.gitignore');
            File::copy($this->frameworkPath . '/Console/resources/package.json', $path . '/package.json');
            File::copy($this->frameworkPath . '/Console/resources/tailwind.config.js', $path . '/tailwind.config.js');
            File::copy($this->frameworkPath . '/Console/resources/src/public/css/tailwind.css', $path . '/src/public/css/tailwind.css');

        } catch (\Throwable $exception) {
            $this->print('Fail initialize project', 'red');
            $this->print($exception->getMessage(), 'red', true);
        }

        File::copyDirectory(__DIR__ . '/../../Template', $path . '/template');
        File::copyDirectory(__DIR__ . '/../../Extension/Ajax/assets', $path . '/resources/ajax');

        $this->print('Success initialize project', 'green');
    }

}