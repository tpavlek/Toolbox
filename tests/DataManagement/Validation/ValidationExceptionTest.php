<?php

namespace Depotwarehouse\Toolbox\Tests\Validation;

use Illuminate\Validation\Validator;
use Illuminate\Support\MessageBag;
use Mockery as m;
use PHPUnit_Framework_TestCase;

class ValidationExceptionTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        m::close();
    }

    public function testConstructor()
    {
        $mock_validator = m::mock(Validator::class);
        $mock_messagebag = m::mock(MessageBag::class);

        $mock_validator->shouldReceive('errors')
            ->andReturn($mock_messagebag);

        $exception = new \Depotwarehouse\Toolbox\DataManagement\Validation\ValidationException($mock_validator);
        $this->assertAttributeEquals($mock_messagebag, "errors", $exception);

        $exception = new \Depotwarehouse\Toolbox\DataManagement\Validation\ValidationException($mock_messagebag);
        $this->assertAttributeEquals($mock_messagebag, "errors", $exception);
    }

    public function testGet()
    {
        $mock_messagebag = m::mock(MessageBag::class);
        $exception = new \Depotwarehouse\Toolbox\DataManagement\Validation\ValidationException($mock_messagebag);

        $this->assertEquals($mock_messagebag, $exception->get());
    }

}
