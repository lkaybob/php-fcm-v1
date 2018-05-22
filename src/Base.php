<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 21/03/2018
 * Time: 11:24
 */

namespace phpFCMv1;

abstract class Base {
    protected $payload;

    /**
     * @param array ...$arg
     */
    protected function validateCurrent(...$arg) {
        $this -> validateArg($arg);
        $this -> checkPayloadNull();
    }

    protected function checkPayloadNull() {
        if (isset($this -> payload)) {
            throw new \BadMethodCallException("Target has already been set", 1);
        }
    }

    /**
     * @param array ...$arg
     * @throws \InvalidArgumentException when item is not defined (null)
     */
    protected function validateArg(array $arg) {
        foreach ($arg as $index => $item) {
            if (is_null($item)) {
                throw new \InvalidArgumentException("Argument is not defined: " . $index);
            }
        }
    }

    /**
     * @return array
     * @throws \UnderflowException
     */
    public function __invoke() {
        return $this -> getPayload();
    }

    /**
     * @return mixed
     */
    public function getPayload() {
        return $this -> payload;
    }

    /**
     * @param mixed $payload
     */
    public function setPayload($payload) {
        $this -> payload = $payload;
    }
}