<?php
/**
 * Copyright (C) 2015 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 18/03/2015
 */


namespace CfCommunity\CfHelper\Connectors;

use Predis\Client;

class RedisConnector extends AbstractConnector
{
    /**
     * @var Client;
     */
    private $connection;

    /**
     * @return Client
     */
    public function getConnection()
    {
        if ($this->connection === null) {
            $this->load();
        }
        return $this->connection;
    }

    public function load()
    {
        $redisService = $this->serviceManager->getService('.*redis.*');
        if ($redisService === null) {
            return;
        }
        $this->credentials = $this->parseFromService($redisService);
        $this->loadRedisConnection();
    }

    private function loadRedisConnection()
    {
        if (empty($this->credentials)) {
            return null;
        }
        if (!class_exists("Predis\\Client")) {
            return null;
        }
        $this->connection = new Client([
            'scheme' => 'tcp',
            'host' => $this->credentials['host'],
            'port' => $this->credentials['port'],
            'password' => $this->credentials['pass'],
        ]);
    }
}