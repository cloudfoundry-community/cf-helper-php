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

use Arthurh\Sphring\Annotations\AnnotationsSphring\Required;
use CfCommunity\CfHelper\Application\ApplicationInfo;

/**
 * Class PopulatorCloudFoundry
 * @package CfCommunity\CfHelper\Services
 */
class PopulatorCloudFoundry extends Populator
{
    /**
     * @var array
     */
    private $vcapServices;
    /**
     * @var Service[]
     */
    private $services = array();
    /**
     * @var ApplicationInfo
     */
    private $applicationInfo;

    /**
     * @var bool
     */
    private $fullyLoaded = false;

    /**
     *
     */
    function __construct()
    {
        parent::__construct();
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
        if (!empty($service)) {
            return $service;
        }
        $service = $this->getServiceInside($name);
        if (!empty($service)) {
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
        return $this->applicationInfo;
    }

    /**
     * @Required
     * @param ApplicationInfo $applicationInfo
     *
     */
    public function setApplicationInfo(ApplicationInfo $applicationInfo)
    {
        $this->applicationInfo = $applicationInfo;
        $this->populateApplicationInfo();
    }

    /**
     *
     */
    public function populateApplicationInfo()
    {
        $vcapApplication = json_decode($_ENV['VCAP_APPLICATION'], true);
        if (empty($vcapApplication)) {
            return;
        }
        foreach ($vcapApplication as $key => $value) {
            if (is_array($value)) {
                $this->applicationInfo->$key = (object)$value;
            } else {
                $this->applicationInfo->$key = $value;
            }
        }
    }

    /**
     *
     */
    public function load()
    {
        if (!isset($_ENV['VCAP_SERVICES']) || $this->vcapServices !== null) {
            return;
        }
        $this->vcapServices = json_decode($_ENV['VCAP_SERVICES'], true);
    }
}