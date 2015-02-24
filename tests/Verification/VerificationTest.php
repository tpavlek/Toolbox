<?php

namespace Depotwarehouse\Toolbox\Tests\Verification;

use Depotwarehouse\Toolbox\Verification;
use PHPUnit_Framework_TestCase;

class VerificationTest extends PHPUnit_Framework_TestCase
{

    public function test_it_passes_with_empty_array_and_requirements()
    {
        $array = [ ];
        $requirements = [ ];

        Verification\require_set($array, $requirements);
    }

    /**
     * If there are requirements, then an empty values array should fail.
     */
    public function test_it_requires_an_empty_array_to_have_requirements()
    {
        $required_parameter = "value";
        $array = [ ];
        $requirements = [ $required_parameter ];

        try {
            Verification\require_set($array, $requirements);
            $this->fail("Exception should be thrown");
        } catch (Verification\ParameterRequiredException $exception) {
            $this->assertEquals($required_parameter, $exception->getRequiredParameter());
        }
    }

    public function test_it_passes_when_all_requirements_present()
    {
        $required_parameter = "value";
        $array = [ 'value' => 'mock', 'object' => 'mock' ];
        $requirements = [ $required_parameter ];

        Verification\require_set($array, $requirements);
    }

    /**
     * A requirement must be present in the values array to pass.
     *
     * @expectedException \Depotwarehouse\Toolbox\Verification\ParameterRequiredException
     * @expectedExceptionMessage A piece of data was not properly passed. Check the parameter: value
     */
    public function test_it_fails_when_a_requirement_is_not_present()
    {
        $required_parameter = "value";
        $array = [ 'object' => 'mock' ];
        $requirements = [ $required_parameter ];

        Verification\require_set($array, $requirements);
    }

    /**
     * A null value for a required key will not pass.
     *
     * @expectedException \Depotwarehouse\Toolbox\Verification\ParameterRequiredException
     * @expectedExceptionMessage A piece of data was not properly passed. Check the parameter: value
     */
    public function test_null_values_do_not_pass_requirements()
    {
        $required_parameter = "value";
        $array = [ 'value' => null ];
        $requirements = [ $required_parameter ];

        Verification\require_set($array, $requirements);
    }

    /**
     * An empty string value for a required key will not pass.
     *
     * @expectedException \Depotwarehouse\Toolbox\Verification\ParameterRequiredException
     * @expectedExceptionMessage A piece of data was not properly passed. Check the parameter: value
     */
    public function test_empty_string_values_do_not_pass_requirements()
    {
        $required_parameter = "value";
        $array = [ 'value' => "" ];
        $requirements = [ $required_parameter ];

        Verification\require_set($array, $requirements);
    }

    /**
     * Only the values starting with the filter will be returned.
     */
    public function test_array_filter_starts_with()
    {
        $array = [ "value" => "mock", "object" => "mock", "valerie" => "mock" ];
        $filter = "val";

        $result = Verification\array_filter_starts_with($array, $filter);
        $this->assertArrayHasKey("value", $result);
        $this->assertArrayHasKey("valerie", $result);
        $this->assertArrayNotHasKey("object", $result);
    }

    /**
     * If none of the values start with the filter, an empty array is returned.
     */
    public function test_array_filter_starts_with_filter_not_found()
    {
        $array = [ "value" => "mock", "object" => "mock", "valerie" => "mock" ];
        $filter = "fred";

        $result = Verification\array_filter_starts_with($array, $filter);
        $this->assertEmpty($result);
    }

    /**
     * No null values should be present after filtering null.
     */
    public function test_array_filter_null_removes_null_objects()
    {
        $array = [ "value" => "mock", "object" => null, "valerie" => null ];

        $result = Verification\array_filter_null($array);
        $this->assertArrayHasKey("value", $result);
        $this->assertArrayNotHasKey("object", $result);
        $this->assertArrayNotHasKey("valerie", $result);
    }

    /**
     * An empty string value is not null, and should not be filtered.
     */
    public function test_array_filter_null_keeps_empty_string_values()
    {
        $array = [ "value" => null, "object" => "" ];
        $result = Verification\array_filter_null($array);
        $this->assertArrayHasKey("object", $result);
        $this->assertArrayNotHasKey("value", $result);
    }

}
