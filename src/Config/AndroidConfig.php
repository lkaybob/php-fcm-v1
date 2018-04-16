<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 31/03/2018
 * Time: 00:04
 */

namespace phpFCMv1\Config;

class AndroidConfig implements CommonConfig {
    const PRIORITY_HIGH = 'HIGH', PRIORITY_NORMAL = 'NORMAL';
    private $payload;

    public function __construct() {
        $this -> payload = array();
    }

    /**
     * @param $key
     * @return mixed
     */
    function setCollapseKey($key) {
        $payload = array_merge($this -> payload, array('collapse_key' => $key));
        $this -> payload = $payload;

        return null;
    }

    /**
     * @param $priority
     * @return mixed
     */
    function setPriority($priority) {
        $payload = array_merge($this -> payload, array('priority' => $priority));
        $this -> payload = $payload;

        return null;
    }

    /**
     * @param $time
     * @return mixed
     */
    function setTimeToLive($time) {
        $payload = array_merge($this -> payload, array('ttl' => $time . 's'));
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
            $payload = array('android' => $this -> payload);
            return $payload;
        }
    }

    function __invoke() {
        // TODO: Implement __invoke() method.
        return $this -> getPayload();
    }
}