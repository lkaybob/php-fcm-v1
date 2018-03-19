<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 19/03/2018
 * Time: 22:42
 */

namespace phpFCMv1\tests;

use phpFCMv1\Credentials;
use PHPUnit\Framework\TestCase;

class CredentialTest extends TestCase {
    public function testWithoutKeyFile() : void {
        $this -> expectException(\InvalidArgumentException::class);
        new Credentials(null);
    }

    public function testWithKeyFile() : void {
        $instance = new Credentials('../service_account.json');
        $this -> assertEquals('../service_account.json', $instance -> getKeyFilePath());
    }

    public function testOpenKeyFile() {
        $body = file_get_contents('../service_account.json');
        $encodedBody = json_decode($body, true);
        $this -> assertNotNull($encodedBody['private_key']);
    }

    public function testAcquireAccessToken() : void {
        $instance = new Credentials('../service_account.json');
        try {
            $accessToken = $instance -> getAccessToken();
        }
        catch (\RuntimeException $exception) {
            echo $exception -> getMessage();
        }

        $this -> assertNotNull($accessToken);
    }
}
