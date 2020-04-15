<?php
/**
 * Copyright (C) 2020 Gavin Hanover
 *
 * This software is distributed under the terms and conditions of the 'MIT'
 * license which can be found in the file 'LICENSE' in this package distribution
 * or at 'http://opensource.org/licenses/MIT'.
 *
 * Author: Gavin Hanover
 * Date: 2020-04-15
 */

namespace Test\CfCommunity\CfHelper\Services;

use CfCommunity\CfHelper\CfHelper;
use CfCommunity\CfHelper\Simulator\CloudFoundrySimulator;
use PHPUnit\Framework\TestCase;

class CloudFoundrySimulatorTest extends TestCase
{
    public function testSimulate()
    {
        $jsonFile = dirname(dirname(dirname(__DIR__))).'/data/vcap.json';
        $expected = json_decode(file_get_contents($jsonFile), true);

        CloudFoundrySimulator::simulate(dirname(dirname(dirname(__DIR__))).'/data/vcap.json');
        $cfHelper = new CfHelper();

        self::assertEquals($expected['application']['name'], $cfHelper->getApplicationInfo()->getName());
    }
}
