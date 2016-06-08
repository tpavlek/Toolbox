<?php

namespace Depotwarehouse\Toolbox\Tests\Collections;


use Depotwarehouse\Toolbox\FrameworkIntegration\Laravel\ToolboxServiceProvider;
use Illuminate\Support\Collection;

class HeadTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function it_applies_a_function_to_head()
    {
        ToolboxServiceProvider::registerCollectionMacros();

        $result = collect([ "a", "b", "c" ])
            ->head(function($letter) {
                return strtoupper($letter);
            });

        $this->assertEquals([ "A", "b", "c" ], $result->all());
    }

    /**
     * @test
     */
    public function it_applies_optional_second_function_to_tail()
    {

        $result = collect([ "a", "b", "c" ])
            ->head(function ($firstLetter) {
                return strtoupper($firstLetter);
            }, function ($tailLetter) {
                return ord($tailLetter);
            });

        $this->assertEquals([ "A", 98, 99 ], $result->all());
    }

    /**
     * @test
     */
    public function it_works_on_a_single_item_collection()
    {
        $result = collect([ "a" ])
            ->head(function ($firstLetter) {
                return strtoupper($firstLetter);
            }, function ($tailLetter) {
                return ord($tailLetter);
            });

        $this->assertEquals([ "A" ], $result->all());
    }

}
