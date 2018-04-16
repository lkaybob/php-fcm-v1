<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 05/04/2018
 * Time: 21:05
 */

namespace phpFCMv1\tests;

require_once __DIR__ . '/../vendor/autoload.php';

use phpFCMv1\Config\APNsConfig;
use PHPUnit\Framework\TestCase;

class APNsConfigTest extends TestCase {
    public function testSetCollapseKey() {
        $instance = new APNsConfig();
        $instance -> setCollapseKey('Test');
        $payload = $instance -> getPayload();

        $this -> assertArrayHasKey('apns', $payload);
        $this -> assertArrayHasKey('apns-collapse-id', $payload['apns']['headers']);
    }

    public function testSetPriority() {
        $instance = new APNsConfig();
        $instance -> setPriority(APNsConfig::PRIORITY_HIGH);
        $payload = $instance -> getPayload();

        $this -> assertArrayHasKey('apns', $payload);
        $this -> assertArrayHasKey('apns-priority', $payload['apns']['headers']);
    }

    public function testSetTimeToLive() {
        $start = new \DateTime('now');

        $instance = new APNsConfig();
        try {
            $instance -> setTimeToLive(1);
        } catch (\Exception $e) {
        }
        $payload = $instance -> getPayload();

        $this -> assertArrayHasKey('apns', $payload);
        $this -> assertArrayHasKey('apns-expiration', $payload['apns']['headers']);

        $end = new \DateTime('@'.$payload['apns']['headers']['apns-expiration']);
        $this -> assertEquals(1, $end -> diff($start) -> d);
    }

    public function testPriorityFire() {
        // $this -> markTestSkipped('Preventing too many notifications');
        $config = new APNsConfig();
        $config -> setPriority(APNsConfig::PRIORITY_HIGH);
        $result = $this -> fireWithConfig($config);

        $this -> assertTrue($result);
    }

    public function testCollapseKeyFire() {
        // $this -> markTestSkipped('Preventing too many notifications');
        $config = new APNsConfig();
        $config -> setCollapseKey('test');
        $firstResult = $this -> fireWithConfig($config);
        sleep(5);
        $secondResult = $this -> fireWithConfig($config);

        $this -> assertEquals($firstResult, $secondResult);
        $this -> assertTrue($firstResult);
    }

    public function testTimeToLiveFire() {
        // $this -> markTestSkipped('Preventing too many notifications');
        $config = new APNsConfig();
        try {
            $config -> setTimeToLive(1);
        } catch (\Exception $e) {
        }
        $result = $this -> fireWithConfig($config);

        $this -> assertTrue($result);
    }

    /**
     * @param $config
     * @return bool
     */
    protected function fireWithConfig($config): bool {
        $fcmTest = new FCMTest();
        $client = $fcmTest -> buildNotification(FCMTest::TEST_TITLE, FCMTest::TEST_BODY, $config);
        $result = $client -> fire();
        return $result;
    }
}
