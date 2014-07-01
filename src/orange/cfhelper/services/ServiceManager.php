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

namespace orange\cfhelper\services;


class ServiceManager
{
    private $populator;

    public function __construct()
    {
        $this->populator = new PopulatorCloudFoundry();
    }

    /**
     * @return mixed
     */
    public function getPopulator()
    {
        return $this->populator;
    }

    /**
     * @Required
     * @param mixed $populator
     */
    public function setPopulator(Populator $populator)
    {
        $this->populator = $populator;
    }


    public function getService($name)
    {
        return $this->populator->getService($name);
    }
}