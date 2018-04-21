<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 05/04/2018
 * Time: 20:12
 */

namespace phpFCMv1;

use phpFCMv1\Config\AndroidConfig;
use phpFCMv1\Config\APNsConfig;
use phpFCMv1\Config\CommonConfig;

class Config implements CommonConfig {
    const PRIORITY_HIGH = 1;
    const PRIORITY_NORMAL = 2;
    private $androidConfig;
    private $apnsConfig;

    public function __construct() {
        $this -> androidConfig = new AndroidConfig();
        $this -> apnsConfig = new APNsConfig();
    }

    /**
     *
     * @param $key
     * @return mixed
     */
    function setCollapseKey($key) {
        $this -> androidConfig -> setCollapseKey($key);
        $this -> apnsConfig -> setCollapseKey($key);

        return null;
    }

    /**
     * @param $priority
     * @return mixed
     */
    function setPriority($priority) {
        switch ($priority) {
            case self::PRIORITY_HIGH:
                $this -> androidConfig -> setPriority(AndroidConfig::PRIORITY_HIGH);
                $this -> apnsConfig -> setPriority(APNsConfig::PRIORITY_HIGH);
                break;
            case self::PRIORITY_NORMAL:
                $this -> androidConfig -> setPriority(AndroidConfig::PRIORITY_NORMAL);
                $this -> apnsConfig -> setPriority(APNsConfig::PRIORITY_NORMAL);
                break;
            default:
                throw new \InvalidArgumentException("Priority option not proper");
                break;
        }

        return null;
    }

    /**
     * @param $time : seconds
     * @return mixed
     */
    function setTimeToLive($time) {
        try {
            $this -> androidConfig -> setTimeToLive($time);
            $this -> apnsConfig -> setTimeToLive($time);
        } catch (\Exception $e) {

        }

        return null;
    }

    /**
     * @return mixed
     */
    function getPayload() {
        $androidConfig = $this -> androidConfig -> getPayload();
        $apnsConfig = $this -> apnsConfig -> getPayload();

        return array_merge($androidConfig, $apnsConfig);
    }

    function __invoke() {
        return $this -> getPayload();
    }
}