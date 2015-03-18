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

use Arthurh\Sphring\Runner\SphringRunner;
use Arthurh\Sphring\Sphring;
use orange\cfhelper\application\ApplicationInfo;
use orange\cfhelper\configuration\PhpIniConfigurator;
use orange\cfhelper\services\ServiceManager;
use orange\cfhelper\simulator\CloudFoundrySimulator;
use Symfony\Component\Yaml\Yaml;

/**
 * Class CfHelper
 * @package orange\cfhelper
 */
class CfHelper extends SphringRunner
{
    const DETECT_CLOUDFOUNDRY = 'VCAP_APPLICATION';


    public function __construct()
    {
        parent::__construct();
        $this->getSphring()->getRootProject(__DIR__);
    }

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


}