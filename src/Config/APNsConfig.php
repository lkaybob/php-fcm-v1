<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 31/03/2018
 * Time: 00:12
 */

namespace phpFCMv1\Config;

class APNsConfig implements CommonConfig {
    const PRIORITY_HIGH = 10, PRIORITY_NORMAL = 5;
    private $payload;

    public function __construct() {
        $this -> payload = array();
    }

    /**
     * @param $key
     * @return mixed
     */
    function setCollapseKey($key) {
        $payload = array_merge($this -> payload, array('apns-collapse-id' => $key));
        $this -> payload = $payload;

        return null;
    }

    /**
     * @param $priority
     * @return mixed
     */
    function setPriority($priority) {
        $payload = array_merge($this -> payload, array('apns-priority' => $priority));
        $this -> payload = $payload;

        return null;
    }

    /**
     * @param $time : Time for notification to live in days
     * @return mixed    : Expiration option using UNIX epoch date
     * @throws \Exception
     */
    function setTimeToLive($time) {
        $expiration = new \DateTime('now');
        $expiration -> add(new \DateInterval('P' . $time . 'D'));
        $expValue = $expiration -> format('U');

        $payload = array_merge($this -> payload, array('apns-expiration' => $expValue));
        $this -> payload = $payload;

        return null;
    }

    /**
     * @return mixed
     */
    public function getPayload() {
        if (!sizeof($this -> payload)) {
            return null;
        } else {
            $payload = array('apns' => $this -> payload);
            return $payload;
        }
    }
}