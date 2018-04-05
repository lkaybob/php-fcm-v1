<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 31/03/2018
 * Time: 00:04
 */

namespace phpFCMv1\Config;


interface CommonConfig {
    /**
     *
     * @param $key
     * @return mixed
     */
    function setCollapseKey($key);

    /**
     * @param $priority
     * @return mixed
     */
    function setPriority($priority);

    /**
     * @param $seconds
     * @return mixed
     */
    function setTimeToLive($seconds);

    /**
     * @return mixed
     */
    function getPayload();
}