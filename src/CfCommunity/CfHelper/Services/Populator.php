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
 * Class Populator
 * @package CfCommunity\CfHelper\Services
 */
abstract class  Populator
{

    /**
     *
     */
    function __construct()
    {
    }

    /**
     * @param $name
     * @return Service
     */
    public abstract function getService($name);

    /**
     *
     */
    public abstract function load();

    /**
     * @return Service[]
     */
    public abstract function getAllServices();

}