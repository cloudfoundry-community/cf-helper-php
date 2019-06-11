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
    const KEY_SIMULATE_SERVICE = 'services';
    /**
     *
     */
    const KEY_SERVICE = 'VCAP_SERVICES';
    /**
     *
     */
    const KEY_APPLICATION = 'applications';

    /**
     * @param $servicesJson
     */
    public static function simulate($servicesJson)
    {
        CloudFoundrySimulator::loadEnv($servicesJson);
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
        $manifestUnparse = json_decode($fileContent, true);;
        $applications = $manifestUnparse[self::KEY_APPLICATION];
        if (empty($applications)) {
            return;
        }

        foreach ($applications as $application) {
            if (empty($application['env'])) {
                continue;
            }
            CloudFoundrySimulator::loadVarEnv($application['env']);
        }
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
        $manifestUnparse = json_decode($fileContent, true);
        $services = $manifestUnparse[self::KEY_SIMULATE_SERVICE];
        if (empty($services)) {
            return;
        }
        foreach ($services as $serviceName => $serviceCredentials) {
            $service = array(
                "name" => $serviceName,
                "label" => "user-provided",
                "tags" => array(),
                "credentials" => $serviceCredentials
            );
            $serviceUserProvided = array(array("user-provided" => $service));
            CloudFoundrySimulator::loadVarEnv(array(self::KEY_SERVICE => json_encode($serviceUserProvided)));
        }
    }
} 
