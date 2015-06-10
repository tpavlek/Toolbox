<?php

namespace Depotwarehouse\Toolbox\Tests\FrameworkIntegration\Laravel\ViewComposers\Composable;

use Depotwarehouse\Toolbox\FrameworkIntegration\Laravel\ViewComposers\Composable\Composer;
use Illuminate\Contracts\View\View;

class StubComposer extends Composer
{

    use StubUserComposeTrait;

    /**
     * Add any additional composition you would like to the View Composer.
     *
     * If you don't wish to add any additional composition, simply leave the body of this method empty.
     *
     * When this function is called, the parent class will have already set all the view variables based on the traits
     * that are present.
     *
     * @param View $view
     * @return null
     */
    function additionalComposition(View $view)
    {
        // TODO: Implement additionalComposition() method.
    }
}
