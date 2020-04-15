<?php
/**
 * Copyright (C) 2018 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 01/03/2018
 */

namespace CfCommunity\CfHelper\Connectors;

use CfCommunity\CfHelper\Services\ServiceManager;

interface Connector
{
    /**
     *
     */
    public function load();

    /**
     * @return mixed
     */
    public function getCredentials();

    /**
     * @return mixed
     */
    public function getConnection();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager);
}
