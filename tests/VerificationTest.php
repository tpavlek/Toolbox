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

}
 