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
class MongoDbConnector extends AbstractUriConnector implements Connector
{
    /**
     * @var \MongoDB\Client;
     */
    private $connection;

    /**
     * @return \MongoDB\Client
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
        $this->loadCredentials();
        $this->loadMongoDbConnection();
    }

    public function loadCredentials()
    {
        $nameOrTagsToFind = '.*mongo.*';
        $mongodbService = $this->serviceManager->getService($nameOrTagsToFind);
        if ($mongodbService === null) {
            $mongodbService = $this->serviceManager->getServiceByTags($nameOrTagsToFind);
        }
        if ($mongodbService === null) {
            return;
        }
        $this->credentials = $this->parseFromService($mongodbService);
    }

    private function loadMongoDbConnection()
    {
        if (empty($this->credentials)) {
            return null;
        }
        if (!class_exists("MongoDB\\Client")) {
            return null;
        }
        $this->connection = new MongoDB\Client($this->credentials['url']);
    }

    public function getName()
    {
        return "mongoDb";
    }
}
