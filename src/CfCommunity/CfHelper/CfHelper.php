<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT' license which can be
 * found in the file 'LICENSE' in this package distribution or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 01-07-2014
 */

namespace CfCommunity\CfHelper;

use CfCommunity\CfHelper\Application\ApplicationInfo;
use CfCommunity\CfHelper\Connectors\Connector;
use CfCommunity\CfHelper\Connectors\DatabaseConnector;
use CfCommunity\CfHelper\Connectors\MongoDbConnector;
use CfCommunity\CfHelper\Connectors\RedisConnector;
use CfCommunity\CfHelper\Exception\ConnectorNotFoundException;
use CfCommunity\CfHelper\Exception\ConnectorNotUniqException;
use CfCommunity\CfHelper\Services\Populator;
use CfCommunity\CfHelper\Services\ServiceManager;
use CfCommunity\CfHelper\Simulator\CloudFoundrySimulator;

/**
 * Class CfHelper
 * @package CfCommunity\CfHelper
 */
class CfHelper
{
    const DETECT_CLOUDFOUNDRY = 'VCAP_APPLICATION';

    private $serviceManager;

    /**
     * @var Connector[]
     */
    private $connectors = array();

    /**
     * @var
     */
    private $connectorsState = array();

    public function __construct(ServiceManager $serviceManager = null)
    {
        if (empty($serviceManager)) {
            $serviceManager = new ServiceManager();
        }
        $this->serviceManager = $serviceManager;
        $this->addConnector(new DatabaseConnector());
        $this->addConnector(new MongoDbConnector());
        $this->addConnector(new RedisConnector());
    }

    public function addConnector(Connector $connector)
    {
        if ($this->hasConnector($connector)) {
            throw new ConnectorNotUniqException($connector);
        }
        $this->connectors[] = $connector;
        $connector->setServiceManager($this->serviceManager);
    }

    /**
     * @param Connector $connector
     * @return bool
     */
    public function hasConnector(Connector $connector)
    {
        foreach ($this->connectors as $conn) {
            if ($conn->getName() == $connector->getName()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @return Populator
     */
    public function getPopulator()
    {
        return $this->serviceManager->getPopulator();
    }

    /**
     * @return ApplicationInfo
     */
    public function getApplicationInfo()
    {
        return $this->serviceManager->getPopulator()->getApplicationInfo();
    }

    /**
     * @param string $manifestYml
     */
    public function simulateCloudFoundry($manifestYml = "services.json")
    {
        CloudFoundrySimulator::simulate($manifestYml);
    }

    public function isInCloudFoundry()
    {
        return !empty(getenv(self::DETECT_CLOUDFOUNDRY));
    }

    /**
     * @return Connector[]
     */
    public function getConnectors()
    {
        return $this->connectors;
    }

    public function __call($methodName, $arguments)
    {
        if (substr($methodName, 0, 3) !== 'get') {
            throw new \Exception('Method ' . $methodName . ' not exists');
        }
        if (substr($methodName, -strlen("Connector")) !== "Connector") {
            throw new \Exception('Method ' . $methodName . ' not exists');
        }
        $connectorName = substr($methodName, 3);
        $connectorName = substr_replace($connectorName, "Connector", 0);

        $connector = $this->getConnector($connectorName);
        if (isset($this->connectorsState[$connector->getName()])) {
            return $connector;
        }
        $this->connectorsState[$connector->getName()] = true;
        $connector->load();
        return $connector;
    }

    /**
     * @param $name
     * @return Connector
     * @throws ConnectorNotFoundException
     */
    public function getConnector($name)
    {
        foreach ($this->connectors as $conn) {
            if ($conn->getName() == $name) {
                return $conn;
            }
        }
        throw new ConnectorNotFoundException($name);
    }
}