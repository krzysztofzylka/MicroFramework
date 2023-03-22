<?php

namespace Krzysztofzylka\MicroFramework\bin\Action;

use Exception;
use Krzysztofzylka\MicroFramework\bin\Console\Console;
use Krzysztofzylka\MicroFramework\bin\Trait\Prints;
use krzysztofzylka\SimpleLibraries\Library\File;

class Init
{

    use Prints;

    /**
     * Console object
     * @var Console
     */
    private Console $console;

    /**
     * Init project
     * @param Console $console
     */
    public function __construct(Console $console)
    {
        $this->console = $console;

        $this->tprint('Init project in path "' . $this->console->path . '"');
        $this->tprint('Create directories');

        try {
            File::mkdir($this->console->path . '/public', 0755);
            File::mkdir($this->console->path . '/public/assets', 0755);
            File::mkdir($this->console->path . '/app/controller', 0755);
            File::mkdir($this->console->path . '/app/model', 0755);
            File::mkdir($this->console->path . '/app/view', 0755);
            File::mkdir($this->console->path . '/public/assets', 0755);
            File::mkdir($this->console->path . '/api/controller', 0755);
            File::mkdir($this->console->path . '/admin_panel/view', 0755);
            File::mkdir($this->console->path . '/admin_panel/controller', 0755);
            File::mkdir($this->console->path . '/admin_panel/model', 0755);
            File::mkdir($this->console->path . '/storage', 0755);
            File::mkdir($this->console->path . '/storage/logs', 0755);
            File::mkdir($this->console->path . '/database_updater', 0755);
            File::mkdir($this->console->path . '/config', 0755);
        } catch (Exception $exception) {
            $this->dtprint('Failed create directory: ' . $exception->getMessage());
        }

        $this->tprint('Copy files');

        try {
            File::copy($this->console->resourcesPath . '/public/index.php', $this->console->path . '/public/index.php');
            File::copy($this->console->resourcesPath . '/public/.htaccess', $this->console->path . '/public/.htaccess');
            File::copy($this->console->resourcesPath . '/config/Config.php', $this->console->path . '/config/Config.php');
            File::copy($this->console->resourcesPath . '/config/.gitignore', $this->console->path . '/config/.gitignore');
            File::copy($this->console->resourcesPath . '/public/assets/dialogbox.css', $this->console->path . '/public/assets/dialogbox.css');
            File::copy($this->console->resourcesPath . '/public/assets/dialogbox.js', $this->console->path . '/public/assets/dialogbox.js');
            File::copy($this->console->resourcesPath . '/public/assets/spinner.css', $this->console->path . '/public/assets/spinner.css');
            File::copy($this->console->resourcesPath . '/public/assets/spinner.js', $this->console->path . '/public/assets/spinner.js');
        } catch (Exception $exception) {
            $this->dtprint('Failed copy file: ' . $exception->getMessage());
        }

        $this->dtprint('End init');
    }

}