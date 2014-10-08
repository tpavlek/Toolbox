<?php

class ValidationExceptionTest extends PHPUnit_Framework_TestCase {

    public function testConstructor() {
        $mock_validator = Mockery::mock('\Illuminate\Validation\Validator');
        $mock_messagebag = Mockery::mock('\Illuminate\Support\MessageBag');
        $mock_validator->shouldReceive('errors')->andReturn($mock_messagebag);

        $exception = new \Depotwarehouse\Toolbox\Exceptions\ValidationException($mock_validator);
        $this->assertAttributeEquals($mock_messagebag, "errors", $exception);

        $exception = new \Depotwarehouse\Toolbox\Exceptions\ValidationException($mock_messagebag);
        $this->assertAttributeEquals($mock_messagebag, "errors", $exception);
    }

    public function testGet() {
        $mock_messagebag = Mockery::mock('\Illuminate\Support\MessageBag');
        $exception = new \Depotwarehouse\Toolbox\Exceptions\ValidationException($mock_messagebag);

        $this->assertEquals($mock_messagebag, $exception->get());
    }

}
 