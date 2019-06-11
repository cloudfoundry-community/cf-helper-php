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

namespace CfCommunity\CfHelper\Exception;


class ConnectorNotFoundException extends \Exception
{
    public function __construct($name)
    {
        parent::__construct("Connector with name " . $name . " doesn't exists.");
    }
}