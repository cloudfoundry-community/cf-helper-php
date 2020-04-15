<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 08/03/2015
 */

namespace CfCommunity\CfHelper\Connectors;

use CfCommunity\CfHelper\Services\Service;


/**
 * Class DatabaseConnector
 * @package CfCommunity\CfHelper\Connectors
 */
class DatabaseConnector extends AbstractUriConnector implements Connector
{
    private const TABLE_NAME = 'rest_proxify';
    private const SENTENCE_PDO = '%s:host=%s;%sdbname=%s';
    private const DBTYPE_PG = '(postgres|pgsql)';
    private const DBTYPE_MYSQL = '(mariadb|mysql)';
    private const DBTYPE_ORACLE = '(oracle|oci)';
    private const DBTYPE_SQLITE = 'sqlite';
    /**
     * @var \PDO;
     */
    private $connection;

    /**
     * @return \PDO
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
        $this->loadDatabaseFromDbParsed();
    }

    public function loadCredentials()
    {
        $dbToFind = implode('|', [
            self::DBTYPE_ORACLE,
            self::DBTYPE_MYSQL,
            self::DBTYPE_PG,
            self::DBTYPE_SQLITE
        ]);
        $nameOrTagsToFind = '.*(db|database|(' . $dbToFind . ')).*';
        $dbService = $this->serviceManager->getService($nameOrTagsToFind);
        if ($dbService === null) {
            $dbService = $this->serviceManager->getServiceByTags($nameOrTagsToFind);
        }
        if ($dbService === null) {
            return;
        }
        $this->credentials = $this->parseDbFromService($dbService);
    }

    public function parseDbFromService(Service $service)
    {
        $toReturn = $this->parseFromService($service);


        if (isset($toReturn['scheme']) && !empty($toReturn['scheme'])) {
            $type = $this->getDbTypeFromServiceName($toReturn['scheme']);
        } else {
            $type = $this->getDbTypeFromServiceName($service->getValue('.*(type).*'));
        }
        if (empty($toReturn['path'])) {
            $database = $service->getValue('.*(database|db).*');
        } else {
            $database = $toReturn['path'];
        }
        $toReturn['database'] = $database;
        if (empty($type)) {
            $type = $this->getDbTypeFromServiceName($service->getName());
        }
        if (empty($type)) {
            $type = $this->getDbTypeFromServiceName($service->getLabel());
        }
        $toReturn['type'] = $type;
        if (!empty($toReturn['port'])) {
            $portPdo = sprintf("port=%s;", $toReturn['port']);
        } else {
            $portPdo = "";
        }
        $toReturn['sentencePdo'] = sprintf(self::SENTENCE_PDO, $type,
            $toReturn['host'], $portPdo, $database);

        return $toReturn;
    }

    private function getDbTypeFromServiceName($serviceName)
    {
        if (preg_match('#.*' . self::DBTYPE_MYSQL . '.*#i', $serviceName)) {
            return "mysql";
        }
        if (preg_match('#.*' . self::DBTYPE_ORACLE . '.*#i', $serviceName)) {
            return "oci";
        }
        if (preg_match('#.*' . self::DBTYPE_PG . '.*#i', $serviceName)) {
            return "pgsql";
        }
        if (preg_match('#.*' . self::DBTYPE_SQLITE . '.*#i', $serviceName)) {
            return "sqlite";
        }
        return null;
    }

    private function loadDatabaseFromDbParsed()
    {
        if (!class_exists("\\PDO")) {
            return null;
        }
        if (is_array($this->credentials)) {
            $this->connection = new \PDO($this->credentials['sentencePdo'], $this->credentials['user'], $this->credentials['pass']);
        } else {
            $this->connection = new \PDO($this->credentials);
        }
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function getName()
    {
        return "database";
    }
}
