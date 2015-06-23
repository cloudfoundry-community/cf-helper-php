<?php
/**
 * Copyright (C) 2015 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 18/03/2015
 */


namespace CfCommunity\CfHelper\Logger;


use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Arthurh\Sphring\Annotations\AnnotationsSphring\Required;

/**
 * Class CloudFoundryLogger
 * @package CfCommunity\CfHelper\Logger
 */
class CloudFoundryLogger extends Logger
{
    /**
     * @var HandlerInterface
     */
    private $cloudFoundryHandler;

    public function __construct($name = 'CloudFoundry Helper', array $processors = array())
    {
        parent::__construct($name, array(), $processors);
    }

    public function loadCloudFoundryHandler()
    {
        $this->pushHandler($this->getCloudFoundryHandler());
    }

    /**
     * @return mixed
     */
    public function getCloudFoundryHandler()
    {
        return $this->cloudFoundryHandler;
    }

    /**
     * @param HandlerInterface $cloudFoundryHandler
     * @Required
     */
    public function setCloudFoundryHandler(HandlerInterface $cloudFoundryHandler)
    {
        $this->cloudFoundryHandler = $cloudFoundryHandler;
    }

    public function setLevel($level)
    {
        $this->cloudFoundryHandler->setLevel($level);
    }

}