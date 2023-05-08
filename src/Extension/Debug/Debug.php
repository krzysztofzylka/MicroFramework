<?php

namespace Krzysztofzylka\MicroFramework\Extension\Debug;

use Exception;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Exception\ViewException;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Translation\Translation;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\View;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

/**
 * Debug extension
 * @package Extension\Debug
 */
class Debug
{

    private FilesystemLoader $filesystemLoader;

    private Environment $environment;

    /**
     * Variables
     * @var array
     */
    public static array $variables = [];

    public function __construct()
    {
        try {
            $this->filesystemLoader = new FilesystemLoader(__DIR__ . '/../../Extension/Twig/TwigFiles');
            $this->environment = new Environment($this->filesystemLoader, ['debug' => true]);
            $this->environment->addExtension(new DebugExtension());
            $translationFunction = new TwigFunction('__', function (string $name) {
                return __($name);
            });
            $this->environment->addFunction($translationFunction);
            $this->environment->setCache(false);
            $view = new View();
            self::$variables['app'] = $view->getGlobalVariables();
            $this->generateSqlTable();
            $this->generateTranslationTable();
            $this->generateKernelTable();
            $this->generateTablesTable();
            $this->generateAccountTable();
            $this->generateEnvTable();
            $this->generateServerInfoTable();
            self::$variables['site_load']['end'] = number_format(microtime(true) - self::$variables['site_load']['start'], 4);
        } catch (Exception $exception) {
            throw new ViewException($exception->getMessage(), 500);
        }
    }

    /**
     * Render debug
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(): string
    {
        return $this->environment->render('MicroFramework/Layout/debug.twig', self::$variables);
    }

    /**
     * SQL list table
     * @return void
     */
    private function generateSqlTable(): void
    {
        ob_start();
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r(array_reverse(\krzysztofzylka\DatabaseManager\Debug::getSql()));
        self::$variables['sqlListTable'] = ob_get_clean();
    }

    /**
     * Translation table
     * @return void
     */
    private function generateTranslationTable(): void
    {
        ob_start();
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r(Translation::$translation);
        self::$variables['translationTable'] = ob_get_clean();
    }

    /**
     * Tables table
     * @return void
     */
    private function generateTablesTable(): void
    {
        ob_start();
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r(self::$variables['table'] ?? []);
        self::$variables['tablesTable'] = ob_get_clean();
    }

    /**
     * Kernel table
     * @return void
     */
    private function generateAccountTable(): void
    {
        if (!Account::isLogged()) {
            return;
        }

        $accountData = Account::$account;
        $accountData['account']['password'] = '******';

        $data = [
            'id' => Account::$accountId,
            'sessionName' => Account::$sessionName,
            'account' => $accountData,
            'rememberFields' => array_column((new Table('account_remember_field'))->findAll(['account_remember_field.account_id' => Account::$accountId]), 'account_remember_field'),
            'storage' => [
                'storageLocked' => Account::$storage->isLocked() ? 'True' : 'False',
                'storagePath' => Account::$storage->getDirectory() . Account::$storage->getIsolatorDirectory(),
                'storageDirectory' => Account::$storage->getDirectory(),
                'storageIsolatorDirectory' => Account::$storage->getIsolatorDirectory()
            ]
        ];

        ob_start();
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r($data);
        self::$variables['accountTable'] = ob_get_clean();
    }

    /**
     * Kernel table
     * @return void
     */
    private function generateKernelTable(): void
    {
        $data = [
            'projectPath' => Kernel::getProjectPath(),
            'url' => Kernel::$url ?? null,
            'data' => Kernel::getData(),
            'paths' => Kernel::getPath(null)
        ];

        ob_start();
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r($data);
        self::$variables['kernelTable'] = ob_get_clean();
    }

    /**
     * Env table
     * @return void
     */
    private function generateEnvTable(): void
    {
        $env = $_ENV;

        foreach (['database_password', 'logger_api_key', 'logger_site_key'] as $name) {
            if (!empty($env[$name])) {
                $env[$name] = '******';
            }
        }

        ob_start();
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r($env);
        self::$variables['envTable'] = ob_get_clean();
    }

    /**
     * ServerInfo table
     * @return void
     */
    private function generateServerInfoTable(): void
    {
        $srv = [
            'PHP_VERSION' => phpversion(),
        ];

        ob_start();
        echo '<br /><h5>PHP</h5>';
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r($srv);
        echo '<br /><h5>GET</h5>';
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r($_GET);
        echo '<br /><h5>POST</h5>';
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r($_POST);
        echo '<br /><h5>FILES</h5>';
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r($_FILES);
        echo '<br /><h5>$_COOKIE</h5>';
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r($_COOKIE);
        echo '<br /><h5>$_SESSION</h5>';
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r($_SESSION);
        echo '<br /><h5>$_REQUEST</h5>';
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r($_REQUEST);
        echo '<br />';
        echo '<br /><h5>$_SERVER</h5>';
        \krzysztofzylka\SimpleLibraries\Library\Debug::print_r($_SERVER);
        self::$variables['serverInfoTable'] = ob_get_clean();
    }

}