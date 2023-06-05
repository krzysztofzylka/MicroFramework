<?php

namespace Krzysztofzylka\MicroFramework;

use krzysztofzylka\DatabaseManager\Exception\SelectException;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Extension\Account\Account;
use Krzysztofzylka\MicroFramework\Extension\Memcache\Memcache;
use Krzysztofzylka\MicroFramework\Extension\Translation\Translation;

class Debug
{

    /**
     * Dane debugowania
     * @var array
     */
    public static array $data = [
        'authorize' => [],
        'sql' => [],
        'times' => [],
        'models' => [],
        'table' => [],
        'translation' => [],
        'views' => [],
        'env' => [],
        'memcache' => []
    ];

    public function __construct()
    {
        if (!$_ENV['config_debug']) {
            return;
        }

        self::$data['authorize'] = array_merge(self::$data['authorize'], $this->getAccountInfo());
        self::$data['translation'] = array_merge(self::$data['translation'], Translation::$translation);
        self::$data['sql'] = array_merge(self::$data['sql'], \krzysztofzylka\DatabaseManager\Debug::getSql());
        self::$data['env'] = array_merge(self::$data['env'], $_ENV);
        self::$data['memcache'] = array_merge(self::$data['memcache'], Memcache::$memcachedInstance->getAllKeys());

        self::$data['env']['database_password'] = '******';
        self::$data['env']['email_password'] = '******';
        self::$data['env']['logger_password'] = '******';
        self::$data['env']['logger_site_key'] = '******';
        self::$data['env']['logger_api_key'] = '******';

        foreach (self::$data['times'] as $id => $time) {
            self::$data['times'][$id] = number_format(str_contains($time, 'E-5') ? 0 : $time, 5, '.', '') . 's';
        }

        $this->loadView();

//        Memcache::set('debugData_' . Account::$accountId . '_' . ($_SERVER['REDIRECT_URL'] ?? '') . '_' . time() . '_' . random_int(1000, 9999), self::$data, 60 * 60);
    }

    /**
     * Generate account data
     * @return array
     * @throws SelectException
     */
    private function getAccountInfo(): array
    {
        $accountData = Account::$account;
        $accountData['account']['password'] = '******';

        return [
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
    }

    /**
     * Load debug view
     * @return void
     * @throws Exception\ViewException
     */
    public function loadView(): void
    {
        $view = new View();
        $this->viewLoaded = true;

        echo $view->render([
            'data' => self::$data,
            'serverTable' => $this->parseServerInfo()
        ], '/MicroFramework/Debug/debug');
    }

    /**
     * Generate server table
     * @return string
     */
    private function parseServerInfo(): string
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
        return ob_get_clean();
    }

}