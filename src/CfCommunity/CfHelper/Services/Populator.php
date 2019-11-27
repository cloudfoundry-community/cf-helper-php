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

use CfCommunity\CfHelper\Application\ApplicationInfo;

/**
 * Class Populator
 * @package CfCommunity\CfHelper\Services
 */
interface Populator
{

    /**
     * @param $name
     * @return null|Service
     */
    function getService($name);

    /**
     * @param $tags
     * @return null|Service[]
     */
    function getServicesByTags($tags);

    /**
     *
     */
    function load();

    /**
     * @return Service[]
     */
    function getAllServices();

    /**
     * @return ApplicationInfo
     */
    function getApplicationInfo();
}