<?php

namespace Depotwarehouse\Toolbox\FrameworkIntegration\Laravel\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Contracts\Support\MessageBag;

class ErrorPartialComposer extends Composer
{

    /**
     * Handles the content of the view and composes any required data.
     *
     * @param View $view
     * @return mixed
     */
    function compose(View $view)
    {
        /** @var MessageBag $errors */
        $errors = $view['errors'];
        $success = ($errors->has('success')) ? $errors->get('success') : [ ];
        $warn = ($errors->has('warn')) ? $errors->get('warn') : [ ];
        $errors_arr = ($errors->has('errors')) ? $errors->get('errors') : [ ];
        // if there's anything else with a different key in the messagebag, we'll treat it as an error.
        $arr = $errors->all();
        unset($arr['success']);
        unset($arr['warn']);
        unset($arr['errors']);
        $errors_arr = array_merge($errors_arr, array_flatten($arr));

        $view->with('success', $success);
        $view->with('warn', $warn);
        $view->with('errors_arr', $errors_arr);
    }
}
