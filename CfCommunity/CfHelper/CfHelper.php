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

use Arthurh\Sphring\Runner\SphringRunner;
use Arthurh\Sphring\Sphring;
use CfCommunity\CfHelper\Application\ApplicationInfo;
use CfCommunity\CfHelper\Configuration\PhpIniConfigurator;
use CfCommunity\CfHelper\Connectors\AbstractConnector;
use CfCommunity\CfHelper\Connectors\DatabaseConnector;
use CfCommunity\CfHelper\Connectors\MongoDbConnector;
use CfCommunity\CfHelper\Connectors\RedisConnector;
use CfCommunity\CfHelper\Logger\CloudFoundryLogger;
use CfCommunity\CfHelper\Services\ServiceManager;
use CfCommunity\CfHelper\Simulator\CloudFoundrySimulator;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CfHelper
 * @package CfCommunity\CfHelper
 */
class CfHelper extends SphringRunner
{
    const DETECT_CLOUDFOUNDRY = 'VCAP_APPLICATION';


    public function __construct()
    {
        parent::__construct();
        $this->getSphring()->setFilename(__DIR__ . '/sphring/main.yml');
    }

    /**
     * @return PhpIniConfigurator
     */
    public function getPhpIniConfigurator()
    {
        return $this->getSphring()->getBean('cfhelper.phpIniConfigurator');
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->getSphring()->getBean('cfhelper.serviceManager');
    }


    /**
     * @return ApplicationInfo
     */
    public function getApplicationInfo()
    {
        return $this->getSphring()->getBean('cfhelper.applicationInfo');
    }

    /**
     * @param string $manifestYml
     */
    public function simulateCloudFoundry($manifestYml = "manifest.yml")
    {
        CloudFoundrySimulator::simulate($manifestYml);
    }

    public function isInCloudFoundry()
    {
        return !empty($_ENV[self::DETECT_CLOUDFOUNDRY]);
    }

    /**
     * @return CloudFoundryLogger
     */
    public function getLogger()
    {
        return $this->getSphring()->getBean('cfhelper.logger.logger');
    }

    /**
     * @return AbstractConnector[]
     */
    public function getConnectors()
    {
        return $this->getSphring()->getBean('cfhelper.connectors');
    }

    /**
     * @return DatabaseConnector
     */
    public function getDatabaseConnector()
    {
        return $this->getSphring()->getBean('cfhelper.connector.database');
    }

    /**
     * @return RedisConnector
     */
    public function getRedisConnector()
    {
        return $this->getSphring()->getBean('cfhelper.connector.redis');
    }

    /**
     * @return MongoDbConnector
     */
    public function getMongoDbConnector()
    {
        return $this->getSphring()->getBean('cfhelper.connector.mongo');
    }
}