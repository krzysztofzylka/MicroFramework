<?php

namespace Krzysztofzylka\MicroFramework\Extension\Statistic;

use Exception;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Kernel;
use Krzysztofzylka\MicroFramework\Trait\Log;
use krzysztofzylka\SimpleLibraries\Library\Client;

class Statistic {

    use Log;

    private Table $statisticInstance;
    private Table $statisticIpInstance;
    private Table $statisticVisitsInstance;

    //save statistics
    public function __construct() {
        try {
            if (!Kernel::getConfig()->statistics || !Kernel::getConfig()->database) {
                return;
            }

            $ip = Client::getIP();

            $data = $this->geoplugin($ip);

            $this->statisticInstance = new Table('statistic');
            $this->statisticIpInstance = new Table('statistic_ip');
            $this->statisticVisitsInstance = new Table('statistic_visits');

            $statisticIp = $this->statisticIpInstance->find(['date' => date('Y-m-d'), 'ip' => $ip]);
            $unique = false;

            if (!$statisticIp) {
                $unique = true;
                $this->statisticIpInstance->insert([
                    'date' => date('Y-m-d'),
                    'ip' => $ip,
                    'visits' => 1
                ]);

                $statisticIp = $this->statisticIpInstance->find(['date' => date('Y-m-d'), 'ip' => $ip]);
            } else {
                $this->statisticIpInstance->setId($statisticIp['statistic_ip']['id'])->updateValue('visits', $statisticIp['statistic_ip']['visits'] + 1);
            }

            $statistic = $this->statisticInstance->find(['date' => date('Y-m-d')]);

            if (!$statistic) {
                $this->statisticInstance->insert([
                    'date' => date('Y-m-d'),
                    'unique' => 1,
                    'visits' => 1
                ]);
            } else {
                $this->statisticInstance->setId($statistic['statistic']['id'])->updateValue('visits', $statistic['statistic']['visits'] + 1);

                if ($unique) {
                    $this->statisticInstance->setId($statistic['statistic']['id'])->updateValue('unique', $statistic['statistic']['unique'] + 1);
                }
            }

            $this->statisticVisitsInstance->insert([
                'statistic_ip_id' => $statisticIp['statistic_ip']['id'],
                'country' => $data['country'] ?? null,
                'city' => $data['city'] ?? null,
                'continent' => $data['continent'] ?? null,
                'browser' => $_SERVER['HTTP_USER_AGENT'],
                'page' => $_GET['url'] ?? null
            ]);
        } catch (Exception $exception) {
            $this->log('Fail save statistic', 'ERR', ['exception' => $exception, 'ip' => $ip]);
        }
    }

    private function geoplugin(string $ip) {
        try {
            $url = 'http://www.geoplugin.net/php.gp?ip=' . $ip;
            $data = unserialize(file_get_contents($url));

            if ($data['geoplugin_status'] === 200) {
                return [
                    'continent' => $data['geoplugin_continentName'] ?? null,
                    'country' => $data['geoplugin_countryName'] ?? null,
                    'city' => $data['geoplugin_city'] ?? null
                ];
            }

            return [];
        } catch (Exception $exception) {
            $this->log('Geoplugin problem', 'ERR', ['exception' => $exception, 'ip' => $ip, 'url' => $url]);

            return [];
        }
    }

}