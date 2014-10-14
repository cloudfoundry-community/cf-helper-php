<?php
/**
 * Copyright (C) 2014 Orange
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Arthur Halet
 * Date: 19/08/2014
 */
namespace orange\cfhelper\simulator;
/**
 * Class CloudFoundrySimulator
 * @package orange\cfhelper\simulator
 */
class CloudFoundrySimulator
{

    /**
     * @param $manifestYml
     */
    public static function simulate($manifestYml)
    {
        CloudFoundrySimulator::loadEnv($manifestYml);
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
        $applications = $manifestUnparse['applications'];
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
} 