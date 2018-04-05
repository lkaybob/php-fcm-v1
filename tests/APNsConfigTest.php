<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 05/04/2018
 * Time: 21:05
 */

namespace phpFCMv1\tests;

use phpFCMv1\Config\APNsConfig;
use PHPUnit\Framework\TestCase;

class APNsConfigTest extends TestCase {
    public function testSetCollapseKey() {
        $instance = new APNsConfig();
        $instance -> setCollapseKey('Test');
        $payload = $instance -> getPayload();

        $this -> assertArrayHasKey('apns', $payload);
        $this -> assertArrayHasKey('apns-collapse-id', $payload['apns']);
    }

    public function testSetPriority() {
        $instance = new APNsConfig();
        $instance -> setPriority(APNsConfig::PRIORITY_HIGH);
        $payload = $instance -> getPayload();

        $this -> assertArrayHasKey('apns', $payload);
        $this -> assertArrayHasKey('apns-priority', $payload['apns']);
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
        $this -> assertArrayHasKey('apns-expiration', $payload['apns']);

        $end = new \DateTime('@'.$payload['apns']['apns-expiration']);
        $this -> assertEquals(1, $end -> diff($start) -> d);
    }
}
