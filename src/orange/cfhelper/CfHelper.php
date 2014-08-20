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

use orange\cfhelper\simulator\CloudFoundrySimulator;
use Symfony\Component\Yaml\Yaml;

class CfHelper
{
    private static $_instance = null;
    private $beanLoader;

    private function __construct()
    {
        $this->beanLoader = \Arhframe\IocArt\BeanLoader::getInstance();
        if (!is_file(__DIR__ . '/../../../../../../context/main.yml')) {
            $yaml = Yaml::parse(__DIR__ . '/../../../context/main.yml');
            unset($yaml['@import']);
            $yaml = Yaml::dump($yaml);
            file_put_contents(__DIR__ . '/../../../context/main.yml', $yaml);
            $this->beanLoader->loadContext(__DIR__ . '/../../../context/main.yml');
        } else {
            $yaml = Yaml::parse(__DIR__ . '/context/main.yml');
            $yaml['@import'] = array('../../../../context/main.yml');
            $yaml = Yaml::dump($yaml);
            file_put_contents(__DIR__ . '/../../../context/main.yml', $yaml);
            $this->beanLoader->loadContext(__DIR__ . '/../../../context/main.yml');
        }
        $phpIniConfigurator = $this->beanLoader->getBean('cfhelper.phpIniConfigurator');
        $phpIniConfigurator->loadIniConfig();
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new CfHelper();
        }
        return self::$_instance;
    }

    public function getServiceManager()
    {
        return $this->beanLoader->getBean('cfhelper.serviceManager');
    }

    public function getApplicationInfo()
    {
        return $this->beanLoader->getBean('cfhelper.applicationInfo');
    }

    public function simulateCloudFoundry($manifestYml = "manifest.yml")
    {
        CloudFoundrySimulator::simulate($manifestYml);
    }
}