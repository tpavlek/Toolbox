<?php

class StringsTest extends PHPUnit_Framework_TestCase {

    public function testParseCommaSeparatedFromSingleID() {
        $id = "71";
        $parse_id = \Depotwarehouse\Toolbox\Strings::parseCommaSeparatedIDs($id);

        $this->assertEquals($id, $parse_id);
    }

    public function testParseCommaSeparatedFromMultipleIDs() {
        $id = "94,63,27";
        // Order is important
        $result_expected = [ 27, 63, 94 ];
        $parse_id = \Depotwarehouse\Toolbox\Strings::parseCommaSeparatedIDs($id);

        $this->assertInternalType("array", $parse_id);
        $this->assertEquals($result_expected, $parse_id);
    }

    public function testGenerateRandomString() {
        $string = \Depotwarehouse\Toolbox\Strings::generateRandomString();

        $this->assertInternalType("string", $string);
        $this->assertEquals(40, strlen($string));

        $string = \Depotwarehouse\Toolbox\Strings::generateRandomString(20);

        $this->assertInternalType("string", $string);
        $this->assertEquals(20, strlen($string));
    }

}
 