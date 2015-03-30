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
    const KEY_SIMULATE_SERVICE = 'serviceSimulate';
    /**
     *
     */
    const KEY_SERVICE = 'VCAP_SERVICES';
    /**
     *
     */
    const KEY_APPLICATION = 'applications';

    /**
     * @param $manifestYml
     */
    public static function simulate($manifestYml)
    {
        CloudFoundrySimulator::loadEnv($manifestYml);
        CloudFoundrySimulator::loadService($manifestYml);
    }

    /**
     * @param $manifestYml
     */
    public static function loadEnv($manifestYml)
    {
        if (!is_file($manifestYml)) {
            return;
        }

        $manifestUnparse = \Symfony\Component\Yaml\Yaml::parse($manifestYml);
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
            $_ENV[$key] = $value;
        }
    }

    /**
     * @param $manifestYml
     */
    public static function loadService($manifestYml)
    {
        if (!is_file($manifestYml)) {
            return;
        }
        $manifestUnparse = \Symfony\Component\Yaml\Yaml::parse($manifestYml);
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