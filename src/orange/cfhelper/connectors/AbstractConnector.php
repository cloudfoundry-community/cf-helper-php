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


namespace orange\cfhelper\connectors;


use orange\cfhelper\services\Service;
use orange\cfhelper\services\ServiceManager;

abstract class AbstractConnector
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    /**
     * @var array
     */
    protected $credentials;

    /**
     * @return mixed
     */
    abstract public function load();

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * @param ServiceManager $serviceManager
     * @Required
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function parseFromService(Service $service)
    {
        $url = $service->getValue('(uri|url)');
        if (!empty($url)) {
            return $this->parseUrl($url);
        }
        $host = $service->getValue('.*host.*');
        $port = $service->getValue('.*port.*');
        $user = $service->getValue('.*(user|login).*');
        $password = $service->getValue('.*pass.*');
        $toReturn['user'] = $user;
        $toReturn['pass'] = $password;
        $toReturn['host'] = $host;
        $toReturn['port'] = $port;
        return $toReturn;
    }

    protected function parseUrl($url)
    {
        $toReturn = [];
        $parsedUrl = parse_url($url);
        $toReturn['host'] = $parsedUrl['host'];
        $toReturn['port'] = $parsedUrl['port'];
        $toReturn['user'] = $parsedUrl['user'];
        $toReturn['pass'] = $parsedUrl['pass'];
        $toReturn['path'] = $parsedUrl['path'];
        return $toReturn;
    }

    abstract public function getConnection();

    /**
     * @return array
     */
    public function getCredentials()
    {
        return $this->credentials;
    }


}