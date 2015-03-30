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


use Monolog\Handler\SyslogHandler;
use Monolog\Logger;

/**
 * Class CloudFoundryHandler
 * @package CfCommunity\CfHelper\Logger
 */
class CloudFoundryHandler extends SyslogHandler
{
    public function __construct($level = Logger::DEBUG, $bubble = true, $logopts = LOG_PID)
    {
        parent::__construct('Cloud Foundry', LOG_USER, $level, $bubble, LOG_PID | LOG_PERROR);
    }

    public function setLevel($level)
    {
        if (!is_string($level)) {
            parent::setLevel((int)$level);
            return;
        }
        if (!defined('\Monolog\Logger::' . $level)) {
            return;
        }
        parent::setLevel(constant('\Monolog\Logger::' . $level));
    }
}