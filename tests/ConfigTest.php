<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 21/04/2018
 * Time: 16:31
 */

namespace phpFCMv1\tests;

use phpFCMv1\Config;
use \PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase {

    public function testSetCollapseKey() {
        // $this -> markTestSkipped("Skipping passed test");
        $fcmTest = new FCMTest();
        $config = new Config();

        $config -> setCollapseKey('test');
        $result = $fcmTest -> fireWithConfig($config);

        $this -> assertTrue($result);
    }

    public function testSetTimeToLive() {
        // $this -> markTestSkipped("Implemented");
        $fcmTest = new FCMTest();
        $config = new Config();

        $config -> setTimeToLive(10);
        $result = $fcmTest -> fireWithConfig($config);

        $this -> assertTrue($result);
    }


    public function testSetPriority() {
        // $this -> markTestSkipped("Not Implemented");
        $fcmTest = new FCMTest();
        $config = new Config();

        $config -> setPriority(Config::PRIORITY_HIGH);
        $result = $fcmTest -> fireWithConfig($config);

        $this -> assertTrue($result);
    }
}
