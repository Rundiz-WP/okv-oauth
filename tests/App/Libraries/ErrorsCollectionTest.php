<?php
/**
 * @package okv-oauth
 */


namespace OKVOauth\Tests\App\Libraries;


use PHPUnit\Framework\TestCase;
use Yoast\PHPUnitPolyfills\Polyfills\AssertIsType;


class ErrorsCollectionTest extends  TestCase
{


    use AssertIsType;


    public function testGetErrorMessage()
    {
        // test that it is always return string.
        $result = \OKVOauth\App\Libraries\ErrorsCollection::getErrorMessage(['boo' => 'The argument must be string but never mind, it must return string anyway.']);
        $this->assertIsString($result);
        $this->assertEmpty($result);
        $this->assertTrue($result === '');

        $result = \OKVOauth\App\Libraries\ErrorsCollection::getErrorMessage('donotmanuallychangeemail');
        $this->assertIsString($result);

        $result = \OKVOauth\App\Libraries\ErrorsCollection::getErrorMessage('thiserrorcodeneverexistsinthecode_' . uniqid());
        $this->assertIsString($result);
        $this->assertEmpty($result);
        $this->assertTrue($result === '');
    }


}
