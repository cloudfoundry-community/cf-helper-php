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

namespace CfCommunity\CfHelper\Services;

use CfCommunity\CfHelper\Application\ApplicationInfo;

/**
 * Class PopulatorCloudFoundry
 * @package CfCommunity\CfHelper\Services
 */
class PopulatorCloudFoundry implements Populator
{
    private const VCAP_APPLICATION = 'VCAP_APPLICATION';
    private const VCAP_SERVICES = 'VCAP_SERVICES';
    /**
     * @var array
     */
    private $vcapServices;
    /**
     * @var Service[]
     */
    private $services = array();

    /**
     * @var bool
     */
    private $fullyLoaded = false;

    /**
     * @var string
     */
    private $servicesJson;

    /**
     * @var string
     */
    private $appJson;

    /**
     * PopulatorCloudFoundry constructor.
     * @param $servicesJson
     * @param $appJson
     */
    public function __construct($servicesJson = null, $appJson = null)
    {
        if (empty($servicesJson)) {
            $servicesJson = getenv(self::VCAP_SERVICES);
        }
        if (empty($appJson)) {
            $appJson = getenv(self::VCAP_APPLICATION);
        }
        $this->servicesJson = $servicesJson;
        $this->appJson = $appJson;
    }

    /**
     * @param $name
     * @return null|Service
     */
    public function getService($name)
    {
        if (empty($this->vcapServices)) {
            $this->vcapServices = array();
        }
        if (!empty($this->services[$name])) {
            return $this->services[$name];
        }
        $service = $this->getServiceFirst($name);
        if ($service !== null) {
            return $service;
        }
        $service = $this->getServiceInside($name);
        if ($service !== null) {
            return $service;
        }
        return null;
    }

    /**
     * @param $name
     * @return null|Service
     */
    private function getServiceFirst($name)
    {
        foreach ($this->vcapServices as $serviceName => $service) {
            if (preg_match('#^' . $name . '$#i', $serviceName)) {
                return $this->makeService($service[0]);
            }
        }
        return null;
    }

    /**
     * @param $service
     * @return Service
     */
    private function makeService($service)
    {
        $serviceObject = new Service($service['name'], $service['credentials'], $service['label'], $service['tags']);
        unset($service['name']);
        unset($service['credentials']);
        unset($service['label']);
        unset($service['tags']);
        $serviceObject->addDatas($service);
        $this->services[$serviceObject->getName()] = $serviceObject;
        return $serviceObject;
    }

    /**
     * @param $name
     * @return null|Service
     */
    private function getServiceInside($name)
    {
        foreach ($this->vcapServices as $serviceFirstName => $services) {
            foreach ($services as $service) {
                if (empty($service['name'])) {
                    continue;
                }
                if (preg_match('#^' . $name . '$#i', $service['name'])) {
                    return $this->makeService($service);
                }
            }
        }
        return null;
    }

    /**
     * @param $tags
     * @return Service[]
     */
    public function getServicesByTags($tags)
    {
        if (!is_array($tags)) {
            $tags = array($tags);
        }
        $services = array();
        $this->getAllServices();
        foreach ($this->services as $service) {
            if ($service->haveOneOfTags($tags)) {
                $services[] = $service;
            }
        }
        return $services;
    }

    /**
     * @return Service[]
     */
    public function getAllServices()
    {
        if ($this->fullyLoaded) {
            return $this->services;
        }
        foreach ($this->vcapServices as $serviceFirstName => $services) {
            foreach ($services as $service) {
                if (empty($service['name'])) {
                    continue;
                }
                $this->makeService($service);
            }
        }
        $this->fullyLoaded = true;
        return $this->services;
    }


    /**
     * @return ApplicationInfo
     */
    public function getApplicationInfo()
    {
        $vcapApplication = json_decode($this->appJson, true);
        if (empty($vcapApplication)) {
            return null;
        }
        $applicationInfo = new ApplicationInfo();
        foreach ($vcapApplication as $key => $value) {
            if (is_array($value)) {
                $applicationInfo->$key = (object)$value;
            } else {
                $applicationInfo->$key = $value;
            }
        }
        return $applicationInfo;
    }

    /**
     *
     */
    public function load()
    {
        if (!isset($this->servicesJson) || $this->vcapServices !== null) {
            return;
        }
        $this->vcapServices = json_decode($this->servicesJson, true);
    }
}
