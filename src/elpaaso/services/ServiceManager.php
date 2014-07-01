<?php
/**
 * Created by IntelliJ IDEA.
 * User: xpbp8114
 * Date: 24/06/2014
 * Time: 16:04
 */

namespace elpaaso\services;


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