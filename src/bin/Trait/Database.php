<?php

namespace Krzysztofzylka\MicroFramework\bin\Trait;

use config\Config;
use Exception;
use krzysztofzylka\DatabaseManager\Exception\DatabaseManagerException;
use Krzysztofzylka\MicroFramework\Exception\DatabaseException;
use Krzysztofzylka\MicroFramework\Exception\NotFoundException;
use Krzysztofzylka\MicroFramework\Kernel;
use krzysztofzylka\SimpleLibraries\Exception\SimpleLibraryException;

trait Database
{

    /**
     * @param $path
     * @return void
     * @throws NotFoundException
     * @throws SimpleLibraryException
     */
    private function databaseConnect($path)
    {
        try {
            Kernel::initPaths($path);
            Kernel::autoload();
            Kernel::setConfig(new Config());
            Kernel::configDatabaseConnect();
        } catch (DatabaseManagerException $exception) {
            $this->dtprint('Database fail: ' . $exception->getHiddenMessage());

            exit;
        } catch (DatabaseException $exception) {
            $this->dtprint('Database fail: ' . $exception->getHiddenMessage());

            exit;
        } catch (Exception $exception) {
            $this->dtprint('Database fail: ' . $exception->getMessage());

            exit;
        }
    }

}