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

namespace CfCommunity\CfHelper\Application;


/**
 * Class ApplicationInfo
 * @package CfCommunity\CfHelper\Application
 */
class ApplicationInfo
{
    /**
     * @var string
     */
    public $version;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string[]
     */
    public $uris;
    /**
     * @var string
     */
    public $host;
    /**
     * @var int
     */
    public $port;

    /**
     *
     */
    public function __construct()
    {

    }


    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return string[]
     */
    public function getUris()
    {
        return $this->uris;
    }

    /**
     * @param string[] $uris
     */
    public function setUris($uris)
    {
        $this->uris = $uris;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

} 