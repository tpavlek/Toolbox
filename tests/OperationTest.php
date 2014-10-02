<?php

class OperationTest extends PHPUnit_Framework_TestCase {

    protected $operation = "=";

    protected $value = "mock_value";

    public function testConstructor() {
        $path = "mock_key";

        // A plain path should yield an empty include path with the path set as the key
        $operation_obj = new \Depotwarehouse\Toolbox\Operations\Operation($path, $this->operation, $this->value);
        $this->assertAttributeEquals($path, 'key', $operation_obj);
        $this->assertAttributeEquals($this->operation, 'operation', $operation_obj);
        $this->assertAttributeEquals($this->value, 'value', $operation_obj);
        $this->assertAttributeEquals([], 'include_path', $operation_obj);

        // A path with multiple colons should yield an array in the order they were declared.
        $path = "mock_obj:mock_sub_obj:mock_key";
        $operation_obj = new \Depotwarehouse\Toolbox\Operations\Operation($path, $this->operation, $this->value);
        $this->assertAttributeEquals('mock_key', 'key', $operation_obj);
        $this->assertAttributeEquals($this->operation, 'operation', $operation_obj);
        $this->assertAttributeEquals($this->value, 'value', $operation_obj);
        $this->assertAttributeEquals([ 'mock_obj', 'mock_sub_obj' ], 'include_path', $operation_obj);
    }

    public function testPullInclude() {
        $path = "mock_obj:mock_sub_obj:mock_key";
        $operation_obj = new \Depotwarehouse\Toolbox\Operations\Operation($path, $this->operation, $this->value);
        $pull = $operation_obj->pullInclude();
        $this->assertEquals('mock_obj', $pull);
        $this->assertAttributeEquals([ 'mock_sub_obj' ], 'include_path', $operation_obj);

        $pull = $operation_obj->pullInclude();

        $this->assertEquals('mock_sub_obj', $pull);
        $this->assertAttributeEquals([ ], 'include_path', $operation_obj);

        try {
            $pull = $operation_obj->pullInclude();
            $this->fail("Exception should be thrown");
        } catch (\Depotwarehouse\Toolbox\Exceptions\ArrayEmptyException $exception) {
            $this->assertEquals("Could not get next include - include path is empty", $exception->getMessage());
        }
    }

    public function testHasInclude() {
        $path = "mock_obj:mock_key";
        $operation_obj = new \Depotwarehouse\Toolbox\Operations\Operation($path, $this->operation, $this->value);

        $this->assertTrue($operation_obj->hasIncludes());

        $operation_obj->pullInclude();

        $this->assertFalse($operation_obj->hasIncludes());
    }

}
 