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

namespace CfCommunity\CfHelper\Configuration;

use CfCommunity\CfHelper\Services\ServiceManager;
use CfCommunity\CfHelper\Application\ApplicationInfo;


/**
 * Class PhpIniConfigurator
 * @package CfCommunity\CfHelper\Configuration
 */
class PhpIniConfigurator
{
    const FILECONFIGURATION = "cfhelper.json";
    /**
     * @var string
     */
    public static $servicePhpIniName = 'php-ini';
    /**
     * @var ServiceManager
     */
    private $serviceManager;
    /**
     * @var ApplicationInfo
     */
    private $applicationInfo;
    /**
     * @var array
     */
    private $config = array();

    /**
     *
     */
    function __construct()
    {
        $this->loadConfigCfHelper();
    }

    /**
     *
     */
    public function loadConfigCfHelper()
    {
        if (!is_file(__DIR__ . '/../../../../../../../' . PhpIniConfigurator::FILECONFIGURATION)) {
            return;
        }
        $composerJson = json_decode(file_get_contents(__DIR__ . '/../../../../../../../' . PhpIniConfigurator::FILECONFIGURATION), true);
        if (empty($composerJson['cfhelper'])) {
            return;
        }
        $this->config = $composerJson['cfhelper'];
        $this->loadConfig();
    }

    /**
     *
     */
    public function loadConfig()
    {
        if (!empty($this->config['type']) && $this->config['type'] == 'development') {
            ini_set("display_errors", "On");
            ini_set("error_reporting", E_ALL & ~E_NOTICE);
            $whoops = new \Whoops\Run;
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $whoops->register();
        } else {
            ini_set("display_errors", "Off");
            ini_set("error_reporting", E_ALL & ~E_DEPRECATED);
        }
    }

    /**
     *
     */
    public function loadIniConfig()
    {
        $arrayValues = array();
        $servicePhpIni = $this->serviceManager->getService(PhpIniConfigurator::$servicePhpIniName . '-' . $this->applicationInfo->getName());
        if ($servicePhpIni != null) {
            $arrayValues = $servicePhpIni->getValues();
        }
        if (is_file(__DIR__ . '/../../../../../../../' . PhpIniConfigurator::FILECONFIGURATION)) {
            $composerJson = json_decode(file_get_contents(__DIR__ . '/../../../../../../../' . PhpIniConfigurator::FILECONFIGURATION), true);
            if (empty($composerJson['php-ini'])) {
                return;
            }
            $arrayValues = array_merge($arrayValues, $composerJson['php-ini']);
        }
        foreach ($arrayValues as $key => $value) {
            ini_set($key, $value);
        }

    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @Required
     * @param ServiceManager $serviceManager
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
     * @Required
     * @param ApplicationInfo $applicationInfo
     */
    public function setApplicationInfo(ApplicationInfo $applicationInfo)
    {
        $this->applicationInfo = $applicationInfo;
    }


}
