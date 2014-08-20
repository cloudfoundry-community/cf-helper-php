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
class CloudFoundrySimulator
{

    public static function simulate($manifestYml)
    {
        CloudFoundrySimulator::loadEnv($manifestYml);
    }

    public static function loadEnv($manifestYml)
    {
        $manifestUnparse = \Symfony\Component\Yaml\Yaml::parse($manifestYml);
        $applications = $manifestUnparse['applications'];
        foreach ($applications as $application) {
            CloudFoundrySimulator::loadVarEnv($application['env']);
        }
    }

    public static function loadVarEnv(array $envVars)
    {
        foreach ($envVars as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
} 