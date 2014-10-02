<?php

class OperationsTest extends PHPUnit_Framework_TestCase {

    public function testGetOpValuePairWithoutOperation() {
        $string = "9";

        try {
            \Depotwarehouse\Toolbox\Operations\Operations::getOpValuePair($string);
            $this->fail("Exception should be thrown");
        } catch (\Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException $exception) {
            $this->assertStringEndsWith($string, $exception->getMessage());
        }
    }

    public function testGetOpValuePairWithoutValue() {
        $string = "=";
        try {
            \Depotwarehouse\Toolbox\Operations\Operations::getOpValuePair($string);
            $this->fail("Exception should be thrown");
        } catch (\Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException $exception) {
            $this->assertStringEndsWith($string, $exception->getMessage());
        }
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
        $operations = [ "<>", "=>", "?<", "equals", "==", "" ];
        foreach ($operations as $operation) {
            try {
                $pair = \Depotwarehouse\Toolbox\Operations\Operations::getOpValuePair($operation . "9");
                $this->fail("Exception should be thrown on operation: {$operation}");
            } catch (\Depotwarehouse\Toolbox\Exceptions\InvalidArgumentException $exception) {
                $this->assertStringEndsWith($operation . "9", $exception->getMessage());
            }
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
        $array = [ 'some_obj:mock_key' => '<9', 'other_key' => '=farts' ];
        $operations = \Depotwarehouse\Toolbox\Operations\Operations::getOperationsFromArrayOfFilters($array);
        $this->assertEquals(2, count($operations));
        $this->assertAttributeEquals([ 'some_obj'], 'include_path', $operations[0]);
        $this->assertAttributeEquals('mock_key', 'key', $operations[0]);

        $this->assertAttributeEquals([], 'include_path', $operations[1]);
        $this->assertAttributeEquals('other_key', 'key', $operations[1]);
    }

}
 