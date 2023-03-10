<?php

namespace Krzysztofzylka\MicroFramework\AdminPanel\model;

use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\Model;
use krzysztofzylka\SimpleLibraries\Library\File;

class log extends Model
{

    /**
     * Get log list
     * @return array
     * [['path' => '', 'fileName' => '', 'logName' => ''], ...]
     */
    public static function getList(): array
    {
        $path = File::repairPath(Kernel::getPath('logs'));
        $list = [];

        File::mkdir($path);

        foreach (glob($path . '/*') as $logPath) {
            $list[] = [
                'path' => File::repairPath($logPath),
                'fileName' => basename($logPath),
                'logName' => explode('.', basename($logPath))[0]
            ];
        }

        return $list;
    }

    /**
     * Read file log
     * @param $path
     * @return array
     */
    public function fileRead($path): array
    {
        $return = [];
        $contents = file_get_contents($path);

        foreach (explode(PHP_EOL, $contents) as $logId => $content) {
            $decode = json_decode($content, true);

            if (!$decode) {
                continue;
            } elseif (!is_array($decode)) {
                $decode = [$decode];
            }

            $return[]['log'] = ['logLineNumber' => explode('.', basename($path))[0] . '-' . $logId + 1, ...$decode];
        }

        return $return;
    }

}