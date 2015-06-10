<?php

namespace Depotwarehouse\Toolbox\Tests\FrameworkIntegration\Laravel\ViewComposers\Composable;

use Illuminate\Contracts\View\View;

trait StubUserComposeTrait
{

    protected function composeUserList(View $view)
    {
        $view->with('composeUserList', null);
    }

    protected function compose_snake_case(View $view)
    {
        $view->with('compose_snake_case', null);
    }

    protected function composeMultipleArguments(View $view, $otherArg = null)
    {
        $view->with('composeMultipleArguments', null);
    }

    public function composeAdminList(View $view)
    {
        $view->with('composeAdminList', null);
    }
}
