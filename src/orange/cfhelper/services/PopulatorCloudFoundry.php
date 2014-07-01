<?php
namespace orange\services;

use orange\cfhelper\application\ApplicationInfo;

class PopulatorCloudFoundry extends Populator
{
    private $vcapServices;
    private $services = array();
    private $applicationInfo;

    function __construct()
    {
        parent::__construct();
        $this->vcapServices = json_decode($_ENV['VCAP_SERVICES'], true);
        if (empty($this->vcapServices)) {
            $this->vcapServices = array();
        }
    }

    public function getService($name)
    {
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
        throw new \Exception("Service $name cannot be found.");
    }

    private function getServiceFirst($name)
    {
        foreach ($this->vcapServices as $serviceName => $service) {
            if ($serviceName == $name) {
                return $this->makeService($service[0]);
            }
        }
        return null;
    }

    private function makeService($service)
    {
        $serviceObject = new Service($service['name'], $service['credentials'], $service['label']);
        unset($service['name']);
        unset($service['credentials']);
        unset($service['label']);
        $serviceObject->addDatas($service);
        $this->services[$serviceObject->getName()] = $serviceObject;
        return $serviceObject;
    }

    private function getServiceInside($name)
    {
        foreach ($this->vcapServices as $serviceFirstName => $services) {
            foreach ($services as $service) {
                if ($service['name'] == $name) {
                    return $this->makeService($service);
                }
            }
        }
        return null;
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
     *
     */
    public function setApplicationInfo(ApplicationInfo $applicationInfo)
    {
        $this->applicationInfo = $applicationInfo;
        $this->populateApplicationInfo();
    }

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

}