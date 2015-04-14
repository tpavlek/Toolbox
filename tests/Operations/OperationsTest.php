<?php

namespace Depotwarehouse\Toolbox\Tests\Operations;


use function Depotwarehouse\Toolbox\Operations\get_op_value_pair;

class OperationsTest extends \PHPUnit_Framework_TestCase {

    public function setUp()
    {
        parent::setUp();
    }
    
    public function tearDown()
    {
        parent::tearDown();
    }

    public function testGetOpValuePairWithoutOperation() {
        $string = "9";

        $pair = get_op_value_pair($string);
        $this->assertEquals(9, $pair['value']);
        $this->assertEquals("=", $pair['op']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage String must end with [A-Za-z0-9.'], given: =
     */
    public function testGetOpValuePairWithoutValue() {
        $string = "=";
        get_op_value_pair($string);
    }

    public function testGetOpValuePairValidOperations() {
        $operations = [ "<=", ">=", "<", ">", "=" ];
        foreach ($operations as $operation) {
            $pair = get_op_value_pair($operation . "9");
            $this->assertArrayHasKey("op", $pair);
            $this->assertArrayHasKey("value", $pair);
            $this->assertEquals(9, $pair['value']);
            $this->assertEquals($operation, $pair['op']);
        }
    }

    public function testGetOpValuePairInvalidOperations() {
        $operations = [ "<>", "=>", "?<", "==", "" ];
        foreach ($operations as $operation) {
            $pair = get_op_value_pair($operation . "9");
            $this->assertEquals("=", $pair['op']);
            $this->assertEquals(9, $pair['value']);
        }
    }

    public function testGetOpValuePairValidValueTypes() {
        $values = [ 9, "12", "hunter2", "t3st", "25.4", "O'Hare" ];
        foreach ($values as $value) {
            $pair = get_op_value_pair('=' . $value);
            $this->assertArrayHasKey("op", $pair);
            $this->assertArrayHasKey("value", $pair);
            $this->assertEquals($value, $pair['value']);
            $this->assertEquals('=', $pair['op']);
        }
    }

    public function testGetOperationsFromArrayOfFilters() {
        // Test with an empty array
        $array = [];
        $operations = \Depotwarehouse\Toolbox\Operations\get_operations_from_array_of_filters($array);
        $this->assertEmpty($operations);

        // test with multiple operations
        $array = [ 'some_obj:mock_key' => '<9', 'other_key' => '=farts'];
        $operations = \Depotwarehouse\Toolbox\Operations\get_operations_from_array_of_filters($array);
        $this->assertEquals(2, count($operations));
        $this->assertAttributeEquals([ 'some_obj'], 'include_path', $operations[0]);
        $this->assertAttributeEquals('mock_key', 'key', $operations[0]);

        $this->assertAttributeEquals([], 'include_path', $operations[1]);
        $this->assertAttributeEquals('other_key', 'key', $operations[1]);
    }

}
