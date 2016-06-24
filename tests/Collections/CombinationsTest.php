<?php

namespace Depotwarehouse\Toolbox\Tests\Collections;

use Depotwarehouse\Toolbox\FrameworkIntegration\Laravel\ToolboxServiceProvider;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class CombinationsTest extends TestCase
{

    /**
     * @test
     */
    public function it_can_combine_two_collections()
    {
        ToolboxServiceProvider::registerCollectionMacros();

        $result = (new Collection([ "a", "b" ]))
            ->combinations(new Collection([ 1, 2, 3 ]));

        $this->assertEquals([ ["a", 1], ["a", 2], ["a", 3], ["b", 1], ["b", 2], ["b", 3] ], $result->all());
    }

    /**
     * @test
     */
    public function it_can_combine_two_collections_with_keys()
    {
        ToolboxServiceProvider::registerCollectionMacros();

        $result = (new Collection([ "a", "b" ]))
            ->combinations(new Collection([ 1, 2, 3 ]), [ 'letter', 'number' ]);

        $this->assertEquals([
            ["letter" => "a", "number" => 1],
            ["letter" => "a", "number" => 2],
            ["letter" => "a", "number" => 3],
            ["letter" => "b", "number" => 1],
            ["letter" => "b", "number" => 2],
            ["letter" => "b", "number" => 3]
        ], $result->all());
    }
}
