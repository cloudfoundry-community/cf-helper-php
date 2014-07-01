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
            var_dump($key . '=' . $value);
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