<?php

class ConfigurationTest extends PHPUnit_Framework_TestCase {

    public function testConstructor() {
        $configuration = new \Depotwarehouse\Toolbox\DataManagement\Configuration();
        $this->assertAttributeInternalType("array", "include", $configuration);
        $this->assertAttributeInternalType("array", "pagination", $configuration);
    }

    public function testSetInclude() {
        $configuration = new \Depotwarehouse\Toolbox\DataManagement\Configuration();
        $configuration->setInclude(3);
        $include = $this->getObjectAttribute($configuration, "include");

        $this->assertEquals(3, $include["max_depth"]);
    }

    public function testSetPagination() {
        $configuration = new \Depotwarehouse\Toolbox\DataManagement\Configuration();
        $configuration->setPagination(3, "my_page");
        $pagination = $this->getObjectAttribute($configuration, "pagination");

        $this->assertEquals(3, $pagination["per_page"]);
        $this->assertEquals("my_page", $pagination["page_name"]);
    }

}
 