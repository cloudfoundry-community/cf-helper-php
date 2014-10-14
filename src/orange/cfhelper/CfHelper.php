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
use orange\cfhelper\services\ServiceManager;
use orange\cfhelper\simulator\CloudFoundrySimulator;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CfHelper
 * @package orange\cfhelper
 */
class CfHelper
{
    /**
     * @var null
     */
    private static $_instance = null;
    /**
     * @var \Arhframe\IocArt\BeanLoader|null
     */
    private $beanLoader;

    /**
     *
     */
    private function __construct()
    {
        $this->beanLoader = \Arhframe\IocArt\BeanLoader::getInstance();
        $this->setContext(__DIR__ . '/../../../context/main.yml');
    }

    /**
     * @param $contextFile
     */
    public function setContext($contextFile)
    {
        $this->beanLoader->loadContext(realpath($contextFile));
        $phpIniConfigurator = $this->beanLoader->getBean('cfhelper.phpIniConfigurator');
        $phpIniConfigurator->loadIniConfig();
    }

    /**
     * @return CfHelper
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new CfHelper();
        }
        return self::$_instance;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->beanLoader->getBean('cfhelper.serviceManager');
    }

    /**
     * @return ApplicationInfo
     */
    public function getApplicationInfo()
    {
        return $this->beanLoader->getBean('cfhelper.applicationInfo');
    }

    /**
     * @param string $manifestYml
     */
    public function simulateCloudFoundry($manifestYml = "manifest.yml")
    {
        CloudFoundrySimulator::simulate($manifestYml);
    }
}