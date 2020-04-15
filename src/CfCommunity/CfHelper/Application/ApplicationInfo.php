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
     * @var string
     */
    public $space_name;
    /**
     * @var string
     */
    public $space_id;
    /**
     * @var string
     */
    public $organization_name;
    /**
     * @var string
     */
    public $organization_id;
    /**
     * @var string
     */
    public $application_id;
    /**
     * @var object
     */
    public $limits;

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

    /**
     * @return string
     */
    public function getSpaceName()
    {
        return $this->space_name;
    }

    /**
     * @param string $space_name
     */
    public function setSpaceName(string $space_name)
    {
        $this->space_name = $space_name;
    }

    /**
     * @return string
     */
    public function getSpaceId()
    {
        return $this->space_id;
    }

    /**
     * @param string $space_id
     */
    public function setSpaceId(string $space_id)
    {
        $this->space_id = $space_id;
    }

    /**
     * @return string
     */
    public function getOrganizationName()
    {
        return $this->organization_name;
    }

    /**
     * @param string $organization_name
     */
    public function setOrganizationName(string $organization_name)
    {
        $this->organization_name = $organization_name;
    }

    /**
     * @return string
     */
    public function getOrganizationId()
    {
        return $this->organization_id;
    }

    /**
     * @param string $organization_id
     */
    public function setOrganizationId(string $organization_id)
    {
        $this->organization_id = $organization_id;
    }

    /**
     * @return string
     */
    public function getApplicationId()
    {
        return $this->application_id;
    }

    /**
     * @param string $application_id
     */
    public function setApplicationId(string $application_id)
    {
        $this->application_id = $application_id;
    }

    /**
     * @return object
     */
    public function getLimits()
    {
        return $this->limits;
    }

    /**
     * @param object $limits
     */
    public function setLimits(object $limits)
    {
        $this->limits = $limits;
    }
}
