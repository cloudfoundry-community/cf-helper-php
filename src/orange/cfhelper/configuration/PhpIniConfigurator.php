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

namespace orange\cfhelper\configuration;


use orange\cfhelper\application\ApplicationInfo;
use orange\cfhelper\services\ServiceManager;

class PhpIniConfigurator
{
    public static $servicePhpIniName = 'php-ini';
    private $serviceManager;
    private $applicationInfo;
    private $config = array();

    function __construct()
    {
        $this->loadConfigCfHelper();
    }

    public function loadConfigCfHelper()
    {
        if (!is_file(__DIR__ . '/../../../../../../../composer.json')) {
            return;
        }
        $composerJson = json_decode(file_get_contents(__DIR__ . '/../../../../../../../composer.json'), true);
        if (empty($composerJson['cfhelper'])) {
            return;
        }
        $this->config = $composerJson['cfhelper'];
        $this->loadConfig();
    }

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

    public function loadIniConfig()
    {
        $arrayValues = array();
        try {

            $servicePhpIni = $this->serviceManager->getService(PhpIniConfigurator::$servicePhpIniName . '-' . $this->applicationInfo->getName());
            $arrayValues = $servicePhpIni->getValues();
        } catch (\Exception $e) {

        }
        if (is_file(__DIR__ . '/../../../../../../../composer.json')) {
            $composerJson = json_decode(file_get_contents(__DIR__ . '/../../../../../../../composer.json'), true);
            $arrayValues = array_merge($arrayValues, $composerJson['php-ini']);
        }
        foreach ($arrayValues as $key => $value) {
            ini_set($key, $value);
        }

    }

    /**
     * @return mixed
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @Required
     * @param mixed $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @return mixed
     */
    public function getApplicationInfo()
    {
        return $this->applicationInfo;
    }

    /**
     * @Required
     * @param mixed $applicationInfo
     */
    public function setApplicationInfo(ApplicationInfo $applicationInfo)
    {
        $this->applicationInfo = $applicationInfo;
    }


}