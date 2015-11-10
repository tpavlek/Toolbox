<?php

namespace Depotwarehouse\Toolbox\Tests\Validation;

use Depotwarehouse\Toolbox\DataManagement\Validation\ValidationException;
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

    /**
     * @test
     */
    public function it_can_construct_from_a_validator()
    {
        $mock_validator = m::mock(Validator::class);
        $errorBag = new MessageBag([
            'errors' => 'Some validation error occurred'
        ]);
        $input_data = [ 'id' => "1234", 'name' => 'Fred' ];
        $mock_validator->shouldReceive('errors')
            ->andReturn($errorBag);
        $mock_validator->shouldReceive('getData')
            ->andReturn($input_data);

        $exception = ValidationException::fromValidator($mock_validator);

        $this->assertEquals($errorBag, $exception->get());
        $this->assertEquals($input_data, $exception->inputData());
    }

    /**
     * @test
     */
    public function it_can_construct_from_a_messagebag()
    {
        $errorBag = new MessageBag([ 'errors' => 'some validation error occurred' ]);

        $exception = ValidationException::fromMessageBag($errorBag);

        $this->assertEquals($errorBag, $exception->errors);
    }


}
