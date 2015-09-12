<?php

class OrganizationListTest extends \BaseTestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $mock;

    public function setUp()
    {
        $this->testClass = 'CKAN\OrganizationList';
        parent::setUp();
        $this->mock = $this->getMockBuilder($this->testClass)
            ->disableOriginalConstructor()
            ->getMock();
    }

//    public function testConstructor()
//    {
        // set expectations for constructor calls
//        $this->mock->expects($this->once())
//            ->method('get')
//            ->with(
//                $this->equalTo('someUrl')
//            );

//        $this->mock->method('get')
//            ->willReturn('testor');
//
//        $this->assertEquals('testor', $this->mock->get('777'));

//        $get = $this->reflection->getMethod('get');

//        $this->setExpectedException('Exception', 'could not get organization list from json someUrl');

//        $constructor = $this->reflection->getConstructor();
//        $this->setExpectedException('Exception');
//        $constructor->invoke($this->mock, 'someUrl');
//    }

    public function testGetTreeArrayFor()
    {
        $this->assertTrue($this->reflection->hasMethod('getTreeArrayFor'));
    }
}
