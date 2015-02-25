<?php

namespace Depotwarehouse\Toolbox\Tests\Strings;

use Depotwarehouse\Toolbox\Strings;
use PHPUnit_Framework_TestCase;

class StringsTest extends PHPUnit_Framework_TestCase
{

    public function test_it_generates_a_random_string_of_length()
    {
        $string = Strings\generate_random_string(40);

        $this->assertInternalType("string", $string);
        $this->assertEquals(40, strlen($string));

        $string = Strings\generate_random_string(20);

        $this->assertInternalType("string", $string);
        $this->assertEquals(20, strlen($string));
    }

    public function test_it_identifies_strings_starting_with_needle()
    {
        $this->assertTrue(Strings\starts_with("mock", "mo"));
        $this->assertFalse(Strings\starts_with("mock", "bu"));
    }

    public function test_it_identifies_strings_ending_with_needle()
    {
        $this->assertTrue(Strings\ends_with("mock", "ck"));
        $this->assertFalse(Strings\ends_with("mock", "bu"));
    }

}
