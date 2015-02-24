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

}
