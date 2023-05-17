<?php

namespace Krzysztofzylka\MicroFramework\Extension\Statistic;

use Exception;
use krzysztofzylka\DatabaseManager\Table;
use Krzysztofzylka\MicroFramework\Trait\Log;
use krzysztofzylka\SimpleLibraries\Library\Client;


/**
 * Statistics
 * @package Extension\Statistic
 */
class Statistic
{

    use Log;

    private Table $statisticInstance;
    private Table $statisticIpInstance;
    private Table $statisticVisitsInstance;

    /**
     * Save statistics
     */
    public function __construct()
    {
        try {
            if (!$_ENV['statistics_enabled'] || !$_ENV['database_enabled']) {
                return;
            }

            $ip = Client::getIP();

            $data = $this->geoplugin($ip);

            $this->statisticInstance = new Table('statistic');
            $this->statisticIpInstance = new Table('statistic_ip');
            $this->statisticVisitsInstance = new Table('statistic_visits');

            $statisticIp = $this->statisticIpInstance->find(['date' => date('Y-m-d'), 'ip' => $ip], ['id', 'visits']);
            $unique = false;

            if (!$statisticIp) {
                $unique = true;
                $this->statisticIpInstance->insert([
                    'date' => date('Y-m-d'),
                    'ip' => $ip,
                    'visits' => 1
                ]);

                $statisticIp = $this->statisticIpInstance->find(['date' => date('Y-m-d'), 'ip' => $ip], ['id', 'visits']);
            } else {
                $this->statisticIpInstance->setId($statisticIp['statistic_ip']['id'])->updateValue('visits', $statisticIp['statistic_ip']['visits'] + 1);
            }

            $statistic = $this->statisticInstance->find(['date' => date('Y-m-d')], ['id', 'visits', 'unique']);

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
                'browser' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'page' => $_GET['url'] ?? null
            ]);
        } catch (Exception $exception) {
            $this->log('Fail save statistic', 'ERR', ['exception' => $exception, 'ip' => $ip]);
        }
    }

    /**
     * Get data from geoplugin
     * @param string $ip
     * @return array
     */
    private function geoplugin(string $ip)
    {
        if (!$_ENV['statistics_analyze_ip']) {
            return [];
        }

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