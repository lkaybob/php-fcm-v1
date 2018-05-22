<?php
/**
 * Created by PhpStorm.
 * User: lkaybob
 * Date: 21/03/2018
 * Time: 09:51
 */

namespace phpFCMv1\tests;

require_once __DIR__ . '/../vendor/autoload.php';

use phpFCMv1\Recipient;
use PHPUnit\Framework\TestCase;

class RecipientTest extends TestCase {
    function testSingleRecipient() {
        $TEST_TOKEN = 'TOKEN';

        $instance = new Recipient();
        $instance -> setSingleRecipient($TEST_TOKEN);

        $equal = array('token' => $TEST_TOKEN);
        $this -> assertEquals($equal, $instance -> getPayload());
    }

    function testTopicRecipient() {
        $TEST_TOPIC = 'TOPIC';

        $instance = new Recipient();
        $instance -> setTopicRecipient($TEST_TOPIC);

        $equal = array('topic' => $TEST_TOPIC);
        $this -> assertEquals($equal, $instance -> getPayload());
    }

    function testConditionalSimple() {
        $TEST_CONDITION = "'dogs' in topics";

        $instance = new Recipient();
        $instance -> setConditionalRecipient($TEST_CONDITION);

        $equal = array('condition' => $TEST_CONDITION);
        $this -> assertEquals($equal, $instance -> getPayload());
    }

    /**
     * Test complex conditionals with && and || operator
     * Should expect \InvalidArgumentException when operators are used incorrectly
     * e.g. "'dogs' in topics && 'cats' in topics" => Pass
     *      "'dogs' in topics & 'cats' in topics"  => Fail
     *
     * TODO: 정규식, explode를 이용해서 오류 탐지할 것. 사용되는 키워드들 확인 필요
     */
    function testConditionalComplex() {
        $this -> markTestSkipped(__METHOD__ . " will be implemented Later");

        $TEST_COMPLEX_CONDITION = "'dogs' in topic & 'cats' in topic";

        $this -> expectException(\InvalidArgumentException::class);

        $instance = new Recipient();
        $instance -> setConditionalRecipient($TEST_COMPLEX_CONDITION);
    }

    function testSingleRecipientWithoutToken() {
        $this -> expectException(\InvalidArgumentException::class);

        $instance = new Recipient();
        $instance -> setSingleRecipient(null);
    }

    function testTopicRecipientWithoutTopic() {
        $this -> expectException(\InvalidArgumentException::class);

        $instance = new Recipient();
        $instance -> setTopicRecipient(null);
    }

    function testConditionalWithoutTopic() {
        $this -> expectException(\InvalidArgumentException::class);

        $instance = new Recipient();
        $instance -> setConditionalRecipient(null);
    }


    function testDuplicateRecipients() {
        $this -> expectException(\BadMethodCallException::class);
        $instance = new Recipient();

        $instance -> setSingleRecipient('ANY_TOKEN');
        $instance -> setSingleRecipient("ANOTHER_TOKEN");
    }
}
