<?php
/**
 * Copyright (C) 2014 Arthur Halet
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 19/08/2014
 */

namespace CfCommunity\CfHelper\Simulator;
/**
 * Class CloudFoundrySimulator
 * @package CfCommunity\CfHelper\Simulator
 */
class CloudFoundrySimulator
{
    /**
     *
     */
    const VCAP_SERVICE = 'VCAP_SERVICES';
    /**
     *
     */
    const VCAP_APPLICATION = 'VCAP_APPLICATION';
    /**
     *
     */
    const ENV = 'ENV';

    /**
     * @param $servicesJson
     */
    public static function simulate($servicesJson)
    {
        CloudFoundrySimulator::loadEnv($servicesJson);
        CloudFoundrySimulator::loadApplication($servicesJson);
        CloudFoundrySimulator::loadService($servicesJson);
    }

    /**
     * @param $servicesJson
     */
    public static function loadEnv($servicesJson)
    {
        if (!is_file($servicesJson)) {
            return;
        }
        $fileContent = file_get_contents($servicesJson);
        $json = json_decode($fileContent, true);
        if (!isset($json[self::ENV])) {
            return;
        }
        CloudFoundrySimulator::loadVarEnv($json[self::ENV]);
    }

    /**
     * @param $servicesJson
     */
    public static function loadApplication($servicesJson)
    {
        if (!is_file($servicesJson)) {
            return;
        }
        $fileContent = file_get_contents($servicesJson);
        $json = json_decode($fileContent, true);

        if (!isset($json[self::VCAP_APPLICATION])) {
            return;
        }

        CloudFoundrySimulator::loadVarEnv(array(self::VCAP_APPLICATION => json_encode($json[self::VCAP_APPLICATION])));
    }

    /**
     * @param array $envVars
     */
    public static function loadVarEnv(array $envVars)
    {
        foreach ($envVars as $key => $value) {
            CloudFoundrySimulator::setVar($key, $value);
        }
    }

    /**
     * @param string $name
     * @param string $value
     */
    public static function setVar($name, $value)
    {
        // Apache environment variable exists, overwrite it
        if (function_exists('apache_getenv') && function_exists('apache_setenv') && apache_getenv($name)) {
            apache_setenv($name, $value);
        }
        putenv("$name=$value");
    }

    /**
     * @param $servicesJson
     */
    public static function loadService($servicesJson)
    {
        if (!is_file($servicesJson)) {
            return;
        }
        $fileContent = file_get_contents($servicesJson);
        $json = json_decode($fileContent, true);

        if (!isset($json[self::VCAP_SERVICE])) {
            return;
        }
        CloudFoundrySimulator::loadVarEnv(array(self::VCAP_SERVICE => json_encode($json[self::VCAP_SERVICE])));
    }
}
