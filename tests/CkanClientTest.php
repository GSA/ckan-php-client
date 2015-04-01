<?php
/**
 * Created by IntelliJ IDEA.
 * User: alexandr.perfilov
 * Date: 3/31/15
 * Time: 4:38 PM
 */

class CkanClientTest extends \BaseTestCase {

    public function setUp()
    {
        $this->CkanClient = $this->getMockBuilder('CkanClient')
            ->disableOriginalConstructor()
            ->getMock();
//        $this->testClass = 'CkanClient';
//        parent::setup();
    }

    public function testSetHeaders()
    {
        $this->assertTrue(true);
    }
}
