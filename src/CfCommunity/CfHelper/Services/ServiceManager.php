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

/**
 * Class ServiceManager
 * @package CfCommunity\CfHelper\Services
 */
class ServiceManager
{
    /**
     * @var Populator
     */
    private $populator;

    /**
     * ServiceManager constructor.
     * @param Populator $populator
     */
    public function __construct($populator = null)
    {
        if (empty($populator)) {
            $populator = new PopulatorCloudFoundry();
        }
        $this->setPopulator($populator);
    }

    /**
     * @return Populator
     */
    public function getPopulator()
    {
        return $this->populator;
    }

    /**
     * @param Populator $populator
     */
    public function setPopulator(Populator $populator)
    {
        $this->populator = $populator;
        $this->populator->load();
    }

    /**
     * @param $name
     * @return null|Service
     */
    public function getService($name)
    {
        return $this->populator->getService($name);
    }

    /**
     * @param $tags
     * @return Service[]
     */
    public function getServicesByTags($tags)
    {
        return $this->populator->getServicesByTags($tags);
    }

    /**
     * @param $tags
     * @return null|Service
     */
    public function getServiceByTags($tags)
    {
        $services = $this->populator->getServicesByTags($tags);
        if (empty($services)) {
            return null;
        }
        return $services[0];
    }

    /**
     * @return Service[]
     */
    public function getAllServices()
    {
        return $this->populator->getAllServices();
    }
}
