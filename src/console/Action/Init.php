<?php

namespace Krzysztofzylka\MicroFramework\console\Action;

use Exception;
use krzysztofzylka\SimpleLibraries\Library\Console\Prints;
use krzysztofzylka\SimpleLibraries\Library\File;

class Init
{

    /**
     * Console object
     */
    private $console;

    /**
     * Init project
     * @param $console
     */
    public function __construct($console)
    {
        $this->console = $console;
        $this->console->initKernel();

        Prints::print('Init project in path "' . $this->console->path . '"', true);
        Prints::print('Create directories', true);

        try {
            File::mkdir($this->console->path . '/public', 0755);
            File::mkdir($this->console->path . '/public/assets', 0755);
            File::mkdir($this->console->path . '/app/controller', 0755);
            File::mkdir($this->console->path . '/app/model', 0755);
            File::mkdir($this->console->path . '/app/view', 0755);
            File::mkdir($this->console->path . '/public/assets', 0755);
            File::mkdir($this->console->path . '/api/controller', 0755);
            File::mkdir($this->console->path . '/storage', 0755);
            File::mkdir($this->console->path . '/storage/logs', 0755);
            File::mkdir($this->console->path . '/database_updater', 0755);
            File::mkdir($this->console->path . '/config', 0755);
        } catch (Exception $exception) {
            Prints::print('Failed create directory: ' . $exception->getMessage(), true, true);
        }

        Prints::print('Copy files', true);

        try {
            $fileList = [
                'public/index.php', 'public/.htaccess', 'config/.gitignore', 'storage/.gitignore', 'package.json', '.gitignore'
            ];

            foreach ($fileList as $filePath) {
                File::copy($this->console->resourcesPath . '/' . $filePath, $this->console->path . '/' . $filePath);
            }
        } catch (Exception $exception) {
            Prints::print('Failed copy file: ' . $exception->getMessage(), true, true);
        }

        Prints::print('Set files contents', true);

        try {
            $indexContent = file_get_contents($this->console->path . '/public/index.php');
            $indexContent = str_replace('{{vendorPath}}', self::getVendorPath($this->console->path) . '/autoload.php', $indexContent);
            file_put_contents($this->console->path . '/public/index.php', $indexContent);
        } catch (Exception $exception) {
            Prints::print('Failed set files contents: ' . $exception->getMessage(), true, true);
        }

        Prints::print('End init', true);
    }

    /**
     * Get vendor path
     * @param $path $console->path
     * @return string
     */
    public static function getVendorPath($path): ?string
    {
        if (file_exists($path . '/vendor')) {
            return $path . '/vendor';
        }

        for ($i = 0; $i <= 15; $i++) {
            $path = realpath($path . '/../');

            if (file_exists($path . '/vendor')) {
                return realpath($path . '/vendor');
            }
        }

        return '';
    }

}