<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 05/04/2018
 * Time: 20:29
 */

namespace phpFCMv1\tests;

require_once __DIR__ . "/../vendor/autoload.php";

use phpFCMv1\Config\AndroidConfig;
use PHPUnit\Framework\TestCase;

class AndroidConfigTest extends TestCase {

    public function testSetCollapseKey() {
        $instance = new AndroidConfig();
        $instance -> setCollapseKey('Test');
        $payload = $instance -> getPayload();

        $this -> assertArrayHasKey('android', $payload);
        $this -> assertArrayHasKey('collapse_key', $payload['android']);
    }

    public function testSetPriority() {
        $instance = new AndroidConfig();
        $instance -> setPriority(AndroidConfig::PRIORITY_HIGH);
        $payload = $instance -> getPayload();

        $this -> assertArrayHasKey('android',$payload);
        $this -> assertArrayHasKey('priority', $payload['android']);
    }

    public function testSetTimeToLive() {
        $instance = new AndroidConfig();
        $instance -> setTimeToLive(4.0);
        $payload = $instance -> getPayload();

        $this -> assertArrayHasKey('android',$payload);
        $this -> assertArrayHasKey('ttl', $payload['android']);
        $this -> assertStringEndsWith('s', $payload['android']['ttl']);
    }

    public function testNoConfig() {
        $instance = new AndroidConfig();
        $payload = $instance -> getPayload();

        $this -> assertNull($payload);
    }

    public function testDuplicateOption() {
        $instance = new AndroidConfig();
        $instance -> setTimeToLive(4.0);
        $instance -> setTimeToLive(5.0);
        $payload = $instance -> getPayload();

        $this -> assertEquals('5s', $payload['android']['ttl']);
    }

    public function testCollapseKeyFire() {
        // $this -> markTestSkipped("Skipping passed test");
        $config = new AndroidConfig();
        $fcmTest = new FCMTest();
        $config -> setCollapseKey('test');
        $firstResult = $fcmTest -> fireWithConfig($config);
        $this -> assertTrue($firstResult);
        sleep(5);

        $secondResult = $fcmTest -> fireWithConfig($config);
        $this -> assertEquals($firstResult, $secondResult);
    }

    public function testPriorityFire() {
        // $this -> markTestSkipped("Should check later");
        $config = new AndroidConfig();
        $fcmTest = new FCMTest();
        $config -> setPriority(AndroidConfig::PRIORITY_HIGH);
        $result = $fcmTest -> fireWithConfig($config);

        $this -> assertTrue($result);
    }

    public function testTimeToLive() {
        // $this -> markTestSkipped("Skipping passed test");
        $config = new AndroidConfig();
        $fcmTest = new FCMTest();
        $config -> setTimeToLive(60);
        $result = $fcmTest -> fireWithConfig($config);

        $this -> assertTrue($result);
    }
}
