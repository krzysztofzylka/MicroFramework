<?php

namespace Krzysztofzylka\MicroFramework\console\Trait;

use Exception;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use Krzysztofzylka\MicroFramework\Exception\DatabaseException;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Library\Console\Prints;

trait Database
{

    /**
     * @param $path
     * @return void
     */
    private function databaseConnect($path): void
    {
        try {
            Kernel::initPaths($path);
            Kernel::autoload();
            Kernel::loadEnv();
            Kernel::configDatabaseConnect();
        } catch (DatabaseManagerException $exception) {
            Prints::print('Database fail: ' . $exception->getHiddenMessage(), false, true);

            exit;
        } catch (DatabaseException $exception) {
            Prints::print('Database fail: ' . $exception->getHiddenMessage(), false, true);

            exit;
        } catch (Exception $exception) {
            Prints::print('Database fail: ' . $exception->getMessage(), false, true);

            exit;
        }
    }

}