<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 20/03/2018
 * Time: 18:58
 */

namespace phpFCMv1;

use \GuzzleHttp;

class Client {
    const SEND_URL = 'https://fcm.googleapis.com/v1/projects/$0/messages:send';
    const CONTENT_TYPE = 'json';
    const HTTP_ERRORS_OPTION = 'http_errors';

    private $credentials;
    private $payload;
    private $URL;

    public function __construct($keyFile) {
        $this -> credentials = new Credentials($keyFile);
        $this -> setProjectID();
        $this -> payload = array('message' => null);
    }

    public function build(Recipient $recipient, Notification $notification = null, Data $data = null) {
        $result = $recipient();
        $isPlayload = false;

        if (!is_null($notification)) {
            $result = array_merge($result, $notification());
            $isPlayload = true;
        }
        if (!is_null($data)) {
            $result = array_merge($result, $data());
            $isPlayload = true;
        }

        if (!$isPlayload) {
            throw new \UnderflowException('Neither notification or data object has not been set');
        } else {
            $this -> setPayload($result);
        }
    }

    /**
     * Fires built message
     */
    public function fire() {
        $options = array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this -> credentials -> getAccessToken()
            )
        );
        $body = array(
            self::CONTENT_TYPE => $this -> getPayload(),
            self::HTTP_ERRORS_OPTION => false
        );
        // Class name conflict occurs, when used as "Client"
        $client = new GuzzleHttp\Client($options);
        $response = $client -> request('POST', $this -> getURL(), $body);

        if ($response -> getStatusCode() == 200) {
            // TODO: Message의 Instance도 return하면 좋지 않을까
            return true;
        } else {
            $result = json_decode($response -> getBody(), true);
            return $result['error']['message'];
        }
    }

    /**
     * @return array
     */
    public function getPayload(): array {
        return $this -> payload;
    }

    /**
     * @param array $payload
     */
    public function setPayload(array $payload): void {
        $this -> payload['message'] = $payload;
    }

    private function setProjectID() {
        $projectId = $this -> credentials -> getProjectID();
        $pattern = '/\$0/';
        $result = preg_replace($pattern, $projectId, self::SEND_URL);
        $this -> setURL($result);
    }

    private function setURL($result) {
        $this -> URL = $result;
    }

    public function getURL() {
        return $this -> URL;
    }
}