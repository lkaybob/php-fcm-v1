<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 27/03/2018
 * Time: 14:00
 */

namespace phpFCMv1\tests;

require_once __DIR__ . '/../vendor/autoload.php';

use phpFCMv1\Config\CommonConfig;
use phpFCMv1\Data;
use phpFCMv1\Client;
use phpFCMv1\Notification;
use phpFCMv1\Recipient;
use phpFCMv1\Config;
use \PHPUnit\Framework\TestCase;

class FCMTest extends TestCase {
    const KEY_FILE = 'service_account.json';
    // const DEVICE_TOKEN = 'eJH9cNs4hc4:APA91bHDwEGN6xEAwbDRpumCRSVnHLGgWXmiwIzAAeUTGP5Fx3diz4mL0T2E5zBVCb_zOfAwwuEsPy4J2504Ct0Mn3NAWVt2MKpvwh1iSUkSMKN0sjTQArMuZpzvm0ioeXkt-QFj3Xvi';
    const DEVICE_TOKEN = 'dswH6YqIC70:APA91bFaFQM_Jw-hoQAYDwXOVN8ifuIQ_GCpT26h7mt_Q-bYc4g-7q8vQqYD5ILAPwuPbU5uk2kbQtYRyDvnnLHvG3cLMcppN41ri4rYAV-Daf4QyCj4l0anuYS-mXTq1j_yanLvhlCJ';

    const TEST_TITLE = 'Testing from Code';
    const TEST_BODY = 'Using phpFCMv1!';
    const TEST_IMAGE = 'https://fastly.picsum.photos/id/982/300/200.jpg?hmac=rd0sm-A6tmsIiavEE2p9ynoCVr9RDUCjJMjqOH7_pvA';

    public function testBuild() {
        $fcm = $this -> buildNotification(self::TEST_TITLE, self::TEST_BODY);
        $payload = $fcm -> getPayload();

        $expected = array(
            'token' => self::DEVICE_TOKEN,
            'notification' => array(
                'title' => self::TEST_TITLE,
                'body' => self::TEST_BODY
            )
        );
        $this -> assertArrayHasKey('message', $payload);
        $this -> assertEquals($expected, $payload['message']);
    }

    public function testFire() {
        // $this -> markTestSkipped(__METHOD__ . ' already passed');
        $fcm = $this -> buildNotification(self::TEST_TITLE, self::TEST_BODY);
        $result = $fcm -> fire();
        echo $result;

        $this -> assertTrue($result);
    }

    public function testBuildWithImage() {
        $fcm = $this -> buildNotificationImage(self::TEST_TITLE, self::TEST_BODY);
        $payload = $fcm -> getPayload();

        $expected = array(
            'token' => self::DEVICE_TOKEN,
            'notification' => array(
                'title' => self::TEST_TITLE,
                'body' => self::TEST_BODY
            ),
            'android' => array(
                'notification' => array(
                    'image' => self::TEST_IMAGE
                )
            )
        );
        $this -> assertArrayHasKey('message', $payload);
        $this -> assertEquals($expected, $payload['message']);
    }

    public function testFireImage() {
        
        $fcm = $this -> buildNotificationImage(self::TEST_TITLE, self::TEST_BODY);
        $result = $fcm -> fire();
        echo $result;

        $this -> assertTrue($result);
    }

    public function testFireWithIncorrectPayload() {
        // $this -> markTestSkipped(__METHOD__ . ' already passed');
        $fcm = $this -> buildNotification(self::TEST_TITLE, self::TEST_BODY);

        $payload = $fcm -> getPayload();
        $payload['message']['dummy'] = 'dummy';
        $fcm -> setPayload($payload['message']);

        $result = $fcm -> fire();
        echo var_dump($result);
        $this -> assertEquals('string', gettype($result));
    }

    /**
     * @param $TEST_TITLE
     * @param $TEST_BODY
     * @param CommonConfig|null $config
     * @return Client
     */
    public function buildNotification($TEST_TITLE, $TEST_BODY, CommonConfig $config = null) {
        $recipient = new Recipient();
        $recipient -> setSingleRecipient(self::DEVICE_TOKEN);

        $notification = new Notification();
        $notification -> setNotification($TEST_TITLE, $TEST_BODY);

        $fcm = new Client(self::KEY_FILE);
        $fcm -> setValidateOnly(true);
        $fcm -> build($recipient, $notification, null, $config);

        return $fcm;
    }

    /**
     * @param $TEST_TITLE
     * @param $TEST_BODY
     * @param CommonConfig|null $config
     * @return Client
     */
    public function buildNotificationImage($TEST_TITLE, $TEST_BODY, CommonConfig $config = null) {
        $recipient = new Recipient();
        $recipient -> setSingleRecipient(self::DEVICE_TOKEN);

        $notification = new Notification();
        $notification -> setNotification($TEST_TITLE, $TEST_BODY);

        $config = new Config();
        $config -> setImage(self::TEST_IMAGE);

        $fcm = new Client(self::KEY_FILE);
        $fcm -> setValidateOnly(true);
        $fcm -> build($recipient, $notification, null, $config);

        return $fcm;
    }

    /**
     * @param $config
     * @return bool
     */
    public function fireWithConfig($config) {
        $client = $this -> buildNotification(FCMTest::TEST_TITLE, FCMTest::TEST_BODY, $config);
        $result = $client -> fire();
        return $result;
    }
}
