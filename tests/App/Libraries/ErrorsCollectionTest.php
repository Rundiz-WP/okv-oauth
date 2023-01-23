<?php
/**
 * @package rundiz-oauth
 */


namespace RundizOauth\Tests\App\Libraries;


class ErrorsCollectionTest extends \WP_UnitTestCase
{


    public function testGetErrorMessage()
    {
        // test that it is always return string.
        $result = \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage(['boo' => 'The argument must be string but never mind, it must return string anyway.']);
        $this->assertIsString($result);
        $this->assertEmpty($result);
        $this->assertTrue($result === '');

        $result = \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('donotmanuallychangeemail');
        $this->assertIsString($result);

        $result = \RundizOauth\App\Libraries\ErrorsCollection::getErrorMessage('thiserrorcodeneverexistsinthecode_' . uniqid());
        $this->assertIsString($result);
        $this->assertEmpty($result);
        $this->assertTrue($result === '');
    }


}
