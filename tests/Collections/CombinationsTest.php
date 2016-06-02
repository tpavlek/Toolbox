<?php

namespace Depotwarehouse\Toolbox\Tests\Collections;

use Depotwarehouse\Toolbox\FrameworkIntegration\Laravel\ToolboxServiceProvider;
use Illuminate\Support\Collection;

class CombinationsTest extends \PHPUnit_Framework_TestCase
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
}
