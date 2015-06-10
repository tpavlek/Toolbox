<?php

namespace Depotwarehouse\Toolbox\Tests\FrameworkIntegration\Laravel\ViewComposers\Composable;

use Illuminate\Contracts\View\View;
use Mockery as m;

class ComposerTest extends \PHPUnit_Framework_TestCase {

    public function setUp()
    {
        parent::setUp();
    }
    
    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function test_it_calls_all_matching_compose_methods()
    {
        $composer = new StubComposer();

        $mockView = m::mock(View::class);
        $mockView->shouldReceive('with')->withArgs([ 'composeUserList', null ])->once();
        $mockView->shouldReceive('with')->withArgs([ 'compose_snake_case', null ])->once();

        $mockView->shouldNotReceive('with')->withArgs([ 'composeMultipleArguments', null ]);
        $mockView->shouldNotReceive('with')->withArgs([ 'composeAdminList', null ]);

        $composer->compose($mockView);
    }

}
