<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 20/03/2018
 * Time: 18:58
 */

namespace phpFCMv1;

use \GuzzleHttp;
use phpFCMv1\Config\CommonConfig;

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

    /**
     * @param Recipient $recipient : Recipient token or topic for the notificaation
     * @param Notification|null $notification : Notification with title & body to send.
     *                                          Not required, if only downstream data payload is needed
     * @param Data|null $data : (Optional) Downstream data payload to send
     * @param CommonConfig|null $config : (Optional) CommonConfig instance to define optional characteristics
     *                                    of notification
     */
    public function build(Recipient $recipient, Notification $notification = null, Data $data = null, CommonConfig $config = null) {
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

        if (!is_null($config)) {
            $result = array_merge($result, $config());
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
            return true;
        } else {
            $result = json_decode($response -> getBody(), true);
            return $result['error']['message'];
        }
    }

    /**
     * @return array
     */
    public function getPayload() {
        return $this -> payload;
    }

    /**
     * @param array $payload
     */
    public function setPayload(array $payload) {
        $this -> payload['message'] = $payload;
    }

    public function setValidateOnly($option) {
        if (is_bool($option))
            $this -> payload['validate_only'] = $option;
        else
            throw new \InvalidArgumentException('validate_only option only allows boolean');
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