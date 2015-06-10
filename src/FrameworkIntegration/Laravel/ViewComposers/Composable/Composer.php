<?php

namespace Depotwarehouse\Toolbox\FrameworkIntegration\Laravel\ViewComposers\Composable;

use Illuminate\Contracts\View\View;

/**
 * Class Composer
 *
 * This class is meant to be overridden by a View Composer in a project that you want to be "Composable" via traits.
 *
 * Functionally, what this means is if you have many view composers with similar functionality (eg. they provide a list of
 * users to the view) you can extract the logic of providing a list of users into a trait, and then add the trait to your
 * view composer.
 *
 * The composable traits take the format of containing protected methods that begin with the word "compose" and take a View Contract as
 * an argument. For example, a trait might have the method signature
 *
 * ```
 * protected function composeUserList(\Illuminate\Contracts\View\View $view);
 * ```
 *
 * If your particular ViewComposer would like to add any other individual and arbitrary data to the view after all the
 * composition is complete, it can be simply added to the hook method "additionalComposition".
 *
 * @package Depotwarehouse\Toolbox\FrameworkIntegration\Laravel\ViewComposers\Composable
 */
abstract class Composer extends \Depotwarehouse\Toolbox\FrameworkIntegration\Laravel\ViewComposers\Composer
{

    /**
     * Handles the content of the view and composes any required data.
     *
     * @param View $view
     * @return mixed
     */
    public function compose(View $view)
    {
        $self = new \ReflectionClass($this);
        foreach ($self->getMethods(\ReflectionMethod::IS_PROTECTED) as $method) {
            if ($this->isMethodAViewComposeMethod($method)) {
                $this->{$method->name}($view);
            }
        }

        $this->additionalComposition($view);
    }

    /**
     * Checks whether the given method is a method that qualifies as a "View Compose" method.
     *
     * The requirements to be a View Compose method are:
     * - starts with "compose"
     * - Has only a single argument, typehinted as \Illuminate\Contracts\View\View
     *
     * @param \ReflectionMethod $method
     * @return bool
     */
    private function isMethodAViewComposeMethod(\ReflectionMethod $method)
    {
        // All methods will start with "compose"
        if (!\Depotwarehouse\Toolbox\Strings\starts_with($method->getName(), "compose")) {
            return false;
        }

        // Each method will only take a single argument
        if ($method->getNumberOfParameters() > 1) {
            return false;
        }

        $parameter = $method->getParameters()[0];

        // The single argument is typehinted as a View contract.
        if ($parameter->getClass()->name != View::class) {
            return false;
        }

        return true;
    }

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
    abstract function additionalComposition(View $view);

}
