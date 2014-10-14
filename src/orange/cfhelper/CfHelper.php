<?php
/**
 * Copyright (C) 2014 Orange
 *
 * This software is distributed under the terms and conditions of the 'MIT' license which can be
 * found in the file 'LICENSE' in this package distribution or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 01-07-2014
 */

namespace orange\cfhelper;

use orange\cfhelper\application\ApplicationInfo;
use orange\cfhelper\configuration\PhpIniConfigurator;
use orange\cfhelper\services\ServiceManager;
use orange\cfhelper\simulator\CloudFoundrySimulator;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CfHelper
 * @package orange\cfhelper
 */
class CfHelper
{
    const DETECT_CLOUDFOUNDRY = 'VCAP_APPLICATION';
    /**
     * @var CfHelper
     */
    private static $_instance = null;
    /**
     * @var \Arhframe\IocArt\BeanLoader|null
     */
    private $beanLoader;
    /**
     * @var PhpIniConfigurator
     */
    private $phpIniConfigurator;

    /**
     * @var ServiceManager
     */
    private $serviceManager;
    /**
     * @var ApplicationInfo
     */
    private $applicationInfo;

    /**
     *
     */
    public function __construct()
    {
        $this->beanLoader = \Arhframe\IocArt\BeanLoader::getInstance();
    }

    /**
     * @return CfHelper
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new CfHelper();
            self::$_instance->loadDefaultContext();
        }
        return self::$_instance;
    }

    public function loadDefaultContext()
    {
        $this->beanLoader->loadContext(realpath(__DIR__ . '/../../../context/main.yml'));
        $this->setPhpIniConfigurator($this->beanLoader->getBean('cfhelper.phpIniConfigurator'));
        $this->serviceManager = $this->beanLoader->getBean('cfhelper.serviceManager');
        $this->applicationInfo = $this->beanLoader->getBean('cfhelper.applicationInfo');
    }


    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @param ServiceManager $serviceManager
     * @Required
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @return ApplicationInfo
     */
    public function getApplicationInfo()
    {
        return $this->applicationInfo;
    }

    /**
     * @param ApplicationInfo $applicationInfo
     * @Required
     */
    public function setApplicationInfo(ApplicationInfo $applicationInfo)
    {
        $this->applicationInfo = $applicationInfo;
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
     * @return PhpIniConfigurator
     */
    public function getPhpIniConfigurator()
    {
        return $this->phpIniConfigurator;
    }

    /**
     * @param PhpIniConfigurator $phpIniConfigurator
     * @Required
     */
    public function setPhpIniConfigurator(PhpIniConfigurator $phpIniConfigurator)
    {
        $this->phpIniConfigurator = $phpIniConfigurator;
        $this->phpIniConfigurator->loadIniConfig();
    }

}