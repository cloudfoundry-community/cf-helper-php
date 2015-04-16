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

/**
 * Class MongoDbConnector
 * @package CfCommunity\CfHelper\Connectors
 */
class MongoDbConnector extends AbstractConnector
{

    /**
     * @var \MongoClient;
     */
    private $connection;

    /**
     * @return \MongoClient
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
        $redisService = $this->serviceManager->getService('.*mongo.*');
        if ($redisService === null) {
            return;
        }
        $this->credentials = $this->parseFromService($redisService);
        $this->loadMongoDbConnection();
    }

    private function loadMongoDbConnection()
    {
        if (empty($this->credentials)) {
            return null;
        }
        if (!class_exists("\\MongoClient")) {
            return null;
        }
        $this->connection = new \MongoClient($this->credentials['url']);
    }
}