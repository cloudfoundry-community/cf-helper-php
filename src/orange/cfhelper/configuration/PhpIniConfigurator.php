<?php
/**
 * Created by IntelliJ IDEA.
 * User: xpbp8114
 * Date: 25/06/2014
 * Time: 12:09
 */

namespace orange\cfhelper\configuration;


use orange\cfhelper\application\ApplicationInfo;
use orange\cfhelper\services\ServiceManager;

class PhpIniConfigurator
{
    public static $servicePhpIniName = 'php-ini';
    private $serviceManager;
    private $applicationInfo;

    function __construct()
    {

    }

    public function loadIniConfig()
    {
        try {
            $servicePhpIni = null;
            $servicePhpIni = $this->serviceManager->getService(PhpIniConfigurator::$servicePhpIniName . '-' . $this->applicationInfo->getName());
        } catch (\Exception $e) {
            return;
        }
        foreach ($servicePhpIni->getValues() as $key => $value) {
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