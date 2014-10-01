<?php

class VerificationTest extends PHPUnit_Framework_TestCase {

    public function testRequireSetWithEmptyArrayAndRequirements() {
        $array = [];
        $requirements = [];

        try {
            \Depotwarehouse\Toolbox\Verification::require_set($array, $requirements);
        } catch (\Depotwarehouse\Toolbox\Exceptions\ParameterRequiredException $exception) {
            $this->fail("Exception should not be thrown");
        }
    }

    public function testRequireSetEmptyArrayWithRequirements() {
        $required_parameter = "value";
        $array = [];
        $requirements = [ $required_parameter ];

        try {
            \Depotwarehouse\Toolbox\Verification::require_set($array, $requirements);
            $this->fail("Exception should be thrown");
        } catch (\Depotwarehouse\Toolbox\Exceptions\ParameterRequiredException $exception) {
            $this->assertEquals($required_parameter, $exception->getRequiredParameter());
        }
    }

    public function testRequireSetArrayWithPassingRequirements() {
        $required_parameter = "value";
        $array = [ 'value' => 'mock', 'object' => 'mock' ];
        $requirements = [ $required_parameter ];

        try {
            \Depotwarehouse\Toolbox\Verification::require_set($array, $requirements);
        } catch (\Depotwarehouse\Toolbox\Exceptions\ParameterRequiredException $exception) {
            $this->fail("Exception should not be thrown");
        }
    }

    public function testRequireSetArrayWithFailingRequirements() {
        $required_parameter = "value";
        $array = [ 'object' => 'mock' ];
        $requirements = [ $required_parameter ];

        try {
            \Depotwarehouse\Toolbox\Verification::require_set($array, $requirements);
            $this->fail("Exception should be thrown");
        } catch (\Depotwarehouse\Toolbox\Exceptions\ParameterRequiredException $exception) {
            $this->assertEquals($required_parameter, $exception->getRequiredParameter());
        }
    }

    public function testRequireSetWithNullRequiredAttribute() {
        $required_parameter = "value";
        $array = [ 'value' => null];
        $requirements = [ $required_parameter ];

        try {
            \Depotwarehouse\Toolbox\Verification::require_set($array, $requirements);
            $this->fail("Exception should be thrown");
        } catch (\Depotwarehouse\Toolbox\Exceptions\ParameterRequiredException $exception) {
            $this->assertEquals($required_parameter, $exception->getRequiredParameter());
        }
    }

    public function testRequireSetWithEmptyStringRequiredAttribute() {
        $required_parameter = "value";
        $array = [ 'value' => "" ];
        $requirements = [ $required_parameter ];

        try {
            \Depotwarehouse\Toolbox\Verification::require_set($array, $requirements);
            $this->fail("Exception should be thrown");
        } catch (\Depotwarehouse\Toolbox\Exceptions\ParameterRequiredException $exception) {
            $this->assertEquals($required_parameter, $exception->getRequiredParameter());
        }
    }

    public function testArrayFilterStartsWith() {
        $array = [ "value" => "mock", "object" => "mock", "valerie" => "mock" ];
        $filter = "val";

        $result = \Depotwarehouse\Toolbox\Verification::array_filter_starts_with($array, $filter);
        $this->assertArrayHasKey("value", $result);
        $this->assertArrayHasKey("valerie", $result);
        $this->assertArrayNotHasKey("object", $result);
    }

    public function testArrayFilterStartsWithPatternNotFound() {
        $array = [ "value" => "mock", "object" => "mock", "valerie" => "mock" ];
        $filter = "fred";

        $result = \Depotwarehouse\Toolbox\Verification::array_filter_starts_with($array, $filter);
        $this->assertEmpty($result);
    }

    public function testArrayFilterNull() {
        $array = [ "value" => "mock", "object" => null, "valerie" => null ];

        $result = \Depotwarehouse\Toolbox\Verification::array_filter_null($array);
        $this->assertArrayHasKey("value", $result);
        $this->assertArrayNotHasKey("object", $result);
        $this->assertArrayNotHasKey("valerie", $result);
    }

    public function testArrayFilterNullEmptyString() {
        $array = [ "value" => null, "object" => "" ];
        $result = \Depotwarehouse\Toolbox\Verification::array_filter_null($array);
        $this->assertArrayHasKey("object", $result);
        $this->assertArrayNotHasKey("value", $result);
    }

    public function testGetOpValuePairWithoutOperation() {
        $string = "9";

        try {
            \Depotwarehouse\Toolbox\Verification::getOpValuePair($string);
            $this->fail("Exception should be thrown");
        } catch (\Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException $exception) {
            $this->assertStringEndsWith($string, $exception->getMessage());
        }
    }

    public function testGetOpValuePairWithoutValue() {
        $string = "=";
        try {
            \Depotwarehouse\Toolbox\Verification::getOpValuePair($string);
            $this->fail("Exception should be thrown");
        } catch (\Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException $exception) {
            $this->assertStringEndsWith($string, $exception->getMessage());
        }
    }

    public function testGetOpValuePairValidOperations() {
        $operations = [ "<=", ">=", "<", ">", "=" ];
        foreach ($operations as $operation) {
            $pair = \Depotwarehouse\Toolbox\Verification::getOpValuePair($operation . "9");
            $this->assertArrayHasKey("op", $pair);
            $this->assertArrayHasKey("value", $pair);
            $this->assertEquals(9, $pair['value']);
            $this->assertEquals($operation, $pair['op']);
        }
    }

    public function testGetOpValuePairInvalidOperations() {
        $operations = [ "<>", "=>", "?<", "equals", "==", "" ];
        foreach ($operations as $operation) {
            try {
                $pair = \Depotwarehouse\Toolbox\Verification::getOpValuePair($operation . "9");
                $this->fail("Exception should be thrown on operation: {$operation}");
            } catch (\Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException $exception) {
                $this->assertStringEndsWith($operation . "9", $exception->getMessage());
            }
        }
    }

    public function testGetOpValuePairValidValueTypes() {
        $values = [ 9, "12", "hunter2", "t3st" ];
        foreach ($values as $value) {
            $pair = \Depotwarehouse\Toolbox\Verification::getOpValuePair('=' . $value);
            $this->assertArrayHasKey("op", $pair);
            $this->assertArrayHasKey("value", $pair);
            $this->assertEquals($value, $pair['value']);
            $this->assertEquals('=', $pair['op']);
        }
    }

}
 