<?php

class OperationsTest extends PHPUnit_Framework_TestCase {


    public function testGetOpValuePairWithoutOperation() {
        $string = "9";

        $pair = \Depotwarehouse\Toolbox\Operations\Operations::getOpValuePair($string);
        $this->assertEquals(9, $pair['value']);
        $this->assertEquals("=", $pair['op']);
    }

    /**
     * @expectedException \Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException
     * @expectedExceptionMessage String must end with [A-Za-z0-9], given: =
     */
    public function testGetOpValuePairWithoutValue() {
        $string = "=";
        \Depotwarehouse\Toolbox\Operations\Operations::getOpValuePair($string);
    }

    public function testGetOpValuePairValidOperations() {
        $operations = [ "<=", ">=", "<", ">", "=" ];
        foreach ($operations as $operation) {
            $pair = \Depotwarehouse\Toolbox\Operations\Operations::getOpValuePair($operation . "9");
            $this->assertArrayHasKey("op", $pair);
            $this->assertArrayHasKey("value", $pair);
            $this->assertEquals(9, $pair['value']);
            $this->assertEquals($operation, $pair['op']);
        }
    }

    public function testGetOpValuePairInvalidOperations() {
        $operations = [ "<>", "=>", "?<", "==", "" ];
        foreach ($operations as $operation) {
            $pair = \Depotwarehouse\Toolbox\Operations\Operations::getOpValuePair($operation . "9");
            $this->assertEquals("=", $pair['op']);
            $this->assertEquals(9, $pair['value']);
        }
    }

    public function testGetOpValuePairValidValueTypes() {
        $values = [ 9, "12", "hunter2", "t3st" ];
        foreach ($values as $value) {
            $pair = \Depotwarehouse\Toolbox\Operations\Operations::getOpValuePair('=' . $value);
            $this->assertArrayHasKey("op", $pair);
            $this->assertArrayHasKey("value", $pair);
            $this->assertEquals($value, $pair['value']);
            $this->assertEquals('=', $pair['op']);
        }
    }

    public function testGetOperationsFromArrayOfFilters() {
        // Test with an empty array
        $array = [];
        $operations = \Depotwarehouse\Toolbox\Operations\Operations::getOperationsFromArrayOfFilters($array);
        $this->assertEmpty($operations);

        // test with multiple operations
        $array = [ 'some_obj:mock_key' => '<9', 'other_key' => '=farts'];
        $operations = \Depotwarehouse\Toolbox\Operations\Operations::getOperationsFromArrayOfFilters($array);
        $this->assertEquals(2, count($operations));
        $this->assertAttributeEquals([ 'some_obj'], 'include_path', $operations[0]);
        $this->assertAttributeEquals('mock_key', 'key', $operations[0]);

        $this->assertAttributeEquals([], 'include_path', $operations[1]);
        $this->assertAttributeEquals('other_key', 'key', $operations[1]);
    }

}
 