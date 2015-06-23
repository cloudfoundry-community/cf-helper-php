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


use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;

/**
 * Class CloudFoundryHandler
 * @package CfCommunity\CfHelper\Logger
 */
class CloudFoundryHandler extends ErrorLogHandler implements HandlerInterface
{
    public function  __construct($messageType = self::OPERATING_SYSTEM, $level = Logger::DEBUG, $bubble = true, $expandNewlines = false)
    {
        parent::__construct($messageType, $level, $bubble, $expandNewlines);
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